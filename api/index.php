<?php

// Punto de entrada para Vercel (runtime PHP serverless)
// Este archivo actúa como el index.php de /public pero desde /api/

define('LARAVEL_START', microtime(true));

// Autoloader de Composer
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap de la aplicación Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Manejo de rutas para PHP serverless en Vercel
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = __FILE__;
$_SERVER['PHP_SELF'] = '/index.php';

// Forzar el path base correcto
if (isset($_SERVER['REQUEST_URI'])) {
    $uri = $_SERVER['REQUEST_URI'];
    // Remover el prefijo /api si Vercel lo agrega internamente
    if (str_starts_with($uri, '/api/index.php')) {
        $_SERVER['REQUEST_URI'] = '/' . ltrim(substr($uri, strlen('/api/index.php')), '/');
    }
}

// Crear y manejar el kernel HTTP
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

$response->send();

$kernel->terminate($request, $response);
