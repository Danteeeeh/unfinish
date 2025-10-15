<?php
/**
 * Authentication Functions
 * PMS-PHP - Pharmacy Management System
 */

// Only define functions if they don't already exist (to avoid conflicts with main config)
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
}

if (!function_exists('getCurrentUser')) {
    function getCurrentUser() {
        if (isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'email' => $_SESSION['email'],
                'role' => $_SESSION['role'],
                'full_name' => $_SESSION['full_name']
            ];
        }
        return null;
    }
}

if (!function_exists('requireLogin')) {
    function requireLogin() {
        if (!isLoggedIn()) {
            setFlash('error', 'Please login to access this page');
            redirect(APP_URL . '/modules/auth/login.php');
        }

        // Check session timeout
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
            logout();
            setFlash('error', 'Your session has expired. Please login again.');
            redirect(APP_URL . '/modules/auth/login.php');
        }

        $_SESSION['last_activity'] = time();
    }
}

if (!function_exists('hasRole')) {
    function hasRole($roles) {
        if (!isLoggedIn()) {
            return false;
        }

        if (!is_array($roles)) {
            $roles = [$roles];
        }

        return in_array($_SESSION['role'], $roles);
    }
}

if (!function_exists('requireRole')) {
    function requireRole($roles) {
        requireLogin();

        if (!hasRole($roles)) {
            setFlash('error', 'You do not have permission to access this page');
            redirect(APP_URL . '/modules/dashboard/index.php');
        }
    }
}

/**
 * Login user (PMS-specific implementation)
 */
if (!function_exists('login')) {
    function login($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['last_activity'] = time();

        // Regenerate session ID for security
        session_regenerate_id(true);

        // Log activity (PMS-specific)
        logActivity($user['id'], 'login', 'User logged in');
    }
}

/**
 * Logout user (PMS-specific implementation)
 */
if (!function_exists('logout')) {
    function logout() {
        if (isLoggedIn()) {
            logActivity($_SESSION['user_id'], 'logout', 'User logged out');
        }

        $_SESSION = [];

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        session_destroy();
    }
}

/**
 * Check password strength (PMS-specific implementation)
 */
if (!function_exists('isStrongPassword')) {
    function isStrongPassword($password) {
        // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
        return strlen($password) >= 8
            && preg_match('/[A-Z]/', $password)
            && preg_match('/[a-z]/', $password)
            && preg_match('/[0-9]/', $password);
    }
}

/**
 * Generate CSRF token (PMS-specific implementation)
 */
if (!function_exists('generateCSRFToken')) {
    function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

/**
 * Validate CSRF token (PMS-specific implementation)
 */
if (!function_exists('validateCSRFToken')) {
    function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}

/**
 * Get CSRF token field (PMS-specific implementation)
 */
if (!function_exists('csrfField')) {
    function csrfField() {
        $token = generateCSRFToken();
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }
}
