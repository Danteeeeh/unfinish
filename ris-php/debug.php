<?php
// Simple RIS database connection test
echo "<h2>RIS System Diagnostic Test</h2>";
echo "<pre>";

// Test 1: Check if files exist
echo "=== File Existence Check ===\n";
$files_to_check = [
    'app/controllers/BaseController.php',
    'app/controllers/DashboardController.php',
    'app/models/Database.php',
    'app/models/Study.php',
    'app/views/layouts/main.php',
    'public/css/style.css'
];

foreach ($files_to_check as $file) {
    $path = __DIR__ . '/' . $file;
    echo ($file . ': ' . (file_exists($path) ? '✓ EXISTS' : '✗ MISSING') . "\n");
}

echo "\n";

// Test 2: Check database connection
echo "=== Database Connection Test ===\n";
try {
    // Load RIS config
    require_once __DIR__ . '/app/config/database.php';

    // Try to connect using the config
    $config = require __DIR__ . '/app/config/database.php';
    $dsn = sprintf(
        "mysql:host=%s;dbname=%s;charset=%s",
        $config['host'],
        $config['database'],
        $config['charset']
    );

    $pdo = new PDO(
        $dsn,
        $config['username'],
        $config['password'],
        $config['options']
    );

    echo "✓ Database connection successful\n";

    // Check if required tables exist
    $tables = ['patients', 'ris_exams', 'ris_equipment', 'staff', 'users'];
    foreach ($tables as $table) {
        try {
            $result = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($result->rowCount() > 0) {
                echo "✓ Table '$table' exists\n";
            } else {
                echo "✗ Table '$table' missing\n";
            }
        } catch (Exception $e) {
            echo "✗ Error checking table '$table': " . $e->getMessage() . "\n";
        }
    }

    $pdo = null;
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Check BaseController
echo "=== BaseController Test ===\n";
try {
    require_once __DIR__ . '/app/controllers/BaseController.php';
    echo "✓ BaseController loaded successfully\n";
} catch (Exception $e) {
    echo "✗ BaseController failed to load: " . $e->getMessage() . "\n";
}

echo "\n=== End of Test ===\n";
echo "</pre>";
?>
