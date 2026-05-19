<?php
return [
    'host' => env('DB_HOST', 'localhost'),
    'port' => (int) env('DB_PORT', 3306),
    'database' => env('DB_DATABASE', 'votingmanagementsystem'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
];
