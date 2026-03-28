<?php

return [
    'url'           => env('FACTUS_URL', 'https://api-sandbox.factus.com.co'),
    'client_id'     => env('FACTUS_CLIENT_ID', ''),
    'client_secret' => env('FACTUS_CLIENT_SECRET', ''),
    'username'      => env('FACTUS_USERNAME', ''),
    'password'      => env('FACTUS_PASSWORD', ''),

    // Configuración DIAN - Ajusta según tu resolución en Factus
    'numbering_range_id'     => env('FACTUS_NUMBERING_RANGE_ID', 8),
    'default_municipality_id' => env('FACTUS_MUNICIPALITY_ID', 980),

    // Datos del establecimiento (punto de venta) - Obligatorio en la API
    'establishment' => [
        'name'         => env('FACTUS_ESTAB_NAME', 'Punto de Venta Principal'),
        'address'      => env('FACTUS_ESTAB_ADDRESS', 'Calle 1 # 2-3'),
        'phone_number' => env('FACTUS_ESTAB_PHONE', '3000000000'),
        'email'        => env('FACTUS_ESTAB_EMAIL', 'sistema@empresa.com'),
        'municipality_id' => env('FACTUS_MUNICIPALITY_ID', 980),
    ],
];
