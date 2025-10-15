<?php
/**
 * Environment Configuration
 * PMS-PHP - Pharmacy Management System
 */

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', ROOT_PATH . '/logs/error.log');

// Timezone
date_default_timezone_set('Asia/Manila');

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

// PHP Settings
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');
ini_set('max_execution_time', '300');
ini_set('memory_limit', '256M');

// Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

// Autoloader for Classes
spl_autoload_register(function ($class) {
    $file = ROOT_PATH . '/classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Include Required Files
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/config/constants.php';
require_once ROOT_PATH . '/includes/functions.php';
require_once ROOT_PATH . '/includes/auth.php';
