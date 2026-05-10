<?php
declare(strict_types=1);

function base_path(string $path = ''): string
{
    $base = dirname(__DIR__);
    return $path ? $base . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : $base;
}

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $segments = explode('\\', $relativeClass);
    $segments[0] = strtolower($segments[0]);
    $file = base_path('app/' . implode('/', $segments) . '.php');

    if (is_file($file)) {
        require_once $file;
    }
});

function env(string $key, mixed $default = null): mixed
{
    static $loaded = false;

    if (!$loaded) {
        $envPath = base_path('.env');
        if (is_file($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                    continue;
                }
                [$k, $v] = explode('=', $line, 2);
                $_ENV[trim($k)] = trim($v, " \t\n\r\0\x0B\"");
            }
        }
        $loaded = true;
    }

    return $_ENV[$key] ?? $default;
}

function flash(string $key = null): mixed
{
    if ($key === null) {
        $flash = $_SESSION['_flash'] ?? null;
        unset($_SESSION['_flash']);
        return $flash;
    }

    $value = $_SESSION[$key] ?? null;
    unset($_SESSION[$key]);
    return $value;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function csrf_validate(?string $token = null): bool
{
    if ($token === null) {
        $token = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    }

    return is_string($token) && hash_equals((string) csrf_token(), (string) $token);
}

function log_error(\Throwable $exception): void
{
    $logDirectory = base_path('storage/logs');
    if (!is_dir($logDirectory) && !mkdir($logDirectory, 0755, true) && !is_dir($logDirectory)) {
        return;
    }

    $message = sprintf(
        '[%s] %s in %s on line %d%sStack trace:%s%s%s',
        date('Y-m-d H:i:s'),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        PHP_EOL,
        PHP_EOL,
        $exception->getTraceAsString(),
        PHP_EOL,
        PHP_EOL
    );

    error_log($message, 3, $logDirectory . DIRECTORY_SEPARATOR . 'error.log');
}

set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
    if (!(error_reporting() & $severity)) {
        return false;
    }

    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(static function (Throwable $exception): void {
    log_error($exception);
    http_response_code(500);
    if (filter_var((string) env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN)) {
        echo '<pre>' . htmlspecialchars((string) $exception, ENT_QUOTES, 'UTF-8') . '</pre>';
    } else {
        echo 'An internal error occurred. Please try again later.';
    }
});

register_shutdown_function(static function (): void {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR], true)) {
        $exception = new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
        log_error($exception);
        http_response_code(500);
        echo 'A fatal error occurred. Please contact support.';
    }
});

session_name((string) env('SESSION_NAME', 'vms_session'));
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');
    ini_set('session.cookie_lifetime', '0');
    ini_set('session.sid_length', '48');
    ini_set('session.sid_bits_per_character', '6');

    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

define('BASE_URL', rtrim((string) env('BASE_URL', env('APP_URL', 'http://localhost/voting-management-system/')), '/') . '/');
define('BASE_PATH', rtrim(parse_url(BASE_URL, PHP_URL_PATH) ?: '/', '/'));
define('ASSETS_URL', BASE_URL . 'assets/');

$debug = filter_var((string) env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN);
error_reporting($debug ? E_ALL : 0);
ini_set('display_errors', $debug ? '1' : '0');
