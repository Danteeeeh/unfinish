<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ' - ' : ''; ?>RIS - Radiology Information System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
        /* Enhanced RIS Styling with LIS Design Pattern */
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            --ris-gradient: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);

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
            --sidebar-bg: #f8f9fa;
        }

        /* Dark Theme Variables */
        [data-theme="dark"] {
            --primary-gradient: linear-gradient(135deg, #9f7aea 0%, #667eea 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            --warning-gradient: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
            --danger-gradient: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
            --ris-gradient: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);

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
            --sidebar-bg: #2d2d2d;
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
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Enhanced Top Header */
        .top-header {
            background: var(--navbar-bg);
            backdrop-filter: blur(20px);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: var(--shadow-sm);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .menu-toggle {
            background: var(--bg-secondary);
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.2rem;
        }

        .menu-toggle:hover {
            background: var(--border-color);
            transform: scale(1.05);
        }

        .brand {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .brand-name {
            font-size: 1.8rem;
            font-weight: 800;
            background: var(--ris-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-module {
            font-size: 0.9rem;
            color: var(--text-secondary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--ris-gradient);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        /* Enhanced Sidebar */
        .sidebar {
            width: 280px;
            background: var(--sidebar-bg);
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
            background: var(--ris-gradient);
            transition: left 0.3s ease;
            z-index: -1;
            opacity: 0.1;
        }

        .nav-item:hover::before {
            left: 0;
        }

        .nav-item:hover {
            color: var(--text-primary);
            background: rgba(37, 99, 235, 0.1);
            transform: translateX(8px);
        }

        .nav-item.active {
            background: var(--ris-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        .nav-item i {
            font-size: 1.2rem;
            width: 20px;
            text-align: center;
        }

        /* Enhanced Main Content */
        .main-content {
            margin-left: 280px;
            margin-top: 80px;
            padding: 2rem;
            min-height: calc(100vh - 80px);
            background: var(--bg-primary);
            transition: background-color 0.3s ease;
        }

        /* Enhanced Dashboard Page Styling */
        .dashboard-page {
            max-width: 1400px;
            margin: 0 auto;
        }

        .page-title {
            font-size: 3rem;
            font-weight: 800;
            background: var(--ris-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 2rem;
            text-align: center;
            letter-spacing: -0.02em;
        }

        /* Enhanced Dashboard Cards with Modern Design */
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .stat-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .stat-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4facfe, #00f2fe);
        }

        .stat-box:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
        }

        .stat-box:nth-child(2) {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stat-box:nth-child(3) {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .stat-label {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 1rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 3.5rem;
            font-weight: 800;
            color: white;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            margin-bottom: 0.5rem;
        }

        .stat-description {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 400;
        }

        /* Enhanced Activity Section */
        .activity-container {
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
            overflow: hidden;
            margin-bottom: 3rem;
        }

        .activity-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .activity-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .activity-feed {
            padding: 0;
        }

        .activity-card {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .activity-card:last-child {
            border-bottom: none;
        }

        .activity-card:hover {
            background: var(--bg-secondary);
            transform: translateX(5px);
        }

        .activity-icon-wrapper {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.4rem;
            box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);
        }

        .activity-info {
            flex: 1;
        }

        .activity-title {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .activity-desc {
            color: var(--text-secondary);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* Enhanced Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .action-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            box-shadow: var(--shadow-lg);
            border: 2px solid transparent;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            color: var(--text-primary);
            position: relative;
            overflow: hidden;
        }

        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
            transition: left 0.5s;
        }

        .action-card:hover::before {
            left: 100%;
        }

        .action-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border-color: rgba(102, 126, 234, 0.3);
        }

        .action-card h4 {
            font-size: 1.4rem;
            margin-bottom: 1rem;
            color: var(--primary-gradient);
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .action-card p {
            color: var(--text-secondary);
            margin: 0;
            font-size: 1rem;
            line-height: 1.5;
        }

        .action-card i {
            font-size: 1.8rem;
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
            border-color: #2563eb;
            transform: scale(1.05);
        }

        .theme-toggle .toggle-ball {
            width: 18px;
            height: 18px;
            background: var(--ris-gradient);
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

        .no-studies {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-secondary);
        }

        .no-studies h3 {
            font-size: 2rem;
            color: var(--text-primary);
            margin-bottom: 1rem;
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
                margin-left: 0;
            }
        }

        @media (max-width: 768px) {
            .top-header {
                padding: 1rem;
            }

            .header-right {
                gap: 1rem;
            }

            .user-name {
                display: none;
            }

            .main-content {
                padding: 1rem;
            }

            .page-title {
                font-size: 2.5rem;
            }

            .stats-cards {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .activity-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
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

        .stat-box {
            animation: fadeInUp 0.6s ease forwards;
        }

        .stat-box:nth-child(1) { animation-delay: 0.1s; }
        .stat-box:nth-child(2) { animation-delay: 0.2s; }
        .stat-box:nth-child(3) { animation-delay: 0.3s; }
        .stat-box:nth-child(4) { animation-delay: 0.4s; }
    </style>
</head>
<body>

    <!-- Top Header -->
    <header class="top-header">
        <div class="header-left">
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="brand">
                <span class="brand-name">RIS</span>
                <span class="brand-module">Radiology Information System</span>
            </div>
        </div>
        <div class="header-right">
            <a href="../../../dashboard.php" class="btn btn-primary btn-sm">🏠 Main Dashboard</a>
            <span class="user-name"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>

            <!-- Theme Toggle -->
            <div class="theme-toggle" onclick="toggleTheme()" title="Toggle Dark Mode">
                <i class="fas fa-sun"></i>
                <div class="toggle-ball"></div>
                <i class="fas fa-moon"></i>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <nav class="sidebar-nav">
            <a href="index.php" class="nav-item active">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="index.php?route=patients/list" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Patients</span>
            </a>
            <a href="index.php?route=studies/list" class="nav-item">
                <i class="fas fa-calendar-alt"></i>
                <span>Studies</span>
            </a>
            <a href="index.php?route=reports/list" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
            <a href="../../../admin.php" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Admin</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="dashboard-page">
            <h1 class="page-title"><i class="fas fa-x-ray"></i> Radiology Information System</h1>

            <?php if (isset($stats)): ?>
            <div class="stats-cards">
                <div class="stat-box">
                    <div class="stat-label">Total Patients</div>
                    <div class="stat-value"><?php echo $stats['total_patients']; ?></div>
                    <div class="stat-description">Registered patients in the system</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Studies Today</div>
                    <div class="stat-value"><?php echo $stats['studies_today']; ?></div>
                    <div class="stat-description">Radiology exams performed today</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Available Equipment</div>
                    <div class="stat-value"><?php echo $stats['available_modalities']; ?></div>
                    <div class="stat-description">Operational radiology equipment</div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (isset($recentStudies) && !empty($recentStudies)): ?>
            <div class="activity-container">
                <div class="activity-header">
                    <h2><i class="fas fa-clock"></i> Recent Studies</h2>
                </div>
                <div class="activity-feed">
                    <?php foreach ($recentStudies as $study): ?>
                    <div class="activity-card">
                        <div class="activity-icon-wrapper">
                            <i class="fas fa-x-ray"></i>
                        </div>
                        <div class="activity-info">
                            <div class="activity-title"><?php echo htmlspecialchars($study['patient_name'] ?? 'Unknown Patient'); ?></div>
                            <div class="activity-desc"><?php echo htmlspecialchars($study['study_type'] ?? 'General Study'); ?> - <?php echo htmlspecialchars($study['study_date'] ?? 'No Date'); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ((!isset($stats) || $stats['total_patients'] == 0) && (!isset($recentStudies) || empty($recentStudies))): ?>
            <div class="no-studies">
                <h3><i class="fas fa-clipboard-list"></i> Welcome to RIS!</h3>
                <p>Your radiology information system is ready to use.</p>
                <p>Add patients and schedule studies to get started.</p>
            </div>
            <?php endif; ?>

            <div class="quick-actions">
                <a href="index.php?route=patients/list" class="action-card">
                    <h4><i class="fas fa-users"></i> Manage Patients</h4>
                    <p>Add and manage patient information</p>
                </a>
                <a href="index.php?route=studies/list" class="action-card">
                    <h4><i class="fas fa-calendar-alt"></i> Schedule Studies</h4>
                    <p>Create and manage radiology studies</p>
                </a>
                <a href="index.php?route=reports/list" class="action-card">
                    <h4><i class="fas fa-chart-bar"></i> View Reports</h4>
                    <p>Access study reports and results</p>
                </a>
            </div>
        </div>
    </main>

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

        // Sidebar toggle functionality
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });

        // Set active nav item with improved detection
        const currentPath = window.location.pathname;
        const currentSearch = window.location.search;

        document.querySelectorAll('.nav-item').forEach(item => {
            const itemPath = new URL(item.href, window.location.origin).pathname;
            const itemSearch = new URL(item.href, window.location.origin).search;

            // Check if the path matches
            if (itemPath === currentPath) {
                document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
                item.classList.add('active');
            }
        });

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
</html>,
