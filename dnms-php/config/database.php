<?php
/**
 * Enhanced Database Configuration
 * DNMS-PHP - Diet & Nutrition Management System
 * Improved with better error handling and connection management
 */

// Define constants only if not already defined
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'healthcare_unified');
}
if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}
if (!defined('DB_CHARSET')) {
    define('DB_CHARSET', 'utf8mb4');
}
if (!defined('DB_CONNECTION_TIMEOUT')) {
    define('DB_CONNECTION_TIMEOUT', 30);
}

// Database connection function with enhanced error handling
if (!function_exists('getDNMSDBConnection')) {
    function getDNMSDBConnection($retryCount = 0) {
        static $connection = null;
        static $lastError = null;

        // Return existing connection if available and healthy
        if ($connection !== null) {
            try {
                $connection->query("SELECT 1");
                return $connection;
            } catch (PDOException $e) {
                error_log("DNMS Database Connection Lost: " . $e->getMessage());
                $connection = null;
            }
        }

        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 10, // Shorter timeout
            ];

            $connection = new PDO($dsn, DB_USER, DB_PASS, $options);

            // Test the connection with a simple query
            $connection->query("SELECT 1");

            error_log("DNMS Database Connection Established Successfully");
            return $connection;

        } catch (PDOException $e) {
            $errorMessage = "Database connection failed: " . $e->getMessage();
            error_log("DNMS Database Connection Error: " . $errorMessage);

            // Implement retry logic for connection failures
            if ($retryCount < 3) {
                sleep(1); // Wait 1 second before retry
                return getDNMSDBConnection($retryCount + 1);
            }

            $lastError = $errorMessage;
            throw new Exception($errorMessage);
        }
    }
}

// Enhanced helper function to check database health
if (!function_exists('testDNMSDatabaseConnection')) {
    function testDNMSDatabaseConnection() {
        try {
            $startTime = microtime(true);
            $db = getDNMSDBConnection();
            $db->query("SELECT 1");
            $endTime = microtime(true);

            $responseTime = round(($endTime - $startTime) * 1000, 2); // in milliseconds

            return [
                'status' => 'success',
                'message' => 'Database connection successful',
                'response_time' => $responseTime . 'ms',
                'database' => DB_NAME,
                'host' => DB_HOST
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'database' => DB_NAME,
                'host' => DB_HOST
            ];
        }
    }
}

// Function to check if required tables exist
if (!function_exists('checkDNMSTablesExist')) {
    function checkDNMSTablesExist() {
        $requiredTables = [
            'dnms_foods',
            'dnms_meals',
            'dnms_nutrition_goals',
            'dnms_users' // Add other tables as needed
        ];

        try {
            $db = getDNMSDBConnection();
            $existingTables = [];

            foreach ($requiredTables as $table) {
                try {
                    $db->query("SELECT 1 FROM {$table} LIMIT 1");
                    $existingTables[] = $table;
                } catch (PDOException $e) {
                    // Table doesn't exist
                }
            }

            return [
                'status' => count($existingTables) === count($requiredTables) ? 'complete' : 'incomplete',
                'existing_tables' => $existingTables,
                'missing_tables' => array_diff($requiredTables, $existingTables),
                'total_required' => count($requiredTables),
                'total_existing' => count($existingTables)
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}

// Function to safely close database connection
if (!function_exists('closeDNMSDBConnection')) {
    function closeDNMSDBConnection() {
        global $connection;
        if ($connection !== null) {
            $connection = null;
            error_log("DNMS Database Connection Closed");
        }
    }
}

// Register shutdown function to close connection
register_shutdown_function('closeDNMSDBConnection');

// Set connection to be closed on script end for non-persistent connections
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
