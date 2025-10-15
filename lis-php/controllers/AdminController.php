<?php
/**
 * Admin Controller
 * Handles administrative functions for LIS
 */

class AdminController {

    public function index() {
        // Check if user is admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?route=dashboard');
            exit();
        }

        // Get system statistics
        $stats = $this->getSystemStats();

        // Load admin dashboard view
        require_once __DIR__ . '/../views/admin/index.php';
    }

    public function users() {
        // Check if user is admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?route=dashboard');
            exit();
        }

        // Get all users
        $users = $this->getAllUsers();

        // Load users management view
        require_once __DIR__ . '/../views/admin/users.php';
    }

    public function settings() {
        // Check if user is admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?route=dashboard');
            exit();
        }

        // Handle form submissions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->saveSettings($_POST);
        }

        // Get current settings
        $settings = $this->getCurrentSettings();

        // Load settings view
        require_once __DIR__ . '/../views/admin/settings.php';
    }

    public function future() {
        // Check if user is admin
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?route=dashboard');
            exit();
        }

        // Load future features view
        require_once __DIR__ . '/../views/admin/future.php';
    }

    private function getSystemStats() {
        try {
            $conn = getLISDBConnection();

            $stats = [];

            // Total patients
            $stmt = $conn->query("SELECT COUNT(*) as count FROM patients");
            $stats['total_patients'] = $stmt->fetch()['count'];

            // Total samples
            $stmt = $conn->query("SELECT COUNT(*) as count FROM samples");
            $stats['total_samples'] = $stmt->fetch()['count'];

            // Pending tests
            $stmt = $conn->query("SELECT COUNT(*) as count FROM test_orders WHERE status = 'pending'");
            $stats['pending_tests'] = $stmt->fetch()['count'];

            // Completed tests today
            $stmt = $conn->query("SELECT COUNT(*) as count FROM test_orders WHERE DATE(created_at) = CURDATE() AND status = 'completed'");
            $stats['completed_today'] = $stmt->fetch()['count'];

            $conn = null;
            return $stats;

        } catch (Exception $e) {
            return [
                'total_patients' => 0,
                'total_samples' => 0,
                'pending_tests' => 0,
                'completed_today' => 0
            ];
        }
    }

    private function getAllUsers() {
        try {
            $conn = getLISDBConnection();
            $stmt = $conn->query("SELECT id, username, email, role, created_at, last_login FROM users ORDER BY created_at DESC");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $conn = null;
            return $users;

        } catch (Exception $e) {
            return [];
        }
    }

    private function getCurrentSettings() {
        try {
            $conn = getLISDBConnection();
            $stmt = $conn->query("SELECT * FROM system_settings WHERE id = 1");
            $settings = $stmt->fetch(PDO::FETCH_ASSOC);
            $conn = null;
            return $settings ?: [];

        } catch (Exception $e) {
            return [];
        }
    }

    private function saveSettings($data) {
        try {
            $conn = getLISDBConnection();

            // Update or insert settings
            $sql = "INSERT INTO system_settings (id, site_name, site_description, maintenance_mode, updated_at)
                    VALUES (1, ?, ?, ?, NOW())
                    ON DUPLICATE KEY UPDATE
                    site_name = VALUES(site_name),
                    site_description = VALUES(site_description),
                    maintenance_mode = VALUES(maintenance_mode),
                    updated_at = VALUES(updated_at)";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $data['site_name'] ?? 'LIS - Laboratory Information System',
                $data['site_description'] ?? 'Advanced laboratory management system',
                isset($data['maintenance_mode']) ? 1 : 0
            ]);

            $conn = null;

            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Settings saved successfully!'];
            header('Location: index.php?route=admin/settings');
            exit();

        } catch (Exception $e) {
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Error saving settings: ' . $e->getMessage()];
        }
    }
}
?>
