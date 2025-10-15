<?php
/**
 * Application Configuration
 * RIS-PHP - Radiology Information System
 */

return [
    // Application Settings
    'app' => [
        'name' => 'RIS-PHP',
        'version' => '1.0.0',
        'url' => getenv('APP_URL') ?: 'http://localhost/ris-php',
        'timezone' => 'Asia/Manila',
        'debug' => getenv('APP_DEBUG') === 'true',
    ],

    // Security Settings
    'security' => [
        'session_lifetime' => 3600, // 1 hour
        'password_min_length' => 8,
        'max_login_attempts' => 5,
        'lockout_duration' => 900, // 15 minutes
        'csrf_token_name' => 'csrf_token',
    ],

    // File Upload Settings
    'upload' => [
        'max_file_size' => 104857600, // 100MB
        'allowed_image_types' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'dcm', 'dicom'],
        'allowed_document_types' => ['pdf', 'doc', 'docx'],
        'studies_path' => __DIR__ . '/../../public/uploads/studies/',
        'reports_path' => __DIR__ . '/../../public/uploads/reports/',
        'temp_path' => __DIR__ . '/../../public/uploads/temp/',
    ],

    // DICOM Settings
    'dicom' => [
        'server_ae_title' => 'RIS_PHP',
        'server_port' => 11112,
        'storage_path' => __DIR__ . '/../../public/uploads/studies/',
        'viewer_url' => '/viewer.php',
    ],

    // Pagination
    'pagination' => [
        'per_page' => 20,
        'max_per_page' => 100,
    ],

    // User Roles
    'roles' => [
        'admin' => 'Administrator',
        'radiologist' => 'Radiologist',
        'technician' => 'Technician',
        'receptionist' => 'Receptionist',
        'referring_physician' => 'Referring Physician',
    ],

    // Study Status
    'study_status' => [
        'scheduled' => 'Scheduled',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'reported' => 'Reported',
        'cancelled' => 'Cancelled',
    ],

    // Report Status
    'report_status' => [
        'draft' => 'Draft',
        'preliminary' => 'Preliminary',
        'final' => 'Final',
        'amended' => 'Amended',
    ],

    // Modalities
    'modalities' => [
        'CR' => 'Computed Radiography',
        'CT' => 'Computed Tomography',
        'MR' => 'Magnetic Resonance',
        'US' => 'Ultrasound',
        'XA' => 'X-Ray Angiography',
        'MG' => 'Mammography',
        'DX' => 'Digital Radiography',
        'PT' => 'Positron Emission Tomography',
        'NM' => 'Nuclear Medicine',
    ],

    // Date/Time Formats
    'formats' => [
        'date' => 'Y-m-d',
        'time' => 'H:i:s',
        'datetime' => 'Y-m-d H:i:s',
        'display_date' => 'd/m/Y',
        'display_datetime' => 'd/m/Y H:i:s',
    ],

    // Email Settings
    'email' => [
        'from_address' => 'noreply@ris-php.com',
        'from_name' => 'RIS-PHP System',
        'smtp_host' => getenv('SMTP_HOST') ?: 'localhost',
        'smtp_port' => getenv('SMTP_PORT') ?: 587,
        'smtp_username' => getenv('SMTP_USER') ?: '',
        'smtp_password' => getenv('SMTP_PASS') ?: '',
        'smtp_encryption' => 'tls',
    ],

    // API Settings
    'api' => [
        'version' => 'v1',
        'rate_limit' => 100, // requests per minute
        'token_expiry' => 86400, // 24 hours
    ],

    // Logging
    'logging' => [
        'enabled' => true,
        'level' => 'info', // debug, info, warning, error
        'path' => __DIR__ . '/../../logs/',
        'max_files' => 30,
    ],
];
