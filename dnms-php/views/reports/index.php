<?php $pageTitle = 'Nutrition Reports'; ?>
<?php
// Use the main layout system
$content = "
    <!-- Reports Content -->
    <div class=\"reports-container\">
        <div class=\"reports-header\">
            <h1>Nutrition Reports</h1>
            <p>Analyze your nutrition data and track your progress</p>
        </div>

        <!-- Report Filters -->
        <div class=\"filters-card\">
            <h3>Report Filters</h3>

            <form class=\"filters-form\">
                <div class=\"filters-grid\">
                    <div class=\"filter-group\">
                        <label for=\"start-date\">Start Date</label>
                        <input type=\"date\" id=\"start-date\" name=\"start_date\" value=\"" . date('Y-m-d', strtotime('-7 days')) . "\">
                    </div>

                    <div class=\"filter-group\">
                        <label for=\"end-date\">End Date</label>
                        <input type=\"date\" id=\"end-date\" name=\"end_date\" value=\"" . date('Y-m-d') . "\">
                    </div>

                    <div class=\"filter-group\">
                        <label for=\"report-type\">Report Type</label>
                        <select id=\"report-type\" name=\"report_type\">
                            <option value=\"summary\">Summary</option>
                            <option value=\"detailed\">Detailed</option>
                            <option value=\"trends\">Trends</option>
                            <option value=\"goals\">Goals Progress</option>
                            <option value=\"custom\">Custom</option>
                        </select>
                    </div>

                    <div class=\"filter-group\">
                        <label for=\"export-format\">Export Format</label>
                        <select id=\"export-format\" name=\"export_format\">
                            <option value=\"html\">HTML</option>
                            <option value=\"pdf\">PDF</option>
                            <option value=\"csv\">CSV</option>
                        </select>
                    </div>
                </div>

                <div class=\"filter-actions\">
                    <button type=\"button\" class=\"btn btn-primary\" onclick=\"generateReport()\">
                        <i class=\"fas fa-chart-bar\"></i>
                        Generate Report
                    </button>
                    <button type=\"button\" class=\"btn btn-secondary\" onclick=\"exportReport()\">
                        <i class=\"fas fa-download\"></i>
                        Export
                    </button>
                </div>
            </form>
        </div>

        <!-- Report Results -->
        <div id=\"report-results\" class=\"report-results\">
            " . (isset($reportData) ? "
                <div class=\"report-card\">
                    <div class=\"report-header\">
                        <h3>" . ucfirst($reportType ?? 'Summary') . " Report</h3>
                        <span class=\"report-date\">" . date('M j, Y') . "</span>
                    </div>

                    <div class=\"report-content\">
                        <pre>" . htmlspecialchars($reportData) . "</pre>
                    </div>
                </div>" : "
                <div class=\"welcome-message\">
                    <div class=\"welcome-icon\">
                        <i class=\"fas fa-chart-line\"></i>
                    </div>
                    <h3>Generate Your First Report</h3>
                    <p>Select your filters above and click \"Generate Report\" to analyze your nutrition data.</p>
                </div>") . "
        </div>
    </div>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>NutriTrack Pro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Enhanced DNMS Styling with LIS Design Pattern */
        :root {
            /* Unified DNMS Color System - Sky Blue Theme */
            --dnms-primary: #0ea5e9;          /* Sky blue */
            --dnms-primary-dark: #0284c7;     /* Darker sky blue for hover */
            --dnms-primary-light: #7dd3fc;    /* Lighter sky blue for accents */
            --dnms-secondary: #06b6d4;        /* Cyan for success */
            --dnms-warning: #f59e0b;          /* Warning orange */
            --dnms-danger: #ef4444;           /* Error red */
            --dnms-info: #3b82f6;             /* Info blue */
            --nutrition-gradient: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);

            /* Background Colors */
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --bg-tertiary: #f1f5f9;
            --bg-card: #ffffff;

            /* Text Colors */
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;

            /* Border and Shadow */
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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
            background: var(--nutrition-gradient);
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
            background: var(--nutrition-gradient);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.3);
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
            box-shadow: var(--shadow-sm);
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
            background: rgba(14, 165, 233, 0.1);
            transform: translateX(8px);
            box-shadow: var(--shadow-sm);
        }

        .nav-item.active {
            background: var(--nutrition-gradient);
            color: white;
            box-shadow: var(--shadow-md);
        }

        .nav-item.active::before {
            left: 0;
            opacity: 0.2;
        }

        .nav-item i {
            font-size: 1.2rem;
            width: 20px;
            text-align: center;
        }

        /* Reports Page Specific Styles */
        .reports-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .reports-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .reports-header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            background: var(--nutrition-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .reports-header p {
            color: var(--text-secondary);
            font-size: 1.125rem;
        }

        .filters-card {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
            margin-bottom: 2rem;
        }

        .filters-card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .filter-group input, .filter-group select {
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.875rem;
            background: var(--bg-primary);
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .filter-group input:focus, .filter-group select:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .filter-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .report-results {
            min-height: 300px;
        }

        .report-card {
            background: var(--bg-card);
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
            overflow: hidden;
            animation: fadeInUp 0.5s ease;
        }

        .report-header {
            background: var(--nutrition-gradient);
            color: white;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .report-header h3 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .report-date {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .report-content {
            padding: 2rem;
        }

        .report-content pre {
            background: var(--bg-secondary);
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            line-height: 1.6;
            margin: 0;
            overflow-x: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .welcome-message {
            text-align: center;
            padding: 4rem 2rem;
            background: var(--bg-card);
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
            animation: fadeInUp 0.5s ease;
        }

        .welcome-icon {
            width: 80px;
            height: 80px;
            background: var(--nutrition-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
        }

        .welcome-message h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .welcome-message p {
            color: var(--text-secondary);
            margin-bottom: 2rem;
            font-size: 1rem;
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

            .reports-header h1 {
                font-size: 2rem;
            }

            .filters-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .filter-actions {
                flex-direction: column;
                align-items: center;
            }

            .report-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .report-content {
                padding: 1.5rem;
            }

            .welcome-message {
                padding: 3rem 1.5rem;
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
                <span class="brand-name">NutriTrack Pro</span>
                <span class="brand-module">DNMS</span>
            </div>
        </div>
        <div class="header-right">
            <a href="../../index.php" class="btn btn-primary btn-sm">🏠 Main Dashboard</a>
            <span class="user-name"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <nav class="sidebar-nav">
            <a href="../../index.php" class="nav-item active">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="../../views/food/search.php" class="nav-item">
                <i class="fas fa-search"></i>
                <span>Food Search</span>
            </a>
            <a href="../meal/planner.php" class="nav-item">
                <i class="fas fa-calendar-alt"></i>
                <span>Meal Planner</span>
            </a>
            <a href="../meal/log.php" class="nav-item">
                <i class="fas fa-book"></i>
                <span>Meal Log</span>
            </a>
            <a href="../meal/history.php" class="nav-item">
                <i class="fas fa-history"></i>
                <span>Meal History</span>
            </a>
            <a href="../../views/goals/index.php" class="nav-item">
                <i class="fas fa-bullseye"></i>
                <span>Goals</span>
            </a>
            <a href="../../views/reports/index.php" class="nav-item">
                <i class="fas fa-chart-line"></i>
                <span>Reports</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <?php echo $content; ?>
    </main>

    <script>
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

            // Check if the path matches (ignoring query parameters for basic matching)
            if (itemPath === currentPath) {
                document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
                item.classList.add('active');
            }
        });

        // Report generation functions
        function generateReport() {
            const startDate = document.getElementById('start-date').value;
            const endDate = document.getElementById('end-date').value;
            const reportType = document.getElementById('report-type').value;

            // Show loading state
            const resultsDiv = document.getElementById('report-results');
            resultsDiv.innerHTML = '<div class="welcome-message"><div class="welcome-icon"><i class="fas fa-spinner fa-spin"></i></div><h3>Generating Report...</h3><p>Please wait while we process your request.</p></div>';

            // Simulate report generation (replace with actual API call)
            setTimeout(() => {
                const mockReportData = generateMockReportData(startDate, endDate, reportType);
                displayReportResults(mockReportData, reportType);
            }, 1500);
        }

        function generateMockReportData(startDate, endDate, type) {
            const data = {
                summary: "Nutrition Summary Report\nTotal Calories: 2,450\nProtein: 125g\nCarbs: 280g\nFat: 85g\nFiber: 32g",
                detailed: "Detailed Nutrition Report\nDay 1: Calories: 2,100, Protein: 110g\nDay 2: Calories: 2,300, Protein: 120g\nDay 3: Calories: 2,500, Protein: 130g",
                trends: "Nutrition Trends\nProtein intake increasing by 5% weekly\nCalorie consistency: 92%\nMacro balance: Optimal",
                goals: "Goals Progress\nProtein Goal: 125g (100% achieved)\nCalorie Goal: 2,500 (98% achieved)\nFiber Goal: 30g (107% achieved)"
            };
            return data[type] || data.summary;
        }

        function displayReportResults(data, type) {
            const resultsDiv = document.getElementById('report-results');
            resultsDiv.innerHTML = `
                <div class="report-card">
                    <div class="report-header">
                        <h3>${type.charAt(0).toUpperCase() + type.slice(1)} Report</h3>
                        <span class="report-date">${new Date().toLocaleDateString()}</span>
                    </div>
                    <div class="report-content">
                        <pre>${data}</pre>
                    </div>
                </div>
            `;
        }

        function exportReport() {
            // Show notification
            showNotification('Report exported successfully!', 'success');
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            const colors = {
                success: 'var(--dnms-secondary)',
                error: 'var(--dnms-danger)',
                warning: 'var(--dnms-warning)',
                info: 'var(--dnms-info)'
            };

            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${colors[type]};
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 12px;
                box-shadow: var(--shadow-lg);
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
