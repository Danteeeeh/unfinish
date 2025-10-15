<?php
/**
 * Base Controller for SORS
 * Provides common functionality for all SORS controllers
 */

class BaseController {
    protected $config;

    public function __construct() {
        $this->config = require __DIR__ . '/../config/config.php';

        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Load a view with layout
     */
    protected function view($view, $data = []) {
        // Extract data to make it available in view
        extract($data);

        // Include header layout
        require_once __DIR__ . '/../views/layouts/header.php';

        // Include the specific view content
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            echo "<div class='alert alert-error'>View not found: {$view}</div>";
        }

        // Include footer layout
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    /**
     * Redirect to a URL
     */
    protected function redirect($url) {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Set flash message
     */
    protected function setFlash($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    /**
     * Get and clear flash message
     */
    protected function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    /**
     * Check if user is authenticated
     */
    protected function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Require authentication
     */
    protected function requireAuth() {
        if (!$this->isAuthenticated()) {
            $this->setFlash('error', 'Please login to access this page');
            $this->redirect('/login');
        }
    }

    /**
     * Get current user
     */
    protected function getCurrentUser() {
        if ($this->isAuthenticated()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'] ?? $_SESSION['user_id'],
                'full_name' => $_SESSION['full_name'] ?? $_SESSION['username'] ?? $_SESSION['user_id'],
                'role' => $_SESSION['role'] ?? 'user'
            ];
        }
        return null;
    }

    /**
     * Sanitize input
     */
    protected function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }

        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');

        return $data;
    }
}
?>
