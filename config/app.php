<?php

declare(strict_types=1);

return [
    'name'        => env('APP_NAME', 'Denosys Core'),
    'env'         => env('APP_ENV', 'production'),
    'url'         => env('APP_URL', 'http://localhost'),
    'key'         => env('APP_KEY'),
    'debug'       => (bool) env('APP_DEBUG', false),
    'vite_server' => 'http://localhost:5173',
    'email'       => [
        'support' => env('APP_SUPPORT_EMAIL'),
    ],
    'address'     => env('APP_ADDRESS'),
];
