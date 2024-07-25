<?php

return [
    'default' => [
        'server' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', 3306),
        'database_name' => env('DB_DB_NAME', 'test'),
        'username' => env('DB_USER', 'root'),
        'password' => env('DB_PASSWORD', '127.0.0.1'),
        'charset' => 'utf8',
    ],
    'change_log' => [
        'server' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', 3306),
        'database_name' => env('DB_DB_NAME', 'test'),
        'username' => env('DB_USER', 'root'),
        'password' => env('DB_PASSWORD', '127.0.0.1'),
        'charset' => 'utf8',
    ],
];