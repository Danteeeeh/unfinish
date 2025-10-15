<?php
/**
 * Global Constants
 * Application-wide configuration constants
 */

// Application Settings
define('APP_NAME', 'Laboratory Information System');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/id-login-admin/lis-php');

// Path Constants
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/uploads/');
define('ASSETS_PATH', ROOT_PATH . '/assets/');

// Session Settings
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds

// Pagination
define('RECORDS_PER_PAGE', 20);

// Sample Status
define('SAMPLE_STATUS_PENDING', 'pending');
define('SAMPLE_STATUS_PROCESSING', 'processing');
define('SAMPLE_STATUS_COMPLETED', 'completed');
define('SAMPLE_STATUS_REJECTED', 'rejected');

// Test Status
define('TEST_STATUS_ORDERED', 'ordered');
define('TEST_STATUS_IN_PROGRESS', 'in_progress');
define('TEST_STATUS_COMPLETED', 'completed');
define('TEST_STATUS_VERIFIED', 'verified');

// User Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_TECHNICIAN', 'technician');
define('ROLE_DOCTOR', 'doctor');
define('ROLE_RECEPTIONIST', 'receptionist');

// Date Format
define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'd/m/Y');
define('DISPLAY_DATETIME_FORMAT', 'd/m/Y H:i:s');

// File Upload Settings
define('MAX_FILE_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_FILE_TYPES', ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx']);
