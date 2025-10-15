<?php
/**
 * Enhanced PMS Dashboard with Dark Mode
 */

// Load PMS config first
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/constants.php';

// Load main config for authentication functions only
require_once __DIR__ . '/../../../config.php';
requireLogin();

// Connect to database
try {
    $conn = getPMSDBConnection();

    // Get stats
    $totalMedicines = $conn->query("SELECT COUNT(*) as count FROM pms_medicines")->fetch()['count'];
    $totalInventory = $conn->query("SELECT COUNT(*) as count FROM pms_inventory")->fetch()['count'];
    $lowStockItems = $conn->query("SELECT COUNT(*) as count FROM pms_inventory WHERE quantity <= 10")->fetch()['count'];
    $expiredItems = $conn->query("SELECT COUNT(*) as count FROM pms_inventory WHERE expiry_date < CURDATE()")->fetch()['count'];
    $totalPrescriptions = $conn->query("SELECT COUNT(*) as count FROM prescriptions")->fetch()['count'];
    $pendingPrescriptions = $conn->query("SELECT COUNT(*) as count FROM prescriptions WHERE status = 'pending'")->fetch()['count'];

    $conn = null; // Close connection
} catch (Exception $e) {
    // Fallback values if database connection fails
    $totalMedicines = 0;
    $totalInventory = 0;
    $lowStockItems = 0;
    $expiredItems = 0;
    $totalPrescriptions = 0;
    $pendingPrescriptions = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>💊 Pharmacy Pro - Advanced Pharmacy Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Enhanced PMS Dashboard with Dark Mode */
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            --pharmacy-gradient: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);

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
            --primary-gradient: linear-gradient(135deg, #9f7aea 0%, #667eea 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            --warning-gradient: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
            --danger-gradient: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
            --pharmacy-gradient: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);

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

        /* Modern Navbar */
        .navbar {
            background: var(--navbar-bg);
            backdrop-filter: blur(20px);
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            position: relative;
        }

        .navbar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--pharmacy-gradient);
        }

        .nav-brand {
            font-size: 2rem;
            font-weight: 800;
            background: var(--pharmacy-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .nav-brand i {
            font-size: 2.2rem;
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
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-menu a:hover {
            color: var(--text-primary);
            background: var(--bg-secondary);
            transform: translateY(-2px);
        }

        .nav-menu a i {
            font-size: 1.1rem;
        }

        /* Enhanced Content */
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
            background: var(--pharmacy-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
        }

        .page-header p {
            font-size: 1.3rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
        }

        /* Enhanced Stats Grid */
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 2rem;
                margin-bottom: 3rem;
            }

        .stat-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--pharmacy-gradient);
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(255, 107, 157, 0.3);
        }

        .stat-card.medicines {
            background: linear-gradient(135deg, rgba(255, 107, 157, 0.1), rgba(196, 69, 105, 0.05));
        }

        .stat-card.prescriptions {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.05));
        }

        .stat-card.pending {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(217, 119, 6, 0.05));
        }

        .stat-card.danger {
            background: linear-gradient(135deg, rgba(245, 101, 101, 0.1), rgba(229, 62, 62, 0.05));
        }

        .stat-icon {
            width: 80px;
            height: 80px;
            background: var(--pharmacy-gradient);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5rem;
            margin: 0 auto 1.5rem;
        }

        .stat-card h3 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            color: var(--text-primary);
            font-weight: 600;
        }

        .stat-card p {
            font-size: 3rem;
            font-weight: 800;
            margin: 0;
            background: var(--pharmacy-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Enhanced Welcome Message */
        .welcome-message {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
            margin-top: 2rem;
        }

        .welcome-message h3 {
            font-size: 2rem;
            color: var(--text-primary);
            margin-bottom: 1rem;
            background: var(--pharmacy-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .welcome-message p {
            color: var(--text-secondary);
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .welcome-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .welcome-btn {
            padding: 1rem 2rem;
            background: var(--pharmacy-gradient);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .welcome-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 107, 157, 0.4);
        }

        /* Theme Toggle */
        .theme-toggle {
            background: var(--border-color);
            border: 2px solid var(--border-color);
            border-radius: 50px;
            padding: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .theme-toggle:hover {
            background: var(--bg-secondary);
            border-color: #ff6b9d;
            transform: scale(1.05);
        }

        .theme-toggle .toggle-ball {
            width: 18px;
            height: 18px;
            background: var(--pharmacy-gradient);
            border-radius: 50%;
            transition: transform 0.3s ease;
            transform: translateX(0);
        }

        [data-theme="dark"] .theme-toggle .toggle-ball {
            transform: translateX(20px);
        }

        .theme-toggle i {
            font-size: 0.75rem;
            color: var(--text-primary);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-card {
            animation: fadeInUp 0.6s ease forwards;
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }
        .stat-card:nth-child(5) { animation-delay: 0.5s; }
        .stat-card:nth-child(6) { animation-delay: 0.6s; }

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

            .page-header h1 {
                font-size: 2.5rem;
            }

            .page-header p {
                font-size: 1.1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .welcome-message {
                padding: 2rem 1rem;
            }

            .welcome-actions {
                flex-direction: column;
                align-items: center;
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
            <h2>🏥 Pharmacy </h2>
            <p>Professional Management</p>
        </div>
        <div class="sidebar-nav">
            <a href="../dashboard/index.php" class="active">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
            <a href="../medicines/list.php">
                <i class="fas fa-capsules"></i>
                Medicines
            </a>
            <a href="../inventory/">
                <i class="fas fa-boxes"></i>
                Inventory
            </a>
            <a href="../reports/">
                <i class="fas fa-chart-line"></i>
                Reports
            </a>
            <a href="../prescriptions/">
                <i class="fas fa-prescription"></i>
                Prescriptions
            </a>
            <a href="../../settings.php">
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
                <a href="../dashboard/">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </div>
        </nav>

        <div class="content">
            <div class="page-header">
                <h1>💊 Pharmacy Management System</h1>
                <p>Advanced pharmaceutical management with inventory tracking, medicine database, and comprehensive reporting</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card medicines">
                    <div class="stat-icon">
                        <i class="fas fa-capsules"></i>
                    </div>
                    <h3>Total Medicines</h3>
                    <p><?php echo number_format($totalMedicines); ?></p>
                </div>

                <div class="stat-card inventory">
                    <div class="stat-icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h3>Inventory Items</h3>
                    <p><?php echo number_format($totalInventory); ?></p>
                </div>

                <div class="stat-card prescriptions">
                    <div class="stat-icon">
                        <i class="fas fa-prescription"></i>
                    </div>
                    <h3>Total Prescriptions</h3>
                    <p><?php echo number_format($totalPrescriptions); ?></p>
                </div>

                <?php if ($lowStockItems > 0): ?>
                <div class="stat-card warning">
                    <div class="stat-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3>Low Stock Items</h3>
                    <p><?php echo number_format($lowStockItems); ?></p>
                </div>
                <?php endif; ?>

                <?php if ($expiredItems > 0): ?>
                <div class="stat-card danger">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <h3>Expired Items</h3>
                    <p><?php echo number_format($expiredItems); ?></p>
                </div>
                <?php endif; ?>

                <?php if ($pendingPrescriptions > 0): ?>
                <div class="stat-card pending">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Pending Prescriptions</h3>
                    <p><?php echo number_format($pendingPrescriptions); ?></p>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($totalMedicines == 0 && $totalInventory == 0): ?>
            <div class="welcome-message">
                <h3>🏥 Welcome to Pharmacy Pro!</h3>
                <p>Your advanced pharmacy management system is ready to use. It looks like you haven't imported the pharmacy database yet.</p>
                <p>Click below to set up your pharmacy database and start managing your medications and inventory.</p>

                <div class="welcome-actions">
                    <a href="../medicines/list.php" class="welcome-btn">
                        <i class="fas fa-capsules"></i>
                        Browse Medicines
                    </a>
                </div>
            </div>
            <?php else: ?>
            <div class="welcome-message">
                <h3>🎯 Ready to Manage Your Pharmacy</h3>
                <p>Your pharmacy system is active with <?php echo number_format($totalMedicines); ?> medicines and <?php echo number_format($totalInventory); ?> inventory items.</p>

                <div class="welcome-actions">
                    <a href="../medicines/list.php" class="welcome-btn">
                        <i class="fas fa-capsules"></i>
                        Manage Medicines
                    </a>
                    <a href="../inventory/" class="welcome-btn">
                        <i class="fas fa-boxes"></i>
                        View Inventory
                    </a>
                </div>
            </div>
            <?php endif; ?>
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

            // Update toggle button on load
            const toggleBall = document.querySelector('.toggle-ball');
            if (savedTheme === 'dark') {
                toggleBall.style.transform = 'translateX(20px)';
            }
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
