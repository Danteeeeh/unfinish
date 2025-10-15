<?php
// Load main config for authentication functions
try {
    require_once __DIR__ . '/../../../config.php';
} catch (Exception $e) {
    header('Location: ../../../../database_setup.php');
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../../login.php');
    exit();
}

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/environment.php';

class ReportsController {
    public function index() {
        include __DIR__ . '/../../views/reports/index.php';
    }
}

// Instantiate and run the controller
try {
    $controller = new ReportsController();
    $controller->index();
} catch (Exception $e) {
    error_log("DNMS Reports Controller Error: " . $e->getMessage());
    die("System error occurred. Please contact administrator.");
}
?>
