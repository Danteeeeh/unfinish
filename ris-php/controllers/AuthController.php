<?php
/**
 * Authentication Controller
 * Handles user authentication and registration
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Audit.php';

class AuthController extends BaseController {
    private $userModel;
    private $auditModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->auditModel = new Audit();
    }

    /**
     * Show login form
     */
    public function showLogin() {
        if ($this->isAuthenticated()) {
            $this->redirect('/dashboard');
        }
        
        $this->view('auth/login', ['title' => 'Login']);
    }

    /**
     * Handle login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }

        $username = $this->sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $this->setFlash('error', 'Please enter both username and password');
            $this->redirect('/login');
        }

        $user = $this->userModel->authenticate($username, $password);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['last_activity'] = time();

            $this->auditModel->log($user['id'], 'login', 'user', $user['id'], null, $_SERVER['REMOTE_ADDR']);

            $this->setFlash('success', 'Welcome back, ' . $user['full_name']);
            $this->redirect('/dashboard');
        } else {
            $this->auditModel->log(null, 'failed_login', 'user', null, ['username' => $username], $_SERVER['REMOTE_ADDR']);
            $this->setFlash('error', 'Invalid username or password');
            $this->redirect('/login');
        }
    }

    /**
     * Handle logout
     */
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $this->auditModel->log($_SESSION['user_id'], 'logout', 'user', $_SESSION['user_id']);
        }

        session_destroy();
        $this->setFlash('success', 'You have been logged out successfully');
        $this->redirect('/login');
    }

    /**
     * Show registration form
     */
    public function showRegister() {
        if ($this->isAuthenticated()) {
            $this->redirect('/dashboard');
        }
        
        $this->view('auth/register', ['title' => 'Register']);
    }

    /**
     * Handle registration
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/register');
        }

        $data = [
            'username' => $this->sanitize($_POST['username'] ?? ''),
            'email' => $this->sanitize($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'full_name' => $this->sanitize($_POST['full_name'] ?? ''),
            'role' => 'receptionist' // Default role
        ];

        // Validation
        $errors = [];

        if (empty($data['username'])) {
            $errors[] = 'Username is required';
        } elseif ($this->userModel->usernameExists($data['username'])) {
            $errors[] = 'Username already exists';
        }

        if (empty($data['email'])) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        } elseif ($this->userModel->emailExists($data['email'])) {
            $errors[] = 'Email already exists';
        }

        if (empty($data['password'])) {
            $errors[] = 'Password is required';
        } elseif (strlen($data['password']) < 8) {
            $errors[] = 'Password must be at least 8 characters';
        } elseif ($data['password'] !== $data['password_confirm']) {
            $errors[] = 'Passwords do not match';
        }

        if (!empty($errors)) {
            $this->setFlash('error', implode('<br>', $errors));
            $this->redirect('/register');
        }

        unset($data['password_confirm']);

        $userId = $this->userModel->create($data);

        if ($userId) {
            $this->auditModel->log($userId, 'register', 'user', $userId);
            $this->setFlash('success', 'Registration successful! Please login.');
            $this->redirect('/login');
        } else {
            $this->setFlash('error', 'Registration failed. Please try again.');
            $this->redirect('/register');
        }
    }
}
