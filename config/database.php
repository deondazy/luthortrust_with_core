<?php

declare(strict_types=1);

return [
    'default' => env('DB_CONNECTION', 'mysql'),

    'connections' => [

        'sqlite' => [
            'driver'   => 'pdo_sqlite',
            'user'     => env('DB_USER'),
            'password' => env('DB_PASSWORD'),
            'path'     => env('DB_DATABASE', 'database.sqlite'),
            'memory'   => env('DB_MEMORY', false),
        ],
        
        'mysql' => [
            'driver'   => 'pdo_mysql',
            'host'     => env('DB_HOST', 'localhost'),
            'port'     => env('DB_PORT', 3306),
            'dbname'   => env('DB_NAME'),
            'user'     => env('DB_USER'),
            'password' => env('DB_PASSWORD'),
            'charset'  => env('DB_CHARSET', 'utf8mb4'),
        ],

        'pgsql' => [
            'driver'   => 'pdo_pgsql',
            'host'     => env('DB_HOST', 'localhost'),
            'port'     => env('DB_PORT', 5432),
            'dbname'   => env('DB_NAME'),
            'user'     => env('DB_USER'),
            'password' => env('DB_PASSWORD'),
            'charset'  => env('DB_CHARSET', 'utf8'),
            'sslmode' => 'prefer',
        ],
    ],
];
