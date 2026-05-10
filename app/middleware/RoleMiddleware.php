<?php
namespace App\Middleware;

class RoleMiddleware
{
    public function __construct(private string $requiredRole)
    {
    }

    public function handle(): void
    {
        $role = $_SESSION['user']['role'] ?? null;
        if ($role !== $this->requiredRole) {
            http_response_code(403);
            echo '403 Forbidden: Unauthorized access.';
            exit;
        }
    }
}
