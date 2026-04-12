<?php

/**
 * Script de prueba (Probe) para validar la integración con la API de Factus / DIAN.
 * 
 * Uso desde línea de comandos:
 * php probe_factus.php
 */

if (php_sapi_name() !== 'cli') {
    echo "<pre>";
}

echo "========================================================\n";
echo "      PRUEBA DE CONEXIÓN Y LÓGICA FACTUS / DIAN\n";
echo "========================================================\n\n";

// Cargar el autoloader de Composer
require __DIR__ . '/vendor/autoload.php';

// Iniciar la aplicación Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Services\FactusService;
use Illuminate\Support\Facades\Log;

try {
    echo "[1/3] Instanciando FactusService...\n";
    $factusService = app(FactusService::class);
    echo "  > OK.\n\n";

    echo "[2/3] Solicitando Token OAuth2 a la API de Factus...\n";
    $token = $factusService->getToken();
    
    if (!$token) {
        echo "  [✗ ERROR] No se pudo obtener el token. Revisa tus credenciales en el archivo .env (FACTUS_USERNAME, FACTUS_PASSWORD, etc).\n";
        exit;
    }
    
    echo "  [✓ ÉXITO] Token válido obtenido: " . substr($token, 0, 30) . "...\n\n";

    echo "[3/3] Prueba de consulta de factura (Estado)\n";
    // Nota: Configura aquí un número de comprobante válido en sandbox para probar que la API responda.
    // Ej: "SETT-1" o el prefijo que estés utilizando.
    $numeroPrueba = "SETT-1"; 
    
    echo "  > Consultando factura de prueba con el número: {$numeroPrueba}...\n";
    $response = $factusService->showBill($numeroPrueba);
    
    if ($response['success']) {
        echo "  [✓ ÉXITO] La API respondió correctamente a la consulta.\n";
        echo "  - Estado de la factura: " . ($response['data']['data']['bill']['status'] ?? 'Desconocido') . "\n";
        echo "  - CUFE: " . ($response['data']['data']['bill']['cufe'] ?? 'N/A') . "\n";
    } else {
        echo "  [!] Nota: La consulta a {$numeroPrueba} falló. Esto es NORMAL si la factura {$numeroPrueba} no existe en tu entorno de pruebas de DIAN/Factus.\n";
        echo "  - Mensaje de la API: " . ($response['message'] ?? 'Hubo un error de consulta') . "\n";
    }

    echo "\n=> LA COMUNICACIÓN CON FACTUS ESTÁ OPERATIVA.\n\n";

} catch (\Exception $e) {
    echo "\n[EXCEPCIÓN CRÍTICA]\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "========================================================\n";
echo "                   FIN DE LA PRUEBA\n";
echo "========================================================\n";

if (php_sapi_name() !== 'cli') {
    echo "</pre>";
}
