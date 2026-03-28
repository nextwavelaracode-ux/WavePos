<?php

/**
 * FactusService — Capa de integración con la API REST de Factus
 *
 * Este servicio actúa como único punto de contacto entre WavePOS y la
 * plataforma Factus (habilitada por la DIAN para Facturación Electrónica
 * en Colombia). Encapsula toda la comunicación HTTP, manejo de tokens
 * OAuth2 y transformación de respuestas.
 *
 * Endpoints cubiertos:
 *  - POST /oauth/token            → Autenticación
 *  - POST /v1/bills/validate      → Crear y validar factura ante DIAN
 *  - GET  /v1/bills/show/{number} → Consultar estado de una factura
 *  - GET  /v1/bills/download-pdf/{number} → Descargar PDF (Base64)
 *  - POST /v1/bills/send-email/{number}   → Enviar factura por correo
 *
 * Configuración requerida en config/factus.php (variables .env):
 *  FACTUS_URL, FACTUS_CLIENT_ID, FACTUS_CLIENT_SECRET,
 *  FACTUS_USERNAME, FACTUS_PASSWORD
 *
 * @package App\Services
 * @author  WavePOS — NextWave
 * @version 1.0.0
 */

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FactusService
{
    /** @var string URL base de la API de Factus (sandbox o producción) */
    protected string $baseUrl;

    /** @var string Client ID de la aplicación OAuth2 registrada en Factus */
    protected string $clientId;

    /** @var string Client Secret de la aplicación OAuth2 registrada en Factus */
    protected string $clientSecret;

    /** @var string Email/usuario de la cuenta Factus */
    protected string $username;

    /** @var string Contraseña de la cuenta Factus */
    protected string $password;

    /**
     * Inicializa el servicio leyendo las credenciales desde config/factus.php.
     * Todos los valores provienen de variables de entorno (.env).
     */
    public function __construct()
    {
        $this->baseUrl      = config('factus.url');
        $this->clientId     = config('factus.client_id');
        $this->clientSecret = config('factus.client_secret');
        $this->username     = config('factus.username');
        $this->password     = config('factus.password');
    }

    /**
     * Obtiene o refresca el Access Token OAuth2 de Factus.
     *
     * Implementa caché inteligente: el token se guarda por 55 minutos
     * (el token real dura 60 min) para evitar condiciones de carrera
     * con tokens a punto de expirar.
     *
     * Flujo:
     *  1. Buscar en caché → si existe, retornarlo directamente.
     *  2. Si no existe, POST a /oauth/token con grant_type=password.
     *  3. Si exitoso, guardar en caché y retornar el token.
     *  4. Si falla, registrar en Log y retornar null.
     *
     * @return string|null  Access token JWT o null si la autenticación falla.
     */
    public function getToken(): ?string
    {
        $cacheKey = 'factus_access_token';

        // Intentar obtener el token del cache primero
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::asForm()->post("{$this->baseUrl}/oauth/token", [
                'grant_type'    => 'password',
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'username'      => $this->username,
                'password'      => $this->password,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $token = $data['access_token'];
                // La API indica que expira en 3600 segundos (1 hr), guardamos por 55 min para asegurar
                Cache::put($cacheKey, $token, now()->addMinutes(55));
                return $token;
            }

            Log::error('Factus get_token Error: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('Factus get_token Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Envía una factura a Factus para ser validada ante la DIAN.
     *
     * Este es el método principal de la integración. Recibe el payload
     * completo conforme a la especificación oficial de la API de Factus
     * y retorna los datos de la factura validada (número, CUFE, QR).
     *
     * Endpoint: POST /v1/bills/validate
     *
     * El payload $invoiceData debe incluir:
     *  - document, numbering_range_id, reference_code (único por factura)
     *  - payment_form, payment_method_code
     *  - establishment (nombre, dirección, teléfono, email, municipality_id)
     *  - customer (identification, names, email, legal_organization_id, etc.)
     *  - items (code_reference, name, quantity, price, tax_rate, etc.)
     *
     * @param  array $invoiceData  Payload JSON según estructura de Factus API.
     * @return array{success: bool, data?: array, message?: string, errors?: array}
     */
    public function validateBill(array $invoiceData): array
    {
        $token = $this->getToken();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'No se pudo obtener el token de autorización de Factus.'
            ];
        }

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->post("{$this->baseUrl}/v1/bills/validate", $invoiceData);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data'    => $response->json()
                ];
            }

            Log::error('Factus validateBill Error: ' . $response->body());
            return [
                'success' => false,
                'message' => 'Error al validar la factura electrónica.',
                'errors'  => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('Factus validateBill Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Excepción al intentar comunicar con Factus.',
                'error'   => $e->getMessage()
            ];
        }
    }

    /**
     * Consulta el estado actualizado de una factura electrónica en Factus.
     *
     * Útil para sincronizar el estado local con la DIAN después de enviar
     * la factura (la DIAN puede tardar algunos segundos en procesar).
     * Se usa en el método show() del controlador para mostrar datos frescos.
     *
     * Endpoint: GET /v1/bills/show/{number}
     *
     * @param  string $number  Número de factura asignado por Factus (ej: "SETT-1").
     * @return array{success: bool, data?: array, message?: string, errors?: array}
     */
    public function showBill(string $number): array
    {
        $token = $this->getToken();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'No se pudo obtener el token de autorización de Factus.'
            ];
        }

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->get("{$this->baseUrl}/v1/bills/show/{$number}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data'    => $response->json()
                ];
            }

            Log::error('Factus showBill Error: ' . $response->body());
            return [
                'success' => false,
                'message' => 'Error al consultar la factura electrónica.',
                'errors'  => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('Factus showBill Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Excepción al intentar comunicar con Factus.',
                'error'   => $e->getMessage()
            ];
        }
    }
    /**
     * Solicita a Factus que envíe la factura electrónica por correo al cliente.
     *
     * Factus gestiona el envío directamente desde su plataforma, incluyendo
     * el PDF de representación gráfica y el XML del documento electrónico.
     *
     * Endpoint: POST /v1/bills/send-email/{number}
     *
     * Nota: En entornos sandbox, Factus puede rechazar algunos correos o
     * comportarse diferente a producción. Se recomienda usar correos
     * de prueba registrados en la plataforma Factus.
     *
     * @param  string $number  Número de la factura electrónica (ej: "SETT-1").
     * @param  string $email   Correo electrónico destino.
     * @return array{success: bool, message: string, errors?: array}
     */
    public function sendEmail(string $number, string $email): array
    {
        $token = $this->getToken();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'No se pudo obtener el token de autorización de Factus.'
            ];
        }

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->post("{$this->baseUrl}/v1/bills/send-email/{$number}", [
                    'email' => $email
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Correo enviado exitosamente a ' . $email
                ];
            }

            Log::error('Factus sendEmail Error: ' . $response->body());

            $errorData = $response->json();
            $detailedError = 'Error al conectarse a Factus.';
            if (isset($errorData['data']['errors']['email'][0])) {
                $detailedError = $errorData['data']['errors']['email'][0];
            } else if (isset($errorData['message'])) {
                $detailedError = $errorData['message'];
            }

            return [
                'success' => false,
                'message' => $detailedError,
                'errors'  => $errorData
            ];

        } catch (\Exception $e) {
            Log::error('Factus sendEmail Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Excepción al conectarse con Factus: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Descarga el PDF de representación gráfica de la factura desde Factus.
     *
     * Factus retorna el PDF codificado en Base64. El controlador lo decodifica
     * y lo envía al navegador como respuesta con Content-Type application/pdf.
     *
     * Endpoint: GET /v1/bills/download-pdf/{number}
     *
     * Estructura de respuesta exitosa:
     *  data.data.pdf_base_64_encoded → string (PDF en Base64)
     *  data.data.file_name           → string (nombre sugerido del archivo)
     *
     * @param  string $number  Número de la factura electrónica (ej: "SETT-1").
     * @return array{success: bool, data?: array, message?: string, error?: string}
     */
    public function downloadPdf(string $number): array
    {
        $token = $this->getToken();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'No se pudo obtener el token de autorización de Factus.'
            ];
        }

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->get("{$this->baseUrl}/v1/bills/download-pdf/{$number}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data'    => $response->json() // Factus suele devolver data => [ pdf_base_64_encoded => "...", file_name => "..." ]
                ];
            }

            Log::error('Factus downloadPdf Error: ' . $response->body());
            return [
                'success' => false,
                'message' => 'Error al descargar el PDF de la factura electrónica.',
                'errors'  => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('Factus downloadPdf Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Excepción al intentar comunicar con Factus.',
                'error'   => $e->getMessage()
            ];
        }
    }
}
