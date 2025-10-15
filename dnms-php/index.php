<?php
/**
 * DNMS-PHP Enhanced Landing Page with LIS Styling
 * Diet & Nutrition Management System
 */

// Prevent direct access issues
if (!defined('DNMS_ENTRY_POINT')) {
    define('DNMS_ENTRY_POINT', true);
}

// Load main config for authentication functions only (don't restart session)
try {
    require_once __DIR__ . '/../config.php';
} catch (Exception $e) {
    // If main config fails, redirect to setup
    header('Location: ../database_setup.php');
    exit();
}

// Load DNMS config
try {
    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/config/constants.php';
} catch (Exception $e) {
    die("DNMS Configuration Error: " . $e->getMessage());
}

// Load includes with error handling
try {
    require_once __DIR__ . '/includes/auth_functions.php';
} catch (Exception $e) {
    error_log("DNMS Auth Functions Error: " . $e->getMessage());
    // Continue without auth functions - use main system functions
}

// Check if user is logged in using main system's session
if (isset($_SESSION['user_id']) && function_exists('isLoggedIn') && isLoggedIn()) {
    // Redirect to dashboard
    header('Location: controllers/user/dashboard.php');
    exit();
}

// Test database connection
try {
    $dbTest = testDNMSDatabaseConnection();
    $dbConnected = $dbTest['status'] === 'success';
} catch (Exception $e) {
    $dbConnected = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🥗 NutriTrack Pro - Advanced Nutrition Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="top-header">
        <div class="header-left">
            <button class="menu-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div class="brand">
                <span class="brand-name">NutriTrack Pro</span>
            </div>
        </div>
        <div class="header-right">
            <div class="user-name">
                <i class="fas fa-user"></i>
                <span>Admin</span>
            </div>
            <a href="../logout.php" class="btn btn-secondary btn-sm">Logout</a>
        </div>
    </nav>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <nav class="sidebar-nav">
            <a href="#features" class="nav-item">
                <i class="fas fa-star"></i>
                <span>Features</span>
            </a>
            <a href="#stats" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>About</span>
            </a>
            <a href="../login.php" class="nav-item">
                <i class="fas fa-sign-in-alt"></i>
                <span>Login</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <div class="dashboard-page">
            <div class="content">

                <div class="page-header">
                    <h1>🥗 NutriTrack Pro</h1>
                    <p>Advanced nutrition management system for tracking meals, managing goals, and achieving optimal health through intelligent diet planning</p>
                </div>

                <div class="hero-buttons">
                    <a href="../login.php" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i>
                        Get Started
                    </a>
                    <a href="#features" class="btn btn-secondary">
                        <i class="fas fa-eye"></i>
                        Learn More
                    </a>
                </div>

                <!-- Features Section -->
                <section class="section-header">
                    <h2>Powerful Features</h2>
                    <p>Discover what makes NutriTrack Pro the ultimate nutrition management solution</p>
                </section>

                <div class="status-grid">
                    <div class="status-card">
                        <div class="status-header">
                            <h3>Food Database</h3>
                        </div>
                        <div class="status-body">
                            <p>Comprehensive database of foods with detailed nutritional information including calories, macronutrients, and micronutrients.</p>
                        </div>
                    </div>

                    <div class="status-card">
                        <div class="status-header">
                            <h3>Meal Tracking</h3>
                        </div>
                        <div class="status-body">
                            <p>Log your meals with portion sizes and get real-time nutritional analysis and goal tracking.</p>
                        </div>
                    </div>

                    <div class="status-card">
                        <div class="status-header">
                            <h3>Progress Analytics</h3>
                        </div>
                        <div class="status-body">
                            <p>Comprehensive insights into your nutrition journey with detailed charts, trends, and goal tracking.</p>
                        </div>
                    </div>

                    <div class="status-card">
                        <div class="status-header">
                            <h3>Goal Management</h3>
                        </div>
                        <div class="status-body">
                            <p>Set nutrition goals, track achievements, and get personalized recommendations for success.</p>
                        </div>
                    </div>

                    <div class="status-card">
                        <div class="status-header">
                            <h3>Smart Alerts</h3>
                        </div>
                        <div class="status-body">
                            <p>Automated notifications for meal times, goal achievements, and nutrition insights.</p>
                        </div>
                    </div>

                    <div class="status-card">
                        <div class="status-header">
                            <h3>Mobile Responsive</h3>
                        </div>
                        <div class="status-body">
                            <p>Beautiful, responsive design that works perfectly on all devices and screen sizes.</p>
                        </div>
                    </div>
                </div>
                <!-- Stats Section -->
                <section class="section-header">
                    <h2>About NutriTrack Pro</h2>
                    <p>Key statistics and information about our nutrition management platform</p>
                </section>

                <div class="stats-cards">
                    <div class="stat-box">
                        <div class="stat-value"><?php echo $dbConnected ? '1000+' : '∞'; ?></div>
                        <div class="stat-label">Food Database</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value">Real-time</div>
                        <div class="stat-label">Nutrition Tracking</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value">24/7</div>
                        <div class="stat-label">Monitoring</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value"><?php echo $dbConnected ? '✓' : '∞'; ?></div>
                        <div class="stat-label">Database Connected</div>
                    </div>
                </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-brand">
                        <i class="fas fa-seedling"></i>
                        <h3>NutriTrack Pro</h3>
                    </div>
                    <p>Your ultimate companion for intelligent nutrition management and health tracking.</p>
                    <div class="social-links">
                        <a href="#" class="social-link" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-link" title="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-link" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link" title="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>

                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="#features">Features</a></li>
                        <li><a href="#stats">About</a></li>
                        <li><a href="../login.php">Login</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Support</h4>
                    <ul class="footer-links">
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Community</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Contact Info</h4>
                    <div class="contact-info">
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <span>support@nutritrackpro.com</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <span>+1 (555) 123-4567</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>San Francisco, CA</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2024 NutriTrack Pro. All rights reserved. | Made with ❤️ for healthier lives</p>
            </div>
        </div>
    </footer>

    <script>
        // Sidebar Toggle with proper state management
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleButton = document.querySelector('.menu-toggle i');

            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');

            // Update toggle icon
            if (sidebar.classList.contains('collapsed')) {
                toggleButton.classList.remove('fa-bars');
                toggleButton.classList.add('fa-times');
            } else {
                toggleButton.classList.remove('fa-times');
                toggleButton.classList.add('fa-bars');
            }
        }

        // Close sidebar when clicking outside (mobile)
        function closeSidebarOnOutsideClick() {
            document.addEventListener('click', function(event) {
                const sidebar = document.getElementById('sidebar');
                const toggleButton = document.querySelector('.menu-toggle');

                if (!sidebar.contains(event.target) && !toggleButton.contains(event.target)) {
                    if (!sidebar.classList.contains('collapsed')) {
                        toggleSidebar();
                    }
                }
            });
        }

        // Add click handlers for feature cards
        function addFeatureInteractions() {
            const featureCards = document.querySelectorAll('.status-card');
            featureCards.forEach(card => {
                card.addEventListener('click', function() {
                    const title = this.querySelector('h3').textContent;
                    showNotification(`Learn more about ${title}!`, 'info');
                });
            });
        }

        // Enhanced notification system with standardized colors
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            const colors = {
                success: 'linear-gradient(135deg, #10b981 0%, #059669 100%)', // Green for success
                error: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',   // Red for error
                warning: 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', // Orange for warning
                info: 'linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)'     // Blue for info (matching primary)
            };

            notification.style.cssText = `
                position: fixed;
                top: 80px;
                right: 20px;
                background: ${colors[type]};
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                z-index: 10001;
                max-width: 300px;
                font-family: 'Inter', sans-serif;
                animation: slideInRight 0.3s ease;
            `;

            notification.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-${getIcon(type)}" style="font-size: 16px;"></i>
                    <p style="margin: 0; font-size: 14px; line-height: 1.4;">${message}</p>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.animation = 'slideInRight 0.3s ease reverse';
                    setTimeout(() => notification.parentNode.removeChild(notification), 300);
                }
            }, 3000);
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

        // Initialize all functionality on page load
        document.addEventListener('DOMContentLoaded', function() {
            addFeatureInteractions();
            closeSidebarOnOutsideClick();

            // Add smooth scrolling for navigation links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Add keyboard navigation for sidebar
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const sidebar = document.getElementById('sidebar');
                    if (!sidebar.classList.contains('collapsed')) {
                        toggleSidebar();
                    }
                }
            });
        });
    </script>
</body>
</html>
