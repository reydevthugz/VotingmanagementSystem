<?php
namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler, array $middleware = []): void
    {
        $this->map('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array $handler, array $middleware = []): void
    {
        $this->map('POST', $path, $handler, $middleware);
    }

    private function map(string $method, string $path, array $handler, array $middleware): void
    {
        $this->routes[$method][$path] = [
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $basePath = rtrim(BASE_PATH, '/');

        if ($basePath !== '' && ($path === $basePath || str_starts_with($path, $basePath . '/'))) {
            $path = substr($path, strlen($basePath)) ?: '/';
        }

        $path = '/' . trim($path, '/');
        if ($path === '//') {
            $path = '/';
        }

        $route = $this->routes[$method][$path] ?? null;

        if (!$route) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_validate()) {
            http_response_code(403);
            echo 'Invalid CSRF token.';
            return;
        }

        foreach ($route['middleware'] as $middlewareDef) {
            if (is_string($middlewareDef) && str_contains($middlewareDef, ':')) {
                [$middlewareClass, $argument] = explode(':', $middlewareDef, 2);
                (new $middlewareClass($argument))->handle();
                continue;
            }

            $middlewareClass = (string) $middlewareDef;
            (new $middlewareClass())->handle();
        }

        [$controllerClass, $action] = $route['handler'];
        (new $controllerClass())->$action();
    }
}
