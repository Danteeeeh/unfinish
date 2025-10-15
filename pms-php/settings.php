<?php
/**
 * PMS-PHP Settings Module
 * Advanced settings and configuration management
 */

// Load PMS config
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';

// Load main config for authentication
require_once __DIR__ . '/../config.php';
requireLogin();

// Connect to database
$conn = getPMSDBConnection();

// Get current settings
try {
    // Get user preferences
    $userSettings = $conn->query("SELECT * FROM user_preferences WHERE user_id = {$_SESSION['user_id']}")->fetch(PDO::FETCH_ASSOC);

    // Get system settings
    $systemSettings = $conn->query("SELECT * FROM system_settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

    $conn = null;
} catch (Exception $e) {
    $userSettings = [];
    $systemSettings = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>⚙️ PMS Settings - System Configuration</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Advanced PMS Settings with Dark Mode */
        :root {
            --primary-gradient: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            --pharmacy-gradient: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
            --info-gradient: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);

            /* Light Theme Variables */
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --bg-tertiary: #e9ecef;
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --text-muted: #868e96;
            --border-color: #dee2e6;
            --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --shadow-md: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            --shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.175);
            --card-bg: #ffffff;
            --navbar-bg: rgba(255, 255, 255, 0.95);
        }

        /* Dark Theme Variables */
        [data-theme="dark"] {
            --primary-gradient: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            --warning-gradient: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
            --danger-gradient: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
            --pharmacy-gradient: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
            --info-gradient: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);

            --bg-primary: #1a1a1a;
            --bg-secondary: #2d2d2d;
            --bg-tertiary: #404040;
            --text-primary: #ffffff;
            --text-secondary: #b3b3b3;
            --text-muted: #888888;
            --border-color: #4a4a4a;
            --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.3);
            --shadow-md: 0 0.5rem 1rem rgba(0, 0, 0, 0.4);
            --shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.5);
            --card-bg: #2d2d2d;
            --navbar-bg: rgba(26, 26, 26, 0.95);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Sidebar Navigation */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 280px;
            background: var(--card-bg);
            border-right: 2px solid var(--border-color);
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .sidebar-header {
            padding: 2rem;
            border-bottom: 1px solid var(--border-color);
            text-align: center;
        }

        .sidebar-header h2 {
            color: var(--text-primary);
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .sidebar-header p {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .sidebar-nav {
            padding: 1rem;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: var(--pharmacy-gradient);
            color: white;
        }

        .sidebar-nav a i {
            font-size: 1.2rem;
        }

        /* Main Content */
        .main-content {
            margin-left: 0;
            transition: margin-left 0.3s ease;
        }

        .main-content.sidebar-open {
            margin-left: 280px;
        }

        /* Hamburger Menu */
        .menu-toggle {
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .menu-toggle:hover {
            background: var(--bg-secondary);
        }

        /* Navbar */
        .navbar {
            background: var(--navbar-bg);
            backdrop-filter: blur(20px);
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
        }

        .nav-brand {
            font-size: 2rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .nav-menu {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .nav-menu a {
            color: var(--text-secondary);
            text-decoration: none;
            padding: 0.75rem 1.25rem;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-menu a:hover {
            color: var(--text-primary);
            background: var(--bg-secondary);
        }

        /* Content */
        .content {
            padding: 3rem;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-header h1 {
            font-size: 3.5rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }

        .page-header p {
            font-size: 1.3rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
        }

        /* Settings Sections */
        .settings-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .settings-section {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
        }

        .settings-section h2 {
            color: var(--text-primary);
            font-size: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .settings-section h2 i {
            color: var(--primary-gradient);
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Settings Grid */
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .setting-item {
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .setting-item:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .setting-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .setting-icon {
            width: 50px;
            height: 50px;
            background: var(--primary-gradient);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .setting-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .setting-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 1rem 1.25rem;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            background: var(--card-bg);
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #ff6b9d;
            box-shadow: 0 0 0 3px rgba(255, 107, 157, 0.1);
        }

        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=US-ASCII,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'%3e%3cpath fill='%23666' d='M2 0L0 2h4zm0 5L0 3h4z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 0.65em auto;
            padding-right: 3rem;
        }

        /* Toggle Switches */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--border-color);
            transition: 0.3s;
            border-radius: 30px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background: white;
            transition: 0.3s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        input:checked + .toggle-slider {
            background: var(--primary-gradient);
        }

        input:checked + .toggle-slider:before {
            transform: translateX(30px);
        }

        /* Buttons */
        .btn-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 107, 157, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 157, 0.4);
        }

        .btn-secondary {
            background: var(--bg-secondary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--border-color);
        }

        /* Footer */
        .footer {
            background: var(--card-bg);
            border-top: 1px solid var(--border-color);
            padding: 2rem;
            text-align: center;
            color: var(--text-secondary);
            margin-top: 3rem;
        }

        .footer p {
            margin: 0;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 1rem;
        }

        .footer-links a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--text-primary);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content.sidebar-open {
                margin-left: 0;
            }

            .navbar {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
            }

            .nav-menu {
                flex-wrap: wrap;
                justify-content: center;
            }

            .content {
                padding: 2rem 1rem;
            }

            .settings-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .btn-group {
                flex-direction: column;
            }

            .footer-links {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar Navigation -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>⚙️ Pharmacy</h2>
            <p>System Settings</p>
        </div>
        <div class="sidebar-nav">
            <a href="modules/dashboard/index.php">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
            <a href="modules/medicines/list.php">
                <i class="fas fa-capsules"></i>
                Medicines
            </a>
            <a href="modules/inventory/">
                <i class="fas fa-boxes"></i>
                Inventory
            </a>
            <a href="modules/reports/">
                <i class="fas fa-chart-line"></i>
                Reports
            </a>
            <a href="modules/prescriptions/">
                <i class="fas fa-prescription"></i>
                Prescriptions
            </a>
            <a href="settings.php" class="active">
                <i class="fas fa-cog"></i>
                Settings
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <nav class="navbar">
            <button class="menu-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div class="nav-brand">
                Pharmacy
            </div>
            <div class="nav-menu">
                <!-- Theme Toggle -->
                <div class="theme-toggle" onclick="toggleTheme()" title="Toggle Dark Mode">
                    <i class="fas fa-sun"></i>
                    <div class="toggle-ball"></div>
                    <i class="fas fa-moon"></i>
                </div>
            </div>
        </nav>

        <div class="content">
            <div class="page-header">
                <h1>⚙️ System Settings</h1>
                <p>Configure your pharmacy management system preferences and customization options</p>
            </div>

            <div class="settings-container">
                <!-- Theme Settings -->
                <div class="settings-section">
                    <h2><i class="fas fa-palette"></i> Theme & Appearance</h2>
                    <div class="settings-grid">
                        <div class="setting-item">
                            <div class="setting-header">
                                <div class="setting-icon">
                                    <i class="fas fa-moon"></i>
                                </div>
                                <div>
                                    <div class="setting-title">Dark Mode</div>
                                    <div class="setting-description">Toggle between light and dark themes</div>
                                </div>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="themeToggle" onchange="toggleTheme()">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="setting-item">
                            <div class="setting-header">
                                <div class="setting-icon">
                                    <i class="fas fa-paint-brush"></i>
                                </div>
                                <div>
                                    <div class="setting-title">Color Scheme</div>
                                    <div class="setting-description">Choose your preferred color theme</div>
                                </div>
                            </div>
                            <select class="form-control form-select" id="colorScheme">
                                <option value="pink">Pink Theme</option>
                                <option value="blue">Blue Theme</option>
                                <option value="green">Green Theme</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- User Preferences -->
                <div class="settings-section">
                    <h2><i class="fas fa-user-cog"></i> User Preferences</h2>
                    <div class="settings-grid">
                        <div class="setting-item">
                            <div class="setting-header">
                                <div class="setting-icon">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <div>
                                    <div class="setting-title">Notifications</div>
                                    <div class="setting-description">Manage notification preferences</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email Notifications</label>
                                <select class="form-control form-select">
                                    <option>Enabled</option>
                                    <option>Disabled</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Low Stock Alerts</label>
                                <select class="form-control form-select">
                                    <option>Enabled</option>
                                    <option>Disabled</option>
                                </select>
                            </div>
                        </div>

                        <div class="setting-item">
                            <div class="setting-header">
                                <div class="setting-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <div class="setting-title">Time & Date</div>
                                    <div class="setting-description">Configure time zone and date format</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Time Zone</label>
                                <select class="form-control form-select">
                                    <option>Asia/Manila (UTC+8)</option>
                                    <option>America/New_York (UTC-5)</option>
                                    <option>Europe/London (UTC+0)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Date Format</label>
                                <select class="form-control form-select">
                                    <option>MM/DD/YYYY</option>
                                    <option>DD/MM/YYYY</option>
                                    <option>YYYY-MM-DD</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Settings -->
                <div class="settings-section">
                    <h2><i class="fas fa-server"></i> System Configuration</h2>
                    <div class="settings-grid">
                        <div class="setting-item">
                            <div class="setting-header">
                                <div class="setting-icon">
                                    <i class="fas fa-database"></i>
                                </div>
                                <div>
                                    <div class="setting-title">Database Settings</div>
                                    <div class="setting-description">Database connection and backup settings</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Auto Backup</label>
                                <select class="form-control form-select">
                                    <option>Daily</option>
                                    <option>Weekly</option>
                                    <option>Monthly</option>
                                    <option>Disabled</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Backup Retention</label>
                                <select class="form-control form-select">
                                    <option>30 days</option>
                                    <option>90 days</option>
                                    <option>1 year</option>
                                </select>
                            </div>
                        </div>

                        <div class="setting-item">
                            <div class="setting-header">
                                <div class="setting-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div>
                                    <div class="setting-title">Security</div>
                                    <div class="setting-description">Security and access control settings</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Session Timeout</label>
                                <select class="form-control form-select">
                                    <option>30 minutes</option>
                                    <option>1 hour</option>
                                    <option>4 hours</option>
                                    <option>Never</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Password Policy</label>
                                <select class="form-control form-select">
                                    <option>Standard</option>
                                    <option>Strong</option>
                                    <option>Maximum</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="btn-group">
                    <button class="btn btn-primary" onclick="saveSettings()">
                        <i class="fas fa-save"></i>
                        Save Settings
                    </button>
                    <button class="btn btn-secondary" onclick="resetSettings()">
                        <i class="fas fa-undo"></i>
                        Reset to Default
                    </button>
                    <button class="btn btn-secondary" onclick="exportSettings()">
                        <i class="fas fa-download"></i>
                        Export Settings
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <p>&copy; <?php echo date('Y'); ?> Pharmacy Pro. All rights reserved.</p>
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Support</a>
            </div>
        </footer>
    </div>

    <script>
        // Theme Management
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.toggle('open');
            mainContent.classList.toggle('sidebar-open');
        }

        function initializeTheme() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);

            // Update toggle button
            const themeToggle = document.getElementById('themeToggle');
            if (themeToggle) {
                themeToggle.checked = savedTheme === 'dark';
            }
        }

        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);

            showNotification(`Switched to ${newTheme} mode!`, 'success');
        }

        function saveSettings() {
            // Simulate saving settings
            showNotification('Settings saved successfully!', 'success');
        }

        function resetSettings() {
            if (confirm('Are you sure you want to reset all settings to default?')) {
                showNotification('Settings reset to default!', 'info');
            }
        }

        function exportSettings() {
            showNotification('Settings exported successfully!', 'success');
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            const colors = {
                success: 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                error: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
                warning: 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                info: 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)'
            };

            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${colors[type]};
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 12px;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
                z-index: 10001;
                max-width: 350px;
                font-family: 'Inter', sans-serif;
                animation: slideInRight 0.3s ease;
            `;

            notification.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-${getIcon(type)}" style="font-size: 18px;"></i>
                    <p style="margin: 0; font-size: 14px; line-height: 1.4;">${message}</p>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.animation = 'slideInRight 0.3s ease reverse';
                    setTimeout(() => notification.parentNode.removeChild(notification), 300);
                }
            }, 4000);
        }

        function getIcon(type) {
            const icons = {
                success: 'check-circle',
                error: 'exclamation-circle',
                warning: 'exclamation-triangle',
                info: 'info-circle'
            };
            return icons[type] || 'info-circle';
        }

        // Initialize theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializeTheme();
        });

        // Add CSS animations for notifications
        const notificationStyles = document.createElement('style');
        notificationStyles.textContent = `
            @keyframes slideInRight {
                from {
                    opacity: 0;
                    transform: translateX(100%);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
        `;
        document.head.appendChild(notificationStyles);
    </script>
</body>
</html>
