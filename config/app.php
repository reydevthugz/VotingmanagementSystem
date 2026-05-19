<?php
return [
    'name' => env('APP_NAME', 'Voting Management System'),
    'env' => env('APP_ENV', 'production'),
    'debug' => filter_var((string) env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN),
    'url' => env('APP_URL', 'http://localhost/voting-management-system/'),
];
