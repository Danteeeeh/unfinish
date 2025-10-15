<?php
/**
 * Application Constants
 * PMS-PHP - Pharmacy Management System
 */

// Application Settings
define('APP_NAME', 'Pharmacy Management System');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/pms-php');

// Path Constants
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/assets/uploads/');
define('MEDICINE_IMAGE_PATH', UPLOAD_PATH . 'medicines/');
define('INVOICE_PATH', UPLOAD_PATH . 'invoices/');

// Session Settings
define('SESSION_TIMEOUT', 3600); // 1 hour
define('SESSION_NAME', 'PMS_SESSION');

// Pagination
define('RECORDS_PER_PAGE', 20);

// User Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_PHARMACIST', 'pharmacist');
define('ROLE_CASHIER', 'cashier');
define('ROLE_MANAGER', 'manager');

// Medicine Categories
define('MEDICINE_CATEGORIES', [
    'Tablet' => 'Tablet',
    'Capsule' => 'Capsule',
    'Syrup' => 'Syrup',
    'Injection' => 'Injection',
    'Drops' => 'Drops',
    'Cream' => 'Cream',
    'Ointment' => 'Ointment',
    'Inhaler' => 'Inhaler',
    'Powder' => 'Powder',
    'Other' => 'Other'
]);

// Stock Alert Levels
define('LOW_STOCK_THRESHOLD', 50);
define('CRITICAL_STOCK_THRESHOLD', 20);
define('EXPIRY_ALERT_DAYS', 90); // Alert 90 days before expiry

// Date Formats
define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'd/m/Y');
define('DISPLAY_DATETIME_FORMAT', 'd/m/Y h:i A');

// Currency
define('CURRENCY_SYMBOL', '$');
define('CURRENCY_CODE', 'USD');

// Tax Settings
define('TAX_RATE', 0.10); // 10% tax
define('DISCOUNT_MAX_PERCENT', 50);

// File Upload Settings
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('ALLOWED_DOCUMENT_TYPES', ['pdf', 'doc', 'docx']);

// Payment Methods
define('PAYMENT_METHODS', [
    'cash' => 'Cash',
    'card' => 'Credit/Debit Card',
    'upi' => 'UPI',
    'cheque' => 'Cheque',
    'online' => 'Online Transfer'
]);

// Invoice Settings
define('INVOICE_PREFIX', 'INV');
define('PURCHASE_PREFIX', 'PUR');

// Report Types
define('REPORT_TYPES', [
    'daily' => 'Daily Report',
    'weekly' => 'Weekly Report',
    'monthly' => 'Monthly Report',
    'yearly' => 'Yearly Report',
    'custom' => 'Custom Date Range'
]);
