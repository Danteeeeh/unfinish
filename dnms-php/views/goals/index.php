<?php
session_start();

// Include main config first (contains core auth functions)
require_once __DIR__ . '/../../../config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Include database configuration
require_once __DIR__ . '/../../config/database.php';

// Include authentication functions
require_once __DIR__ . '/../../includes/auth_functions.php';

// Get user data
$userId = $_SESSION['user_id'];
$currentUser = getCurrentUser();

$pageTitle = 'Nutrition Goals';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - NutriTrack Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
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
            --primary-gradient: linear-gradient(135deg, #0ea5e9 0%, #7dd3fc 100%);
            --secondary-gradient: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            --success-gradient: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);

            /* Light Theme Variables */
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --bg-tertiary: #f1f5f9;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --card-bg: #ffffff;
            --navbar-bg: rgba(255, 255, 255, 0.95);
            --sidebar-bg: #f8fafc;
        }

        /* Dark Theme Variables */
        [data-theme="dark"] {
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-tertiary: #334155;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4), 0 2px 4px -1px rgba(0, 0, 0, 0.3);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.5), 0 4px 6px -2px rgba(0, 0, 0, 0.4);
            --card-bg: #1e293b;
            --navbar-bg: rgba(15, 23, 42, 0.95);
            --sidebar-bg: #1e293b;
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

        .user-name {
            color: var(--text-secondary);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
        }

        .nav-item.active {
            background: var(--nutrition-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.3);
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

        /* Goals Page Styling */
        .goals-page {
            max-width: 1400px;
            margin: 0 auto;
        }

        .page-title {
            font-size: 3rem;
            font-weight: 800;
            background: var(--nutrition-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 2rem;
            text-align: center;
            letter-spacing: -0.02em;
        }

        /* Goals specific styles from original */
        .goals-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .goals-header h1 {
            font-size: 3rem;
            font-weight: 800;
            background: var(--nutrition-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
            animation: fadeInUp 0.6s ease-out;
        }

        .goals-header p {
            font-size: 1.25rem;
            color: var(--text-secondary);
            animation: fadeInUp 0.6s ease-out 0.1s both;
        }

        .goals-actions {
            display: flex;
            justify-content: center;
            margin-bottom: 3rem;
        }

        .goals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .goal-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out;
        }

        .goal-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--nutrition-gradient);
        }

        .goal-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
            border-color: var(--nutrition-gradient);
        }

        .goal-header {
            display: flex;
            align-items: flex-start;
            margin-bottom: 2rem;
            gap: 1.5rem;
        }

        .goal-icon {
            width: 60px;
            height: 60px;
            background: var(--nutrition-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .goal-info {
            flex: 1;
        }

        .goal-info h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .goal-description {
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .goal-status {
            background: var(--success-gradient);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .goal-status.completed {
            background: var(--success-gradient);
        }

        .goal-status.active {
            background: var(--warning-gradient);
        }

        .goal-progress {
            margin-top: 1.5rem;
        }

        .progress-info {
            display: flex;
            align-items: baseline;
            gap: 0.5rem;
            margin-bottom: 1rem;
            justify-content: center;
        }

        .progress-current {
            font-size: 2rem;
            font-weight: 700;
            color: var(--nutrition-gradient);
        }

        .progress-separator {
            color: var(--text-secondary);
            font-size: 1.5rem;
        }

        .progress-target {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .progress-unit {
            font-size: 1rem;
            color: var(--text-secondary);
            margin-left: 0.5rem;
        }

        .progress-bar {
            height: 12px;
            background: var(--bg-tertiary);
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .progress-fill {
            height: 100%;
            background: var(--nutrition-gradient);
            border-radius: 6px;
            transition: width 0.5s ease;
        }

        .progress-percentage {
            text-align: center;
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--nutrition-gradient);
        }

        .goal-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
        }

        .btn-secondary {
            background: var(--bg-tertiary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--border-color);
            transform: translateY(-1px);
        }

        .btn-danger {
            background: var(--danger-gradient);
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        .no-goals {
            text-align: center;
            padding: 5rem 2rem;
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
        }

        .no-goals-icon {
            width: 100px;
            height: 100px;
            background: var(--nutrition-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5rem;
            margin: 0 auto 2rem;
            animation: pulse 2s infinite;
        }

        .no-goals h3 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        .no-goals p {
            color: var(--text-secondary);
            font-size: 1.125rem;
            margin-bottom: 2rem;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
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

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
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

            .goals-grid {
                grid-template-columns: 1fr;
            }

            .goal-header {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .goal-actions {
                justify-content: center;
            }

            .goals-actions {
                flex-direction: column;
                gap: 1rem;
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
            <a href="../../dashboard.php" class="btn btn-primary btn-sm">🏠 Main Dashboard</a>
            <span class="user-name"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <nav class="sidebar-nav">
            <a href="../../index.php" class="nav-item">
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
            <a href="index.php" class="nav-item active">
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
        <div class="goals-page">
            <div class="goals-header">
                <h1>🎯 Nutrition Goals</h1>
                <p>Set and track your nutrition targets to achieve your health objectives</p>
            </div>

            <div class="goals-actions">
                <a href="add.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Add New Goal
                </a>
            </div>

            <?php if (isset($goals) && !empty($goals)): ?>
                <div class="goals-grid">
                    <?php foreach ($goals as $goal): ?>
                        <div class="goal-card">
                            <div class="goal-header">
                                <div class="goal-icon">
                                    <i class="fas fa-bullseye"></i>
                                </div>
                                <div class="goal-info">
                                    <h3><?php echo htmlspecialchars($goal['name'] ?? 'Goal'); ?></h3>
                                    <p class="goal-description"><?php echo htmlspecialchars($goal['description'] ?? 'Track your progress towards this goal'); ?></p>
                                </div>
                                <div class="goal-status <?php echo $goal['status'] ?? 'active'; ?>">
                                    <?php echo ucfirst($goal['status'] ?? 'Active'); ?>
                                </div>
                            </div>

                            <div class="goal-progress">
                                <div class="progress-info">
                                    <span class="progress-current"><?php echo number_format($goal['current'] ?? 0); ?></span>
                                    <span class="progress-separator">/</span>
                                    <span class="progress-target"><?php echo number_format($goal['target'] ?? 0); ?></span>
                                    <span class="progress-unit"><?php echo htmlspecialchars($goal['unit'] ?? 'units'); ?></span>
                                </div>

                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo min(100, (($goal['current'] ?? 0) / ($goal['target'] ?? 1)) * 100); ?>%"></div>
                                </div>

                                <div class="progress-percentage">
                                    <?php echo number_format(min(100, (($goal['current'] ?? 0) / ($goal['target'] ?? 1)) * 100), 1); ?>%
                                </div>
                            </div>

                            <div class="goal-actions">
                                <a href="edit.php?id=<?php echo $goal['id']; ?>" class="btn btn-secondary">
                                    <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <button onclick="deleteGoal(<?php echo $goal['id']; ?>)" class="btn btn-danger">
                                    <i class="fas fa-trash"></i>
                                    Delete
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-goals">
                    <div class="no-goals-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3>No Goals Set Yet</h3>
                    <p>Set your first nutrition goal to start tracking your progress towards better health!</p>
                    <a href="add.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Create Your First Goal
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        // Sidebar toggle functionality
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });

        // Set active nav item
        const currentPath = window.location.href;
        document.querySelectorAll('.nav-item').forEach(item => {
            if (item.href === currentPath) {
                document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
                item.classList.add('active');
            }
        });

        function deleteGoal(goalId) {
            if (confirm('Are you sure you want to delete this goal? This action cannot be undone.')) {
                // Create a form to submit the delete request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'delete.php?id=' + goalId;

                document.body.appendChild(form);
                form.submit();
            }
        }

        // Initialize theme
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        });
    </script>
</body>
</html>
