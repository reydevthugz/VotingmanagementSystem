<?php
/**
 * Voting Management System - Configuration & Environment Check
 * Run this to diagnose configuration issues
 */

header('Content-Type: text/plain; charset=utf-8');

echo "========================================\n";
echo "VOTING MANAGEMENT SYSTEM - DIAGNOSTICS\n";
echo "========================================\n\n";

// 1. Check PHP Version
echo "[1] PHP VERSION\n";
echo "PHP Version: " . phpversion() . "\n";
echo "PHP SAPI: " . php_sapi_name() . "\n\n";

// 2. Check File Paths
echo "[2] FILE PATHS\n";
echo "Current File: " . __FILE__ . "\n";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'NOT SET') . "\n";
echo "Script Filename: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'NOT SET') . "\n";
echo "Script Name: " . ($_SERVER['SCRIPT_NAME'] ?? 'NOT SET') . "\n";
echo "Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "\n";
echo "Base Path (calculated): " . (dirname(__DIR__)) . "\n\n";

// 3. Load .env and Bootstrap
echo "[3] LOADING BOOTSTRAP\n";
$bootstrap_path = __DIR__ . '/../app/bootstrap.php';
if (file_exists($bootstrap_path)) {
    echo "✓ Bootstrap found at: $bootstrap_path\n";
    require_once $bootstrap_path;
    echo "✓ Bootstrap loaded successfully\n\n";
} else {
    echo "✗ Bootstrap NOT found at: $bootstrap_path\n\n";
}

// 4. Check Environment Configuration
echo "[4] ENVIRONMENT CONFIGURATION\n";
echo "APP_NAME: " . env('APP_NAME') . "\n";
echo "APP_ENV: " . env('APP_ENV') . "\n";
echo "APP_DEBUG: " . env('APP_DEBUG') . "\n";
echo "APP_URL: " . env('APP_URL') . "\n";
echo "BASE_URL (defined): " . (defined('BASE_URL') ? BASE_URL : 'NOT DEFINED') . "\n";
echo "BASE_PATH (defined): " . (defined('BASE_PATH') ? BASE_PATH : 'NOT DEFINED') . "\n";
echo "ASSETS_URL (defined): " . (defined('ASSETS_URL') ? ASSETS_URL : 'NOT DEFINED') . "\n\n";

// 5. Check Database Configuration
echo "[5] DATABASE CONFIGURATION\n";
echo "DB_HOST: " . env('DB_HOST') . "\n";
echo "DB_PORT: " . env('DB_PORT') . "\n";
echo "DB_DATABASE: " . env('DB_DATABASE') . "\n";
echo "DB_USERNAME: " . env('DB_USERNAME') . "\n";
echo "DB_PASSWORD: " . (env('DB_PASSWORD') ? '(set)' : '(empty)') . "\n\n";

// 6. Test Database Connection
echo "[6] DATABASE CONNECTION TEST\n";
try {
    $config = require base_path('config/database.php');
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $config['host'],
        $config['port'],
        $config['database'],
        $config['charset']
    );
    
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    echo "✓ Database connection successful!\n";
    
    // Check if tables exist
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "✓ Tables in database: " . count($tables) . "\n";
    echo "  Tables: " . implode(', ', $tables) . "\n\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n\n";
}

// 7. Check Core Files Exist
echo "[7] CORE FILES CHECK\n";
$required_files = [
    'app/bootstrap.php',
    'app/core/Router.php',
    'config/database.php',
    'routes/web.php',
    'public/index.php',
    '.htaccess',
    'public/.htaccess'
];

foreach ($required_files as $file) {
    $path = base_path($file);
    $exists = file_exists($path) ? '✓' : '✗';
    echo "$exists $file\n";
}
echo "\n";

// 8. Check Apache Modules
echo "[8] APACHE MODULES\n";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    echo "mod_rewrite: " . (in_array('mod_rewrite', $modules) ? '✓ YES' : '✗ NO') . "\n";
    echo "mod_ssl: " . (in_array('mod_ssl', $modules) ? '✓ YES' : '✗ NO') . "\n";
    echo "mod_php: " . (in_array('mod_php', $modules) || in_array('mod_php8', $modules) ? '✓ YES' : '✗ NO') . "\n";
    echo "\nAll modules (" . count($modules) . "):\n";
    foreach ($modules as $mod) {
        echo "  - $mod\n";
    }
} else {
    echo "apache_get_modules() not available (likely running via CLI)\n";
}
echo "\n";

// 9. Check Router
echo "[9] ROUTER CLASS CHECK\n";
if (class_exists('App\Core\Router')) {
    echo "✓ Router class found\n";
} else {
    echo "✗ Router class NOT found\n";
}
echo "\n";

// 10. Directory Permissions
echo "[10] DIRECTORY PERMISSIONS\n";
$dirs_to_check = [
    'app',
    'public',
    'config',
    'routes',
    'storage' => false
];

foreach ($dirs_to_check as $dir => $required) {
    $is_required = is_bool($required) ? $required : true;
    $path = base_path(is_int($dir) ? $dirs_to_check[$dir] : $dir);
    
    if (is_dir($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        echo "✓ $path (perms: $perms)\n";
    } elseif ($is_required) {
        echo "✗ $path (MISSING)\n";
    } else {
        echo "○ $path (optional, not present)\n";
    }
}
echo "\n";

echo "========================================\n";
echo "END OF DIAGNOSTICS\n";
echo "========================================\n";
