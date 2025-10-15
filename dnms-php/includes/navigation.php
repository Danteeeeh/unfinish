<?php
/**
 * DNMS Navigation Include with LIS Styling
 * Enhanced navigation bar for DNMS pages
 */

// Only show navigation if user is logged in
if (!isset($_SESSION['user_id'])) {
    return;
}
?>

<!-- Enhanced DNMS Navigation with Sidebar -->
<div class="top-header">
    <div class="header-left">
        <button class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="brand">
            <span class="brand-name">NutriTrack Pro</span>
            <span class="brand-module">DNMS</span>
        </div>
    </div>
    <div class="header-right">
        <a href="/id-login-admin/dashboard.php" class="btn btn-primary btn-sm">🏠 Main Dashboard</a>
        <span class="user-name"><i class="fas fa-user-circle"></i> <?php echo getCurrentUser()['full_name'] ?? 'User'; ?></span>

        <!-- Theme Toggle -->
        <div class="theme-toggle" onclick="toggleTheme()" title="Toggle Dark Mode">
            <i class="fas fa-sun"></i>
            <div class="toggle-ball"></div>
            <i class="fas fa-moon"></i>
        </div>
    </div>
</div>

<!-- Enhanced Sidebar -->
<aside class="sidebar" id="sidebar">
    <nav class="sidebar-nav">
        <a href="controllers/user/dashboard.php" class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], 'dashboard.php') !== false) ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        <a href="controllers/food/search.php" class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], 'food') !== false) ? 'active' : ''; ?>">
            <i class="fas fa-search"></i>
            <span>Food Search</span>
        </a>
        <a href="controllers/meal/log.php" class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], 'meal') !== false) ? 'active' : ''; ?>">
            <i class="fas fa-utensils"></i>
            <span>Meal Logging</span>
        </a>
        <a href="controllers/recipes/index.php" class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], 'recipe') !== false) ? 'active' : ''; ?>">
            <i class="fas fa-book-open"></i>
            <span>Recipes</span>
        </a>
        <a href="controllers/shopping/index.php" class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], 'shopping') !== false) ? 'active' : ''; ?>">
            <i class="fas fa-shopping-cart"></i>
            <span>Shopping Lists</span>
        </a>
        <a href="controllers/exercise/index.php" class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], 'exercise') !== false) ? 'active' : ''; ?>">
            <i class="fas fa-dumbbell"></i>
            <span>Exercise</span>
        </a>
        <a href="controllers/water/index.php" class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], 'water') !== false) ? 'active' : ''; ?>">
            <i class="fas fa-tint"></i>
            <span>Water Tracking</span>
        </a>
        <a href="controllers/measurements/index.php" class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], 'measurement') !== false) ? 'active' : ''; ?>">
            <i class="fas fa-weight"></i>
            <span>Measurements</span>
        </a>
        <a href="controllers/goals/index.php" class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], 'goal') !== false) ? 'active' : ''; ?>">
            <i class="fas fa-bullseye"></i>
            <span>Goals</span>
        </a>
        <a href="controllers/reports/index.php" class="nav-item <?php echo (strpos($_SERVER['REQUEST_URI'], 'report') !== false) ? 'active' : ''; ?>">
            <i class="fas fa-chart-bar"></i>
            <span>Reports</span>
        </a>
    </nav>
</aside>

<!-- Enhanced Main Content Area -->
<main class="main-content" id="mainContent">
    <?php displayFlashMessage(); ?>

    <style>
        /* Enhanced Sidebar Styling */
        .sidebar {
            width: 280px;
            background: var(--bg-secondary);
            position: fixed;
            left: 0;
            top: 80px;
            height: calc(100vh - 80px);
            overflow-y: auto;
            border-right: 1px solid var(--border-color);
            transition: transform 0.3s ease, background-color 0.3s ease;
            z-index: 999;
        }

        .sidebar-nav {
            padding: 2rem 1rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            margin-bottom: 0.5rem;
            border-radius: 12px;
            text-decoration: none;
            color: var(--text-secondary);
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--nutrition-gradient);
            transition: left 0.3s ease;
            z-index: -1;
            opacity: 0.1;
        }

        .nav-item:hover::before {
            left: 0;
        }

        .nav-item:hover {
            color: var(--text-primary);
            background: rgba(78, 205, 196, 0.1);
            transform: translateX(8px);
        }

        .nav-item.active {
            background: var(--nutrition-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(78, 205, 196, 0.3);
        }

        .nav-item i {
            font-size: 1.2rem;
            width: 20px;
            text-align: center;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0 !important;
            }
        }
    </style>

    <script>
        // Sidebar toggle functionality
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });

        // Theme Management
        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);

            // Update toggle button
            const toggleBall = document.querySelector('.toggle-ball');
            if (newTheme === 'dark') {
                toggleBall.style.transform = 'translateX(20px)';
            } else {
                toggleBall.style.transform = 'translateX(0)';
            }

            showNotification(`Switched to ${newTheme} mode!`, 'success');
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
                success: 'linear-gradient(135deg, #48bb78 0%, #38a169 100%)',
                error: 'linear-gradient(135deg, #f56565 0%, #e53e3e 100%)',
                warning: 'linear-gradient(135deg, #ed8936 0%, #dd6b20 100%)',
                info: 'linear-gradient(135deg, #3182ce 0%, #2b6cb0 100%)'
            };

            notification.style.cssText = `
                position: fixed;
                top: 100px;
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
