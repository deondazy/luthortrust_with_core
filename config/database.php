<?php

declare(strict_types=1);

return [

    'mysql' => [
        'driver'   => 'pdo_mysql',
        'host'     => env('DB_HOST', 'localhost'),
        'port'     => env('DB_PORT', 3306),
        'dbname'   => env('DB_NAME'),
        'user'     => env('DB_USER'),
        'password' => env('DB_PASSWORD'),
        'charset'  => env('DB_CHARSET', 'utf8mb4'),
    ],
];
