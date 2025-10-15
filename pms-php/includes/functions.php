<?php
/**
 * Helper Functions
 * PMS-PHP - Pharmacy Management System
 */

/**
 * Sanitize input data
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Redirect to a page
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Set flash message
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and display flash message
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        
        $alertClass = [
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info'
        ];
        
        $class = $alertClass[$flash['type']] ?? 'alert-info';
        
        echo '<div class="alert ' . $class . ' alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($flash['message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
    }
}

/**
 * Format currency
 */
function formatCurrency($amount) {
    return CURRENCY_SYMBOL . number_format($amount, 2);
}

/**
 * Format date
 */
function formatDate($date, $format = DISPLAY_DATE_FORMAT) {
    if (empty($date) || $date === '0000-00-00') {
        return 'N/A';
    }
    return date($format, strtotime($date));
}

/**
 * Format datetime
 */
function formatDateTime($datetime, $format = DISPLAY_DATETIME_FORMAT) {
    if (empty($datetime) || $datetime === '0000-00-00 00:00:00') {
        return 'N/A';
    }
    return date($format, strtotime($datetime));
}

/**
 * Calculate percentage
 */
function calculatePercentage($value, $total) {
    if ($total == 0) return 0;
    return round(($value / $total) * 100, 2);
}

/**
 * Generate random string
 */
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

/**
 * Upload file
 */
function uploadFile($file, $destination, $allowedTypes = ALLOWED_IMAGE_TYPES) {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'message' => 'Invalid file upload'];
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload error occurred'];
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File size exceeds limit'];
    }
    
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($fileExt, $allowedTypes)) {
        return ['success' => false, 'message' => 'File type not allowed'];
    }
    
    $newFilename = uniqid() . '_' . time() . '.' . $fileExt;
    $targetPath = $destination . $newFilename;
    
    if (!file_exists($destination)) {
        mkdir($destination, 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'filename' => $newFilename, 'path' => $targetPath];
    }
    
    return ['success' => false, 'message' => 'Failed to move uploaded file'];
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
 * Check if stock is low
 */
function isLowStock($quantity) {
    return $quantity <= LOW_STOCK_THRESHOLD && $quantity > CRITICAL_STOCK_THRESHOLD;
}

/**
 * Check if stock is critical
 */
function isCriticalStock($quantity) {
    return $quantity <= CRITICAL_STOCK_THRESHOLD;
}

/**
 * Check if medicine is expiring soon
 */
function isExpiringSoon($expiryDate) {
    $today = new DateTime();
    $expiry = new DateTime($expiryDate);
    $diff = $today->diff($expiry);
    
    return $diff->days <= EXPIRY_ALERT_DAYS && $diff->invert == 0;
}

/**
 * Check if medicine is expired
 */
function isExpired($expiryDate) {
    return strtotime($expiryDate) < time();
}

/**
 * Generate invoice number
 */
function generateInvoiceNumber() {
    return INVOICE_PREFIX . date('Ymd') . rand(1000, 9999);
}

/**
 * Generate purchase order number
 */
function generatePurchaseNumber() {
    return PURCHASE_PREFIX . date('Ymd') . rand(1000, 9999);
}

/**
 * Calculate discount amount
 */
function calculateDiscount($amount, $discountPercent) {
    return $amount * ($discountPercent / 100);
}

/**
 * Calculate tax amount
 */
function calculateTax($amount) {
    return $amount * TAX_RATE;
}

/**
 * Calculate total with tax
 */
function calculateTotalWithTax($subtotal, $discount = 0) {
    $afterDiscount = $subtotal - $discount;
    $tax = calculateTax($afterDiscount);
    return $afterDiscount + $tax;
}

/**
 * Log activity
 */
function logActivity($userId, $action, $details = '') {
    try {
        $db = getDBConnection();
        $stmt = $db->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$userId, $action, $details, $_SERVER['REMOTE_ADDR']]);
    } catch (PDOException $e) {
        error_log("Activity Log Error: " . $e->getMessage());
    }
}

/**
 * Get pagination data
 */
function getPagination($totalRecords, $currentPage = 1, $recordsPerPage = RECORDS_PER_PAGE) {
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
 * Generate pagination HTML
 */
function renderPagination($pagination, $baseUrl) {
    if ($pagination['total_pages'] <= 1) {
        return '';
    }
    
    $html = '<nav><ul class="pagination justify-content-center">';
    
    // Previous button
    if ($pagination['has_previous']) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . ($pagination['current_page'] - 1) . '">Previous</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
    }
    
    // Page numbers
    for ($i = 1; $i <= $pagination['total_pages']; $i++) {
        if ($i == $pagination['current_page']) {
            $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . $i . '">' . $i . '</a></li>';
        }
    }
    
    // Next button
    if ($pagination['has_next']) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '?page=' . ($pagination['current_page'] + 1) . '">Next</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Next</span></li>';
    }
    
    $html .= '</ul></nav>';
    
    return $html;
}

/**
 * Export to CSV
 */
function exportToCSV($data, $filename) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    if (!empty($data)) {
        fputcsv($output, array_keys($data[0]));
        
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
    }
    
    fclose($output);
    exit();
}
