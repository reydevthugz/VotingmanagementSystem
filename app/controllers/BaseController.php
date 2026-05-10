<?php
namespace App\Controllers;

class BaseController
{
    protected function render(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = base_path('app/views/' . $view . '.php');
        if (!is_file($viewFile)) {
            http_response_code(500);
            echo 'View not found.';
            return;
        }
        require base_path('app/views/layouts/main.php');
    }

    protected function redirect(string $path): void
    {
        if (!str_starts_with($path, 'http://') && !str_starts_with($path, 'https://')) {
            $path = BASE_URL . ltrim($path, '/');
        }
        header('Location: ' . $path);
        exit;
    }

    protected function backWithErrors(string $path, array $errors, array $old = []): void
    {
        $_SESSION['_errors'] = $errors;
        $_SESSION['_old'] = $old;
        $this->redirect($path);
    }

    protected function flash(string $type, string $message): void
    {
        $_SESSION['_flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    protected function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function recordActivity(string $action, array $context = []): void
    {
        if (empty($_SESSION['user']['id'])) {
            return;
        }

        try {
            $activityLog = new \App\Models\ActivityLog();
            $activityLog->record(
                (int) $_SESSION['user']['id'],
                $action,
                $context,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            );
        } catch (\Throwable) {
            // Logging failure should not affect user flow.
        }
    }
}
