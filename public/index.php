<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

$router = new App\Core\Router();
require_once base_path('routes/web.php');
$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
