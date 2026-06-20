<?php

return [

    'listings' => [
        'cache_store' => env('MARKETPLACE_LISTINGS_CACHE_STORE', 'database'),
        'ttl_seconds' => (int) env('MARKETPLACE_LISTINGS_CACHE_TTL', 120),
    ],

    'uploads' => [
        'disk' => env('MARKETPLACE_UPLOAD_DISK', 'public'),
        'product_path' => 'products',
    ],

    'default_currency' => env('MARKETPLACE_DEFAULT_CURRENCY', 'EUR'),

];
