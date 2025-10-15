<?php
/**
 * Get Food Categories API
 * Returns all unique food categories from the database
 */

header('Content-Type: application/json');

// Prevent direct access issues
if (!defined('DNMS_ENTRY_POINT')) {
    define('DNMS_ENTRY_POINT', true);
}

// Load main config for authentication functions only (don't restart session)
try {
    require_once __DIR__ . '/../../config.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Configuration error']);
    exit();
}

// Load DNMS config
try {
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../config/constants.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DNMS Configuration Error']);
    exit();
}

// Load includes with error handling
try {
    require_once __DIR__ . '/../includes/auth_functions.php';
} catch (Exception $e) {
    // Continue without auth functions for API endpoints
}

// Check if user is logged in using main system's session
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Initialize Food class
require_once __DIR__ . '/../../classes/Food.php';

try {
    $food = new Food();
    $categories = $food->getCategories();

    echo json_encode($categories);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error fetching categories']);
}
?>
