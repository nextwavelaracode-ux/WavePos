<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FactusService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected string $username;
    protected string $password;

    public function __construct()
    {
        $this->baseUrl = config('factus.url');
        $this->clientId = config('factus.client_id');
        $this->clientSecret = config('factus.client_secret');
        $this->username = config('factus.username');
        $this->password = config('factus.password');
    }

    /**
     * Get or refresh the access token from Factus.
     * Caches the token to avoid unnecessary requests (Token expires in 60 mins approx).
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
     * Send an invoice to Factus to be validated by DIAN.
     *
     * @param array $invoiceData The generated JSON payload following Factus structure.
     * @return array
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
     * Retrieve an invoice by its number.
     *
     * @param string $number Invoice Number (e.g., SETT1)
     * @return array
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
