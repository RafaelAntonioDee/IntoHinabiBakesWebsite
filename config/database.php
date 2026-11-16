<?php

return [
    'default' => env('DB_CONNECTION', 'supabase'),

    'connections' => [
        'supabase' => [
            'driver'   => 'pgsql',
            'host'     => env('SUPABASE_DB_HOST', 'db.your-project-ref.supabase.co'),
            'port'     => env('SUPABASE_DB_PORT', '5432'),
            'database' => env('SUPABASE_DB_NAME', 'postgres'),
            'username' => env('SUPABASE_DB_USER', 'postgres'),
            'password' => env('SUPABASE_DB_PASSWORD', ''),
            'sslmode'  => 'require',
            'options'  => [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ],
        ],

        'mysql' => [
            'driver'   => 'mysql',
            'host'     => env('MYSQL_HOST', '127.0.0.1'),
            'port'     => env('MYSQL_PORT', '3306'),
            'database' => env('MYSQL_DATABASE', 'sweet_cookie_db'),
            'username' => env('MYSQL_USERNAME', 'root'),
            'password' => env('MYSQL_PASSWORD', ''),
            'charset'  => 'utf8mb4',
            'options'  => [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ],
        ],
    ],
];
