<?php
/**
 * PMS-PHP Prescription View
 * View individual prescription details
 */

// Load PMS config
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/constants.php';

// Load main config for authentication
require_once __DIR__ . '/../../../config.php';
requireLogin();

// Get prescription ID from URL
$prescriptionId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$prescriptionId) {
    header('Location: index.php');
    exit;
}

// Connect to database and get prescription
try {
    $conn = getPMSDBConnection();
    $stmt = $conn->prepare("SELECT * FROM pms_prescriptions WHERE id = ?");
    $stmt->execute([$prescriptionId]);
    $prescription = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$prescription) {
        header('Location: index.php');
        exit;
    }

    $conn = null;
} catch (Exception $e) {
    $prescription = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>💊 Prescription Details - Pharmacy Pro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Enhanced PMS Prescription View with Dark Mode */
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

        /* Prescription Details */
        .prescription-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .prescription-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
        }

        .prescription-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border-color);
        }

        .prescription-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .prescription-actions {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 107, 157, 0.3);
        }

        .btn-secondary {
            background: var(--bg-secondary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--border-color);
        }

        /* Info Rows */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .info-card {
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid var(--border-color);
        }

        .info-row {
            display: flex;
            margin-bottom: 1.5rem;
            align-items: flex-start;
        }

        .info-label {
            width: 120px;
            font-weight: 600;
            color: var(--text-primary);
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .info-value {
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-filled {
            background: rgba(72, 187, 120, 0.2);
            color: #10b981;
        }

        .status-cancelled {
            background: rgba(245, 101, 101, 0.2);
            color: #ef4444;
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
        }

        /* Medicines Display */
        .medicines-display {
            background: var(--bg-tertiary);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            white-space: pre-line;
            line-height: 1.6;
        }

        /* Action Cards */
        .action-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
        }

        .action-card h3 {
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .action-btn {
            padding: 1rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-secondary);
            color: var(--text-primary);
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            justify-content: center;
        }

        .action-btn:hover {
            background: var(--primary-gradient);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 107, 157, 0.3);
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

            .info-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .prescription-actions {
                flex-direction: column;
            }

            .action-grid {
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
            <h2>💊 Pharmacy </h2>
            <p>Prescription Management</p>
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
            <a href="../reports/">
                <i class="fas fa-chart-line"></i>
                Reports
            </a>
            <a href="../prescriptions/" class="active">
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
            <div class="nav-brand">Pharmacy</div>
            <div class="nav-menu">
                <a href="index.php">
                    <i class="fas fa-list"></i>
                    Prescriptions List
                </a>
                <a href="add.php">
                    <i class="fas fa-plus"></i>
                    Add Prescription
                </a>
            </div>
        </nav>

        <div class="content">
            <div class="page-header">
                <h1>💊 Prescription Details</h1>
                <p>View detailed information about this prescription</p>
            </div>

            <div class="prescription-container">
                <!-- Prescription Header -->
                <div class="prescription-card">
                    <div class="prescription-header">
                        <h2 class="prescription-title">Prescription #<?php echo htmlspecialchars($prescription['id']); ?></h2>
                        <div class="prescription-actions">
                            <a href="edit.php?id=<?php echo $prescription['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-edit"></i>
                                Edit Prescription
                            </a>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                Back to List
                            </a>
                        </div>
                    </div>

                    <div class="info-grid">
                        <!-- Patient Information -->
                        <div class="info-card">
                            <h3 style="color: var(--text-primary); margin-bottom: 1.5rem;">
                                <i class="fas fa-user" style="color: var(--primary-gradient); margin-right: 0.5rem;"></i>
                                Patient Information
                            </h3>

                            <div class="info-row">
                                <div class="info-label">Patient ID:</div>
                                <div class="info-value"><?php echo htmlspecialchars($prescription['patient_id'] ?? 'N/A'); ?></div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Full Name:</div>
                                <div class="info-value">
                                    <?php
                                    $fullName = '';
                                    if (isset($prescription['first_name']) && isset($prescription['last_name'])) {
                                        $fullName = htmlspecialchars($prescription['first_name'] . ' ' . $prescription['last_name']);
                                    }
                                    echo $fullName ?: 'N/A';
                                    ?>
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Status:</div>
                                <div class="info-value">
                                    <span class="status-badge status-<?php echo strtolower($prescription['status'] ?? 'pending'); ?>">
                                        <?php echo ucfirst($prescription['status'] ?? 'Pending'); ?>
                                    </span>
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Date:</div>
                                <div class="info-value">
                                    <?php
                                    if (isset($prescription['created_at'])) {
                                        echo date('M d, Y \a\t H:i', strtotime($prescription['created_at']));
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <!-- Doctor Information -->
                        <div class="info-card">
                            <h3 style="color: var(--text-primary); margin-bottom: 1.5rem;">
                                <i class="fas fa-user-md" style="color: var(--primary-gradient); margin-right: 0.5rem;"></i>
                                Doctor Information
                            </h3>

                            <div class="info-row">
                                <div class="info-label">Doctor Name:</div>
                                <div class="info-value">
                                    <?php
                                    $doctorName = '';
                                    if (isset($prescription['doctor_fname']) && isset($prescription['doctor_lname'])) {
                                        $doctorName = htmlspecialchars($prescription['doctor_fname'] . ' ' . $prescription['doctor_lname']);
                                    }
                                    echo $doctorName ?: 'N/A';
                                    ?>
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">License:</div>
                                <div class="info-value"><?php echo htmlspecialchars($prescription['doctor_license'] ?? 'N/A'); ?></div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Contact:</div>
                                <div class="info-value"><?php echo htmlspecialchars($prescription['doctor_contact'] ?? 'N/A'); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Prescription Details -->
                    <div class="info-card" style="margin-top: 2rem;">
                        <h3 style="color: var(--text-primary); margin-bottom: 1.5rem;">
                            <i class="fas fa-pills" style="color: var(--primary-gradient); margin-right: 0.5rem;"></i>
                            Prescription Details
                        </h3>

                        <div class="info-row">
                            <div class="info-label">Medicines:</div>
                            <div class="info-value">
                                <div class="medicines-display">
                                    <?php echo nl2br(htmlspecialchars($prescription['medicines'] ?? 'No medicines specified')); ?>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($prescription['notes'])): ?>
                        <div class="info-row">
                            <div class="info-label">Notes:</div>
                            <div class="info-value">
                                <div class="medicines-display">
                                    <?php echo nl2br(htmlspecialchars($prescription['notes'])); ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Action Cards -->
                <div class="action-card">
                    <h3><i class="fas fa-cogs"></i> Actions</h3>
                    <div class="action-grid">
                        <button class="action-btn" onclick="fillPrescription()">
                            <i class="fas fa-check-circle"></i>
                            Mark as Filled
                        </button>
                        <button class="action-btn" onclick="cancelPrescription()">
                            <i class="fas fa-times-circle"></i>
                            Cancel Prescription
                        </button>
                        <button class="action-btn" onclick="printPrescription()">
                            <i class="fas fa-print"></i>
                            Print Prescription
                        </button>
                        <a href="../sales/" class="action-btn">
                            <i class="fas fa-shopping-cart"></i>
                            Create Sale
                        </a>
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
        }

        function fillPrescription() {
            if (confirm('Mark this prescription as filled?')) {
                showNotification('Prescription marked as filled!', 'success');
                // In real app, send request to server
            }
        }

        function cancelPrescription() {
            if (confirm('Cancel this prescription? This action cannot be undone.')) {
                showNotification('Prescription cancelled!', 'warning');
                // In real app, send request to server
            }
        }

        function printPrescription() {
            window.print();
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
