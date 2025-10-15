<?php
// Load PMS config first
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/constants.php';

$searchTerm = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';

$prescription = new Prescription();
if ($searchTerm || $statusFilter) {
    // For now, just get all prescriptions since search functionality needs to be implemented
    $prescriptions = $prescription->getAll();
} else {
    $prescriptions = $prescription->getAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>💊 Pharmacy Pro - Prescriptions Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Enhanced PMS Prescriptions List with Dark Mode */
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

        /* Enhanced Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
            background: var(--card-bg);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
        }

        .data-table thead {
            background: var(--pharmacy-gradient);
            color: white;
        }

        .data-table th {
            padding: 1.5rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border: none;
        }

        .data-table td {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        .data-table tbody tr {
            transition: all 0.3s ease;
        }

        .data-table tbody tr:hover {
            background: var(--bg-secondary);
            transform: translateX(4px);
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Status Badges */
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-active {
            background: rgba(72, 187, 120, 0.2);
            color: #10b981;
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
        }

        .status-completed {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
        }

        .status-cancelled {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        /* Control Buttons */
        .control-btn {
            padding: 0.75rem 2rem;
            background: var(--pharmacy-gradient);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .control-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 107, 157, 0.3);
        }

        .btn-small {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            background: var(--pharmacy-gradient);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        /* Search and Filter Section */
        .controls-section {
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
        }

        .controls-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            align-items: end;
        }

        .control-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            position: relative;
        }

        .control-label {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.875rem;
        }

        .control-input, .control-select {
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            background: var(--card-bg);
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .control-input:focus, .control-select:focus {
            outline: none;
            border-color: #ff6b9d;
            box-shadow: 0 0 0 3px rgba(255, 107, 157, 0.1);
        }

        /* Enhanced No Data Message */
        .no-data {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 4rem 2rem;
            text-align: center;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
        }

        .no-data h3 {
            font-size: 2rem;
            color: var(--text-primary);
            margin-bottom: 1rem;
            background: var(--pharmacy-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .no-data p {
            color: var(--text-secondary);
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .no-data-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .no-data-btn {
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

        .no-data-btn:hover {
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

            .controls-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .data-table {
                font-size: 0.9rem;
            }

            .data-table th, .data-table td {
                padding: 0.75rem;
            }

            .no-data {
                padding: 3rem 1rem;
            }

            .no-data-actions {
                flex-direction: column;
                align-items: center;
            }

            .footer-links {
                flex-direction: column;
                gap: 1rem;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .data-table tbody tr {
            animation: fadeInUp 0.6s ease forwards;
        }
    </style>
</head>
<body>
    <!-- Sidebar Navigation -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>💊 Pharmacy </h2>
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
            <div class="nav-brand">
                Pharmacy 
            </div>
            <div class="nav-menu">
                <a href="add.php">
                    <i class="fas fa-plus"></i>
                    Add Prescription
                </a>
                <a href="../dashboard/">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
                <a href="../../../logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>

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
                <h1>💊 Prescriptions Management</h1>
                <p>Manage patient prescriptions and check for drug interactions</p>
            </div>

            <?php if (empty($prescriptions)): ?>
                <div class="no-data">
                    <h3>🏥 No Prescriptions Found</h3>
                    <p>Your pharmacy database doesn't have any prescriptions yet.</p>
                    <p>Add prescriptions to start managing patient medications.</p>

                    <div class="no-data-actions">
                        <a href="add.php" class="no-data-btn">
                            <i class="fas fa-plus"></i>
                            Add New Prescription
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Search and Filter Controls -->
                <div class="controls-section">
                    <div class="controls-grid">
                        <div class="control-group">
                            <label class="control-label">Search Prescriptions</label>
                            <input type="text" class="control-input" id="searchInput" placeholder="Search by patient, doctor..." onkeyup="filterPrescriptions()">
                        </div>
                        <div class="control-group">
                            <label class="control-label">Status Filter</label>
                            <select class="control-select" id="statusFilter" onchange="filterPrescriptions()">
                                <option value="">All Statuses</option>
                                <option value="active">Active</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="control-group">
                            <button class="control-btn" onclick="clearFilters()">
                                <i class="fas fa-filter"></i>
                                Clear Filters
                            </button>
                        </div>
                        <div class="control-group">
                            <button class="control-btn" onclick="exportPrescriptions()" style="background: #10b981;">
                                <i class="fas fa-download"></i>
                                Export Data
                            </button>
                        </div>
                    </div>
                </div>

                <div class="table-container">
                    <table class="data-table" id="prescriptionsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient ID</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($prescriptions as $pres): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($pres['id']); ?></strong></td>
                                <td><?php echo htmlspecialchars($pres['patient_id']); ?></td>
                                <td><?php echo htmlspecialchars($pres['doctor_name']); ?></td>
                                <td><?php echo htmlspecialchars($pres['prescription_date']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($pres['status']); ?>">
                                        <?php echo ucfirst($pres['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="edit.php?id=<?php echo $pres['id']; ?>" class="btn-small" style="background: #3b82f6;">
                                        <i class="fas fa-edit"></i>
                                        Edit
                                    </a>
                                    <a href="view.php?id=<?php echo $pres['id']; ?>" class="btn-small" style="background: #10b981;">
                                        <i class="fas fa-eye"></i>
                                        View
                                    </a>
                                    <button class="btn-small" style="background: #dc3545;" onclick="deletePrescription(<?php echo $pres['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
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

        // Search and Filter Functionality
        function filterPrescriptions() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value.toLowerCase();

            // Filter table rows
            const tableRows = document.querySelectorAll('#prescriptionsTable tbody tr');
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const status = row.cells[4]?.textContent.toLowerCase() || '';

                const matchesSearch = text.includes(searchTerm);
                const matchesStatus = !statusFilter || status.includes(statusFilter);

                if (matchesSearch && matchesStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            showNotification(`Filtered to ${document.querySelectorAll('#prescriptionsTable tbody tr[style*="display: none"]').length} hidden items`, 'info');
        }

        function clearFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';
            filterPrescriptions();
            showNotification('Filters cleared!', 'info');
        }

        function deletePrescription(id) {
            if (confirm('Are you sure you want to delete this prescription?')) {
                // Simulate delete (in real app, send to server)
                const row = document.querySelector(`#prescriptionsTable tbody tr td:first-child`);
                if (row && row.textContent == id) {
                    row.closest('tr').remove();
                    showNotification('Prescription deleted!', 'success');
                }
            }
        }

        function exportPrescriptions() {
            showNotification('Exporting prescription data...', 'info');
            // In a real application, this would trigger a server-side export
            setTimeout(() => {
                showNotification('Prescription data exported!', 'success');
            }, 1500);
        }

        // Initialize theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializeTheme();

            // Add event listeners for real-time filtering
            document.getElementById('searchInput').addEventListener('input', filterPrescriptions);
            document.getElementById('statusFilter').addEventListener('change', filterPrescriptions);
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
