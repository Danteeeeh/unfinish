<?php
/**
 * PMS-PHP Reports Module
 * Advanced reporting and analytics for pharmacy management
 */

// Load PMS config
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/constants.php';

// Load main config for authentication
require_once __DIR__ . '/../../../config.php';
requireLogin();

// Connect to database
$conn = getPMSDBConnection();

// Get report data
try {
    $totalMedicines = $conn->query("SELECT COUNT(*) as count FROM pms_medicines")->fetch()['count'];
    $totalInventory = $conn->query("SELECT COUNT(*) as count FROM pms_inventory")->fetch()['count'];
    $lowStockCount = $conn->query("SELECT COUNT(*) as count FROM pms_inventory WHERE quantity <= 10")->fetch()['count'];
    $expiredCount = $conn->query("SELECT COUNT(*) as count FROM pms_inventory WHERE expiry_date < CURDATE()")->fetch()['count'];

    // Get top medicines by value
    $topMedicines = $conn->query("
        SELECT m.generic_name, SUM(i.quantity * m.price) as total_value
        FROM pms_inventory i
        JOIN pms_medicines m ON i.medicine_id = m.id
        GROUP BY m.id, m.generic_name
        ORDER BY total_value DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    $conn = null;
} catch (Exception $e) {
    $totalMedicines = 0;
    $totalInventory = 0;
    $lowStockCount = 0;
    $expiredCount = 0;
    $topMedicines = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📊 PMS Reports - Advanced Analytics</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js">
    <style>
        /* Advanced PMS Reports with Dark Mode */
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

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
        }

        .stat-icon {
            width: 80px;
            height: 80px;
            background: var(--primary-gradient);
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
        }

        .stat-card p {
            font-size: 3rem;
            font-weight: 800;
            margin: 0;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Chart Section */
        .chart-section {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
        }

        .chart-section h3 {
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        /* Top Medicines Table */
        .top-medicines {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
        }

        .top-medicines h3 {
            margin-bottom: 1.5rem;
            color: var(--text-primary);
        }

        .top-medicines-table {
            width: 100%;
            border-collapse: collapse;
        }

        .top-medicines-table th,
        .top-medicines-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .top-medicines-table th {
            background: var(--primary-gradient);
            color: white;
            font-weight: 600;
        }

        .top-medicines-table tbody tr:hover {
            background: var(--bg-secondary);
        }

        /* Controls */
        .controls-section {
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
        }

        .controls-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            align-items: end;
        }

        .control-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .control-label {
            font-weight: 600;
            color: var(--text-primary);
        }

        .control-input {
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            background: var(--card-bg);
            color: var(--text-primary);
        }

        .control-btn {
            padding: 0.75rem 2rem;
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .control-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 107, 157, 0.3);
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

            .controls-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
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
            <h2>🏥 Pharmacy Pro</h2>
            <p>Professional Management</p>
        </div>
        <div class="sidebar-nav">
            <a href="../dashboard/index.php">
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
            <a href="../reports/" class="active">
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
                Pharmacy Pro
            </div>
            <div class="nav-menu">
                <a href="../dashboard/">
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
                <a href="../../../logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
        </nav>

        <div class="content">
            <div class="page-header">
                <h1>📊 Advanced Reports & Analytics</h1>
                <p>Comprehensive insights into your pharmacy operations with detailed reports and visualizations</p>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-pills"></i>
                    </div>
                    <h3>Total Medicines</h3>
                    <p><?php echo number_format($totalMedicines); ?></p>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h3>Total Inventory Items</h3>
                    <p><?php echo number_format($totalInventory); ?></p>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3>Low Stock Items</h3>
                    <p><?php echo number_format($lowStockCount); ?></p>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <h3>Expired Items</h3>
                    <p><?php echo number_format($expiredCount); ?></p>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="chart-section">
                <h3>Inventory Overview</h3>
                <canvas id="overviewChart" width="400" height="200"></canvas>
            </div>

            <!-- Top Medicines Table -->
            <div class="top-medicines">
                <h3>Top Medicines by Value</h3>
                <?php if (empty($topMedicines)): ?>
                    <p>No data available.</p>
                <?php else: ?>
                    <table class="top-medicines-table">
                        <thead>
                            <tr>
                                <th>Medicine Name</th>
                                <th>Total Value (₱)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topMedicines as $medicine): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($medicine['generic_name']); ?></td>
                                    <td><strong>₱<?php echo number_format($medicine['total_value'], 2); ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- Report Controls -->
            <div class="controls-section">
                <div class="controls-grid">
                    <div class="control-group">
                        <label class="control-label">Generate Report</label>
                        <select class="control-input" id="reportType">
                            <option value="inventory">Inventory Report</option>
                            <option value="sales">Sales Report</option>
                            <option value="expiry">Expiry Report</option>
                        </select>
                    </div>
                    <div class="control-group">
                        <button class="control-btn" onclick="generateReport()">
                            <i class="fas fa-file-pdf"></i>
                            Generate PDF
                        </button>
                    </div>
                    <div class="control-group">
                        <button class="control-btn" onclick="exportData()">
                            <i class="fas fa-download"></i>
                            Export Data
                        </button>
                    </div>
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Initialize theme
        function initializeTheme() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.toggle('open');
            mainContent.classList.toggle('sidebar-open');
        }

        function generateReport() {
            const reportType = document.getElementById('reportType').value;
            showNotification(`${reportType.charAt(0).toUpperCase() + reportType.slice(1)} report generated!`, 'success');
            // In real app, send request to server
        }

        function exportData() {
            showNotification('Data exported successfully!', 'success');
            // In real app, trigger download
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            initializeTheme();
            renderOverviewChart();

            // Add staggered animations to cards
            const cards = document.querySelectorAll('.stat-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });
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
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}" style="font-size: 18px;"></i>
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

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            initializeTheme();
            renderOverviewChart();

            // Add CSS animations
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
        });
    </script>
</body>
</html>
