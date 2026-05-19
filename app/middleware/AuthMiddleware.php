<?php
namespace App\Middleware;

class AuthMiddleware
{
    public function handle(): void
    {
        if (empty($_SESSION['user'])) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }

        $expectedAgent = $_SESSION['user']['agent'] ?? '';
        $expectedIp = $_SESSION['user']['ip'] ?? '';
        $currentAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $currentIp = $_SERVER['REMOTE_ADDR'] ?? '';

        if ($expectedAgent !== $currentAgent || $expectedIp !== $currentIp) {
            session_unset();
            session_destroy();
            header('Location: ' . BASE_URL . 'login');
            exit;
        }
    }
}
