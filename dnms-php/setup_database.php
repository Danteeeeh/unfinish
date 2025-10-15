<?php
/**
 * DNMS Database Setup Script
 * Creates all necessary DNMS tables from the schema file
 */

// Prevent direct access
if (!defined('DNMS_SETUP')) {
    define('DNMS_SETUP', true);
}

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'healthcare_unified';

try {
    // Connect to MySQL server (without selecting database first)
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    echo "✓ Database '$dbname' created or already exists\n";

    // Select the database
    $pdo->exec("USE `$dbname`");

    // Read and execute the SQL schema file
    $sqlFile = __DIR__ . '/../healthcare_unified.sql';

    if (!file_exists($sqlFile)) {
        throw new Exception("SQL schema file not found: $sqlFile");
    }

    $sql = file_get_contents($sqlFile);

    // Split by semicolon to execute each statement separately
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    $executed = 0;
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^--/', $statement)) {
            try {
                $pdo->exec($statement);
                $executed++;
            } catch (PDOException $e) {
                // Skip comments and empty statements
                if (!preg_match('/^(SET|USE|--)/', $statement)) {
                    echo "Warning: Failed to execute statement: " . substr($statement, 0, 100) . "...\n";
                    echo "Error: " . $e->getMessage() . "\n";
                }
            }
        }
    }

    echo "✓ Executed $executed SQL statements\n";

    // Verify tables were created
    $tables = [
        'dnms_foods',
        'dnms_meals',
        'dnms_meal_items',
        'dnms_meal_foods',
        'dnms_nutrition_goals',
        'dnms_users',
        'dnms_meal_plans'
    ];

    $createdTables = [];
    foreach ($tables as $table) {
        try {
            $result = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($result->rowCount() > 0) {
                $createdTables[] = $table;
            }
        } catch (Exception $e) {
            // Table doesn't exist
        }
    }

    if (count($createdTables) >= 4) { // At least the main tables
        echo "✓ DNMS database setup completed successfully!\n";
        echo "✓ Created tables: " . implode(', ', $createdTables) . "\n";
        echo "\n🎉 Your DNMS system is now ready to use!\n";
        echo "You can now access the dashboard and start using the nutrition management features.\n";
    } else {
        echo "⚠️  Some tables may not have been created. Please check the database manually.\n";
    }

} catch (Exception $e) {
    echo "❌ Database setup failed: " . $e->getMessage() . "\n";
    echo "Please ensure:\n";
    echo "1. MySQL server is running\n";
    echo "2. Database credentials are correct\n";
    echo "3. The healthcare_unified.sql file exists\n";
    exit(1);
}
?>
