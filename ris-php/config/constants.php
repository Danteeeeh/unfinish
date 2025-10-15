<?php
/**
 * Global Constants
 * Application-wide configuration constants for RIS-PHP
 */

// Application Settings
define('APP_NAME', 'Radiology Information System');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/id-login-admin/ris-php');

// Path Constants
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/uploads/');
define('ASSETS_PATH', ROOT_PATH . '/assets/');

// Session Settings
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds

// Pagination
define('RECORDS_PER_PAGE', 20);

// Study Status
define('STUDY_STATUS_SCHEDULED', 'scheduled');
define('STUDY_STATUS_IN_PROGRESS', 'in_progress');
define('STUDY_STATUS_COMPLETED', 'completed');
define('STUDY_STATUS_REPORTED', 'reported');
define('STUDY_STATUS_CANCELLED', 'cancelled');

// Report Status
define('REPORT_STATUS_DRAFT', 'draft');
define('REPORT_STATUS_PRELIMINARY', 'preliminary');
define('REPORT_STATUS_FINAL', 'final');
define('REPORT_STATUS_AMENDED', 'amended');

// User Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_RADIOLOGIST', 'radiologist');
define('ROLE_TECHNICIAN', 'technician');
define('ROLE_RECEPTIONIST', 'receptionist');
define('ROLE_REFERRING_PHYSICIAN', 'referring_physician');

// Modalities
define('MODALITY_CR', 'CR'); // Computed Radiography
define('MODALITY_CT', 'CT'); // Computed Tomography
define('MODALITY_MR', 'MR'); // Magnetic Resonance
define('MODALITY_US', 'US'); // Ultrasound
define('MODALITY_XA', 'XA'); // X-Ray Angiography
define('MODALITY_MG', 'MG'); // Mammography
define('MODALITY_DX', 'DX'); // Digital Radiography
define('MODALITY_PT', 'PT'); // Positron Emission Tomography
define('MODALITY_NM', 'NM'); // Nuclear Medicine

// Date Format
define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'd/m/Y');
define('DISPLAY_DATETIME_FORMAT', 'd/m/Y H:i:s');

// File Upload Settings
define('MAX_FILE_SIZE', 104857600); // 100MB in bytes
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'dcm', 'dicom', 'pdf', 'doc', 'docx']);

// DICOM Settings
define('DICOM_SERVER_AE_TITLE', 'RIS_PHP');
define('DICOM_SERVER_PORT', 11112);
