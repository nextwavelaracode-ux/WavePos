<?php

declare(strict_types=1);

/**
 * WavePos - Vercel Serverless Function Entry Point for Laravel
 *
 * Configurado profesionalmente para resolver las restricciones de solo lectura
 * de AWS Lambda / Vercel Serverless, gestionando /tmp de forma dinámica y
 * normalizando variables de entorno del servidor.
 */

// 1. Normalizar variables del servidor para evitar discrepancias en Symfony/Laravel Request
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF']    = '/index.php';

// 2. Garantizar que todos los directorios requeridos en /tmp existan
$storageDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/storage/app/public',
    '/tmp/bootstrap/cache',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

if (!file_exists('/tmp/database.sqlite')) {
    @touch('/tmp/database.sqlite');
}

// 3. Fallbacks de rutas para caché compilada y vistas en /tmp
if (!getenv('VIEW_COMPILED_PATH')) {
    putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
    $_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
}
if (!getenv('APP_CONFIG_CACHE')) {
    putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
    $_ENV['APP_CONFIG_CACHE'] = '/tmp/bootstrap/cache/config.php';
}
if (!getenv('APP_EVENTS_CACHE')) {
    putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');
    $_ENV['APP_EVENTS_CACHE'] = '/tmp/bootstrap/cache/events.php';
}
if (!getenv('APP_PACKAGES_CACHE')) {
    putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
    $_ENV['APP_PACKAGES_CACHE'] = '/tmp/bootstrap/cache/packages.php';
}
if (!getenv('APP_ROUTES_CACHE')) {
    putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php');
    $_ENV['APP_ROUTES_CACHE'] = '/tmp/bootstrap/cache/routes.php';
}
if (!getenv('APP_SERVICES_CACHE')) {
    putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');
    $_ENV['APP_SERVICES_CACHE'] = '/tmp/bootstrap/cache/services.php';
}

// 4. Cargar autoloader de Composer
require __DIR__ . '/../vendor/autoload.php';

// 5. Verificar modo de mantenimiento si existiese
if (file_exists('/tmp/storage/framework/maintenance.php')) {
    require '/tmp/storage/framework/maintenance.php';
} elseif (file_exists(__DIR__ . '/../storage/framework/maintenance.php')) {
    require __DIR__ . '/../storage/framework/maintenance.php';
}

// 6. Arrancar Laravel
/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 7. Redirigir el almacenamiento dinámico al directorio escribible /tmp/storage
$app->useStoragePath('/tmp/storage');

// 8. Capturar y despachar la petición HTTP
$app->handleRequest(\Illuminate\Http\Request::capture());
