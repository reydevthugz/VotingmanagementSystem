<?php
/**
 * Database Migration Script for Voting Management System
 * 
 * This script creates the database and all required tables
 * Run: php database/migrate.php
 */

require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

try {
    // Load environment variables
    $host = env('DB_HOST', '127.0.0.1');
    $port = (int) env('DB_PORT', 3306);
    $database = env('DB_DATABASE', 'votingmanagementsystem');
    $username = env('DB_USERNAME', 'root');
    $password = env('DB_PASSWORD', '');
    
    echo "Connecting to MySQL server...\n";
    
    // Connect without database to create it
    $dsn = sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $host, $port);
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "Connected successfully.\n\n";
    
    // Create database if not exists
    echo "Creating database '{$database}'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database created/verified.\n\n";
    
    // Switch to the database
    $pdo->exec("USE `{$database}`");
    
    // Read and execute schema
    echo "Creating tables...\n";
    $schemaFile = __DIR__ . '/schema.sql';
    $schema = file_get_contents($schemaFile);
    
    if ($schema === false) {
        throw new RuntimeException("Failed to read schema file: {$schemaFile}");
    }
    
    // Split by semicolon to execute individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $schema)),
        fn($stmt) => $stmt !== '' && !preg_match('/^--/', $stmt)
    );
    
    $successCount = 0;
    foreach ($statements as $statement) {
        if (empty(trim($statement))) {
            continue;
        }
        try {
            $pdo->exec($statement);
            $successCount++;
        } catch (PDOException $e) {
            // Skip "already exists" errors
            if (str_contains($e->getMessage(), 'already exists')) {
                echo "  - Table already exists, skipping...\n";
            } else {
                throw $e;
            }
        }
    }
    
    echo "\n✅ Migration completed successfully!\n";
    echo "   - Database: {$database}\n";
    echo "   - Statements executed: {$successCount}\n";
    echo "\nYou can now run the application at: http://localhost/voting-management-system/public\n\n";
    
} catch (PDOException $e) {
    echo "\n❌ Database Error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
