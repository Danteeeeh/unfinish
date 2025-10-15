<?php
/**
 * Base Controller
 * Parent class for all controllers
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
     * Load a view
     */
    public function view($view, $data = []) {
        extract($data);
        
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View not found: {$view}");
        }
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
        
        // Check session timeout
        $timeout = $this->config['security']['session_lifetime'];
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
            session_destroy();
            $this->setFlash('error', 'Your session has expired. Please login again.');
            $this->redirect('/login');
        }
        
        $_SESSION['last_activity'] = time();
    }

    /**
     * Check if user has required role
     */
    protected function requireRole($roles) {
        $this->requireAuth();
        
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        if (!in_array($_SESSION['role'], $roles)) {
            $this->setFlash('error', 'You do not have permission to access this page');
            $this->redirect('/dashboard');
        }
    }

    /**
     * Get current user
     */
    protected function getCurrentUser() {
        if ($this->isAuthenticated()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role' => $_SESSION['role'],
                'full_name' => $_SESSION['full_name']
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

    /**
     * JSON response
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Validate CSRF token
     */
    protected function validateCSRF() {
        $token = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        
        if (empty($token) || $token !== $sessionToken) {
            $this->setFlash('error', 'Invalid security token');
            return false;
        }
        
        return true;
    }

    /**
     * Generate CSRF token
     */
    protected function generateCSRF() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}
