<?php

return [
    'connections' => [
        'mysql' => [
            'dbname'   => env('DB_DATABASE'),
            'user'     => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'host'     => env('DB_HOST'),
            'driver'   => env('DB_DRIVER')
        ],
    ]
];
