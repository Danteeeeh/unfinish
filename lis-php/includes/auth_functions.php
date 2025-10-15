<?php
/**
 * Authentication Helper Functions
 * Handles user authentication and authorization
 * Note: These are simplified functions for demo purposes
 */

// Only define functions if they don't already exist (to avoid conflicts)
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return true; // Simplified for demo - no actual login required
    }
}

if (!function_exists('getCurrentUser')) {
    function getCurrentUser() {
        return [
            'id' => 1,
            'username' => 'admin',
            'email' => 'admin@lis.com',
            'role' => 'admin',
            'full_name' => 'Administrator'
        ];
    }
}

if (!function_exists('requireLogin')) {
    function requireLogin() {
        // No login required - auto access granted
        return true;
    }
}

if (!function_exists('hasRole')) {
    function hasRole($roles) {
        return true; // No role restrictions
    }
}

if (!function_exists('requireRole')) {
    function requireRole($roles) {
        return true; // No role restrictions
    }
}

/**
 * Login user (LIS-specific implementation)
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
    }
}

/**
 * Logout user (LIS-specific implementation)
 */
if (!function_exists('logout')) {
    function logout() {
        $_SESSION = [];

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        session_destroy();
    }
}

/**
 * Set flash message (LIS-specific implementation)
 */
if (!function_exists('setFlashMessage')) {
    function setFlashMessage($type, $message) {
        $_SESSION['flash_message'] = [
            'type' => $type,
            'message' => $message
        ];
    }
}

/**
 * Display flash message (LIS-specific implementation)
 */
if (!function_exists('displayFlashMessage')) {
    function displayFlashMessage() {
        if (isset($_SESSION['flash_message'])) {
            $flash = $_SESSION['flash_message'];
            $type = htmlspecialchars($flash['type']);
            $message = htmlspecialchars($flash['message']);

            echo '<div class="alert alert-' . $type . '" role="alert">';
            echo '<i class="fas fa-' . ($type == 'success' ? 'check-circle' : 'exclamation-circle') . '"></i> ';
            echo $message;
            echo '<button type="button" class="close" onclick="this.parentElement.style.display=\'none\';">&times;</button>';
            echo '</div>';

            unset($_SESSION['flash_message']);
        }
    }
}

/**
 * Redirect to URL (LIS-specific implementation)
 */
if (!function_exists('redirect')) {
    function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
}

/**
 * Sanitize input (LIS-specific implementation)
 */
if (!function_exists('sanitizeInput')) {
    function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map('sanitizeInput', $data);
        }

        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');

        return $data;
    }
}
