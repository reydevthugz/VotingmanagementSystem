<?php
namespace App\Middleware;

class GuestMiddleware
{
    public function handle(): void
    {
        if (!empty($_SESSION['user'])) {
            $role = $_SESSION['user']['role'] ?? '';
            if ($role === 'student') {
                header('Location: ' . BASE_URL . 'student/dashboard');
                exit;
            }

            header('Location: ' . BASE_URL . 'dashboard');
            exit;
        }
    }
}
