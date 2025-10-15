<?php
/**
 * Authentication Functions
 * DNMS-PHP - Diet & Nutrition Management System
 *
 * Note: Main authentication functions (isLoggedIn, requireLogin, etc.)
 * are provided by the main config.php file to avoid conflicts
 */

?>
function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? $_SESSION['user_id'],
            'email' => $_SESSION['email'] ?? '',
            'role' => $_SESSION['role'] ?? $_SESSION['user_role'] ?? 'user',
            'full_name' => $_SESSION['full_name'] ?? $_SESSION['user_name'] ?? $_SESSION['user_id']
        ];
    }
    return null;
}

function hasRole($roles) {
    if (!isLoggedIn()) {
        return false;
    }

    if (!is_array($roles)) {
        $roles = [$roles];
    }

    $userRole = $_SESSION['role'] ?? $_SESSION['user_role'] ?? 'user';
    return in_array($userRole, $roles);
}

function requireRole($roles) {
    requireLogin();

    if (!hasRole($roles)) {
        setFlash('error', 'You do not have permission to access this page');
        redirect('dashboard.php');
    }
}

function login($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['last_activity'] = time();

    session_regenerate_id(true);

    // Log activity if logging is available
    if (function_exists('logActivity')) {
        logActivity($user['id'], 'login', 'User logged in');
    }
}

function logout() {
    if (isLoggedIn()) {
        if (function_exists('logActivity')) {
            logActivity($_SESSION['user_id'], 'logout', 'User logged out');
        }
    }

    $_SESSION = [];

    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }

    session_destroy();
}

function displayFlashMessage() {
    $flash = getFlash();
    if ($flash) {
        $type = htmlspecialchars($flash['type']);
        $message = htmlspecialchars($flash['message']);
        return "<div class='alert alert-{$type} alert-dismissible fade show' role='alert'>
            {$message}
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>";
    }
    return '';
}
