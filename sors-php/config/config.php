<?php
/**
 * Application Configuration
 * SORS-PHP - Surgery Operating Room Scheduling System
 */

return [
    'app' => [
        'name' => 'SORS - Surgery Operating Room Scheduling',
        'version' => '1.0.0',
        'url' => getenv('APP_URL') ?: 'http://localhost/sors-php/public',
        'timezone' => 'Asia/Manila',
        'debug' => getenv('APP_DEBUG') === 'true',
    ],

    'security' => [
        'session_lifetime' => 7200, // 2 hours
        'password_min_length' => 8,
    ],

    'surgery' => [
        'types' => [
            'general' => 'General Surgery',
            'orthopedic' => 'Orthopedic Surgery',
            'cardiovascular' => 'Cardiovascular Surgery',
            'neurosurgery' => 'Neurosurgery',
            'plastic' => 'Plastic Surgery',
            'urology' => 'Urology',
            'gynecology' => 'Gynecology',
            'ophthalmology' => 'Ophthalmology',
            'ent' => 'ENT Surgery',
            'pediatric' => 'Pediatric Surgery',
        ],
        'priorities' => [
            'elective' => 'Elective',
            'urgent' => 'Urgent',
            'emergency' => 'Emergency',
        ],
        'statuses' => [
            'scheduled' => 'Scheduled',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'postponed' => 'Postponed',
        ],
    ],

    'room' => [
        'statuses' => [
            'available' => 'Available',
            'occupied' => 'Occupied',
            'maintenance' => 'Under Maintenance',
            'cleaning' => 'Being Cleaned',
        ],
        'types' => [
            'major' => 'Major Operating Room',
            'minor' => 'Minor Operating Room',
            'emergency' => 'Emergency OR',
            'hybrid' => 'Hybrid OR',
        ],
    ],

    'staff' => [
        'roles' => [
            'surgeon' => 'Surgeon',
            'anesthesiologist' => 'Anesthesiologist',
            'nurse' => 'Surgical Nurse',
            'technician' => 'Surgical Technician',
            'assistant' => 'Surgical Assistant',
        ],
    ],

    'upload' => [
        'max_file_size' => 10485760, // 10MB
        'allowed_types' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'],
        'documents_path' => __DIR__ . '/../../uploads/documents/',
        'reports_path' => __DIR__ . '/../../uploads/reports/',
    ],

    'pagination' => [
        'per_page' => 20,
    ],

    'formats' => [
        'date' => 'Y-m-d',
        'time' => 'H:i',
        'datetime' => 'Y-m-d H:i:s',
        'display_date' => 'd/m/Y',
        'display_time' => 'h:i A',
        'display_datetime' => 'd/m/Y h:i A',
    ],
];
