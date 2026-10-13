<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WooCommerce Store Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for connecting to the WooCommerce REST API.
    |
    */

    'store_url' => env('WOOCOMMERCE_STORE_URL', 'http://tienda.test'),
    'consumer_key' => env('WOOCOMMERCE_CONSUMER_KEY', ''),
    'consumer_secret' => env('WOOCOMMERCE_CONSUMER_SECRET', ''),
    'webhook_secret' => env('WOOCOMMERCE_WEBHOOK_SECRET', ''),
    'timeout' => env('WOOCOMMERCE_TIMEOUT', 15),
    'verify_ssl' => env('WOOCOMMERCE_VERIFY_SSL', false),
];
