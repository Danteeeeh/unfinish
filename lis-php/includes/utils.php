<?php
/**
 * General Utility Functions
 * Helper functions for common tasks
 */

/**
 * Format date for display
 */
function formatDate($date, $format = DISPLAY_DATE_FORMAT) {
    if (empty($date)) {
        return '';
    }
    
    return date($format, strtotime($date));
}

/**
 * Format datetime for display
 */
function formatDateTime($datetime, $format = DISPLAY_DATETIME_FORMAT) {
    if (empty($datetime)) {
        return '';
    }
    
    return date($format, strtotime($datetime));
}

/**
 * Calculate age from date of birth
 */
function calculateAge($dateOfBirth) {
    $dob = new DateTime($dateOfBirth);
    $now = new DateTime();
    $age = $now->diff($dob);
    
    return $age->y;
}

/**
 * Generate random string
 */
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    
    return $randomString;
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number
 */
function validatePhone($phone) {
    // Remove all non-numeric characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Check if phone number has at least 10 digits
    return strlen($phone) >= 10;
}

/**
 * Format file size
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Upload file
 */
function uploadFile($file, $destination, $allowedTypes = ALLOWED_FILE_TYPES) {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'message' => 'Invalid file upload.'];
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error.'];
    }
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File size exceeds maximum allowed size.'];
    }
    
    // Get file extension
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Check file type
    if (!in_array($fileExtension, $allowedTypes)) {
        return ['success' => false, 'message' => 'File type not allowed.'];
    }
    
    // Generate unique filename
    $newFilename = uniqid() . '_' . basename($file['name']);
    $targetPath = $destination . '/' . $newFilename;
    
    // Create directory if it doesn't exist
    if (!file_exists($destination)) {
        mkdir($destination, 0755, true);
    }
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'filename' => $newFilename, 'path' => $targetPath];
    }
    
    return ['success' => false, 'message' => 'Failed to move uploaded file.'];
}

/**
 * Delete file
 */
function deleteFile($filePath) {
    if (file_exists($filePath)) {
        return unlink($filePath);
    }
    
    return false;
}

/**
 * Get pagination data
 */
function getPaginationData($totalRecords, $currentPage = 1, $recordsPerPage = RECORDS_PER_PAGE) {
    $totalPages = ceil($totalRecords / $recordsPerPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $recordsPerPage;
    
    return [
        'total_records' => $totalRecords,
        'total_pages' => $totalPages,
        'current_page' => $currentPage,
        'records_per_page' => $recordsPerPage,
        'offset' => $offset,
        'has_previous' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages
    ];
}

/**
 * Log activity
 */
function logActivity($userId, $action, $description) {
    $logFile = ROOT_PATH . '/logs/activity.log';
    $logDir = dirname($logFile);
    
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date(DATETIME_FORMAT);
    $logEntry = "[{$timestamp}] User ID: {$userId} | Action: {$action} | Description: {$description}\n";
    
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

/**
 * Send email notification
 */
function sendEmailNotification($to, $subject, $message) {
    // Placeholder for email functionality
    // In production, use PHPMailer or similar library
    
    $headers = "From: " . APP_NAME . " <noreply@example.com>\r\n";
    $headers .= "Reply-To: noreply@example.com\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $message, $headers);
}

/**
 * Generate barcode (placeholder)
 */
function generateBarcode($data) {
    // Placeholder for barcode generation
    // In production, use a barcode library like picqer/php-barcode-generator
    
    return $data;
}

/**
 * Export to CSV
 */
function exportToCSV($data, $filename) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // Add headers
    if (!empty($data)) {
        fputcsv($output, array_keys($data[0]));
        
        // Add data rows
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
    }
    
    fclose($output);
    exit;
}
