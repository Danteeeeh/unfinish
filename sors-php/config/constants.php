<?php
/**
 * Global Constants
 * Application-wide configuration constants for SORS-PHP
 */

// Application Settings
define('APP_NAME', 'Surgery Operating Room Scheduling System');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/id-login-admin/sors-php');

// Path Constants
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/uploads/');
define('ASSETS_PATH', ROOT_PATH . '/assets/');

// Session Settings
define('SESSION_TIMEOUT', 7200); // 2 hours in seconds

// Pagination
define('RECORDS_PER_PAGE', 20);

// Surgery Types
define('SURGERY_TYPE_GENERAL', 'general');
define('SURGERY_TYPE_ORTHOPEDIC', 'orthopedic');
define('SURGERY_TYPE_CARDIOVASCULAR', 'cardiovascular');
define('SURGERY_TYPE_NEUROSURGERY', 'neurosurgery');
define('SURGERY_TYPE_PLASTIC', 'plastic');
define('SURGERY_TYPE_UROLOGY', 'urology');
define('SURGERY_TYPE_GYNECOLOGY', 'gynecology');
define('SURGERY_TYPE_OPHTHALMOLOGY', 'ophthalmology');
define('SURGERY_TYPE_ENT', 'ent');
define('SURGERY_TYPE_PEDIATRIC', 'pediatric');

// Surgery Priorities
define('PRIORITY_ELECTIVE', 'elective');
define('PRIORITY_URGENT', 'urgent');
define('PRIORITY_EMERGENCY', 'emergency');

// Surgery Status
define('STATUS_SCHEDULED', 'scheduled');
define('STATUS_IN_PROGRESS', 'in_progress');
define('STATUS_COMPLETED', 'completed');
define('STATUS_CANCELLED', 'cancelled');
define('STATUS_POSTPONED', 'postponed');

// Room Status
define('ROOM_STATUS_AVAILABLE', 'available');
define('ROOM_STATUS_OCCUPIED', 'occupied');
define('ROOM_STATUS_MAINTENANCE', 'maintenance');
define('ROOM_STATUS_CLEANING', 'cleaning');

// Room Types
define('ROOM_TYPE_MAJOR', 'major');
define('ROOM_TYPE_MINOR', 'minor');
define('ROOM_TYPE_EMERGENCY', 'emergency');
define('ROOM_TYPE_HYBRID', 'hybrid');

// Staff Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_SURGEON', 'surgeon');
define('ROLE_ANESTHESIOLOGIST', 'anesthesiologist');
define('ROLE_NURSE', 'nurse');
define('ROLE_TECHNICIAN', 'technician');
define('ROLE_ASSISTANT', 'assistant');

// Date Format
define('DATE_FORMAT', 'Y-m-d');
define('TIME_FORMAT', 'H:i');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'd/m/Y');
define('DISPLAY_TIME_FORMAT', 'h:i A');
define('DISPLAY_DATETIME_FORMAT', 'd/m/Y h:i A');

// File Upload Settings
define('MAX_FILE_SIZE', 10485760); // 10MB in bytes
define('ALLOWED_FILE_TYPES', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']);
