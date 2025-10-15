<?php
/**
 * DNMS User Dashboard Controller
 * Enhanced dashboard with comprehensive nutrition tracking
 */

// Prevent direct access issues
if (!defined('DNMS_ENTRY_POINT')) {
    define('DNMS_ENTRY_POINT', true);
}

// Load main config for authentication functions only (don't restart session)
try {
    require_once __DIR__ . '/../../../config.php';
} catch (Exception $e) {
    // If main config fails, redirect to setup
    header('Location: ../../../../database_setup.php');
    exit();
}

// Load DNMS config
try {
    require_once __DIR__ . '/../../config/constants.php';
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../config/environment.php';
    require_once __DIR__ . '/../../classes/User.php';
    require_once __DIR__ . '/../../classes/Food.php';
    require_once __DIR__ . '/../../classes/Meal.php';
} catch (Exception $e) {
    die("DNMS Configuration Error: " . $e->getMessage());
}

// Load includes with error handling
try {
    require_once __DIR__ . '/../../includes/auth_functions.php';
} catch (Exception $e) {
    error_log("DNMS Auth Functions Error: " . $e->getMessage());
}

// Check if user is logged in using main system's session
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../../login.php');
    exit();
}

// Initialize classes
require_once __DIR__ . '/../../classes/Food.php';
require_once __DIR__ . '/../../classes/Meal.php';
require_once __DIR__ . '/../../classes/Recipe.php';
require_once __DIR__ . '/../../classes/ShoppingList.php';
require_once __DIR__ . '/../../classes/Exercise.php';
require_once __DIR__ . '/../../classes/WaterTracker.php';
require_once __DIR__ . '/../../classes/BodyMeasurements.php';

try {
    $food = new Food();
    $meal = new Meal();
    $recipe = new Recipe();
    $shoppingList = new ShoppingList();
    $exercise = new Exercise();
    $waterTracker = new WaterTracker();
    $bodyMeasurements = new BodyMeasurements();

    // Get dashboard data with error handling
    $stats = getDashboardStats();
    $recentMeals = getRecentMeals($_SESSION['user_id']);
    $achievements = getAchievements($_SESSION['user_id']);
    $insights = getNutritionInsights($_SESSION['user_id']);
    $goalProgress = getGoalProgress($_SESSION['user_id']);
    $weeklyData = getWeeklyData($_SESSION['user_id']);
    $topFoods = getTopFoods($_SESSION['user_id']);

    // Get new feature data
    $exerciseSummary = $exercise->getExerciseSummary($_SESSION['user_id']);
    $todayWater = $waterTracker->getDailyWaterSummary($_SESSION['user_id'], date('Y-m-d'));
    $latestMeasurements = $bodyMeasurements->getLatestMeasurements($_SESSION['user_id']);
    $recentRecipes = $recipe->getUserRecipes($_SESSION['user_id'], 3);
    $shoppingLists = $shoppingList->getUserShoppingLists($_SESSION['user_id']);

    // Load layout and inject dashboard content
    ob_start();
    include __DIR__ . '/../../views/dashboard/index.php';
    $dashboardContent = ob_get_clean();

    // Wrap dashboard content with sidebar structure
    $sidebarStructure = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - NutriTrack Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* Enhanced DNMS Styling with LIS-inspired Design */
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
            font-family: \'Inter\', -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif;
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
            content: \'\';
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

        /* Enhanced Dashboard Page Styling */
        .dashboard-page {
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

        /* Enhanced Statistics Cards */
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .stat-box {
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

        .stat-box::before {
            content: \'\';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--nutrition-gradient);
        }

        .stat-box:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(14, 165, 233, 0.3);
        }

        .stat-label {
            font-size: 1rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .stat-value {
            font-size: 3rem;
            font-weight: 800;
            background: var(--nutrition-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
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
            border-color: #0ea5e9;
            transform: scale(1.05);
        }

        .theme-toggle .toggle-ball {
            width: 18px;
            height: 18px;
            background: var(--nutrition-gradient);
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
                <span class="brand-name">NutriTrack Pro</span>
                <span class="brand-module">DNMS</span>
            </div>
        </div>
        <div class="header-right">
            <a href="../../../dashboard.php" class="btn btn-primary btn-sm">🏠 Main Dashboard</a>

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
        ' . $dashboardContent . '

    <script>
        // Theme Management
        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute(\'data-theme\');
            const newTheme = currentTheme === \'dark\' ? \'light\' : \'dark\';

            document.documentElement.setAttribute(\'data-theme\', newTheme);
            localStorage.setItem(\'theme\', newTheme);

            // Update toggle button
            const toggleBall = document.querySelector(\'.toggle-ball\');
            if (newTheme === \'dark\') {
                toggleBall.style.transform = \'translateX(20px)\';
            } else {
                toggleBall.style.transform = \'translateX(0)\';
            }

            showNotification(`Switched to ${newTheme} mode!`, \'success\');
        }

        function initializeTheme() {
            const savedTheme = localStorage.getItem(\'theme\') || \'light\';
            document.documentElement.setAttribute(\'data-theme\', savedTheme);

            // Update toggle button on load
            const toggleBall = document.querySelector(\'.toggle-ball\');
            if (savedTheme === \'dark\') {
                toggleBall.style.transform = \'translateX(20px)\';
            }
        }

        function showNotification(message, type = \'info\') {
            const notification = document.createElement(\'div\');
            const colors = {
                success: \'linear-gradient(135deg, #06b6d4 0%, #0891b2 100%)\',
                error: \'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)\',
                warning: \'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)\',
                info: \'linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%)\'
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
                font-family: \'Inter\', sans-serif;
                animation: slideInRight 0.3s ease;
            `;

            notification.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-` + getIcon(type) + `" style="font-size: 18px;"></i>
                    <p style="margin: 0; font-size: 14px; line-height: 1.4;">` + message + `</p>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.animation = \'slideInRight 0.3s ease reverse\';
                    setTimeout(() => notification.parentNode.removeChild(notification), 300);
                }
            }, 4000);
        }

        function getIcon(type) {
            const icons = {
                success: \'check-circle\',
                error: \'exclamation-circle\',
                warning: \'exclamation-triangle\',
                info: \'info-circle\'
            };
            return icons[type] || \'info-circle\';
        }

        // Sidebar toggle functionality
        document.getElementById(\'menuToggle\').addEventListener(\'click\', function() {
            document.getElementById(\'sidebar\').classList.toggle(\'show\');
        });

        // Set active nav item
        const currentPath = window.location.href;
        document.querySelectorAll(\'.nav-item\').forEach(item => {
            if (item.href === currentPath) {
                document.querySelectorAll(\'.nav-item\').forEach(i => i.classList.remove(\'active\'));
                item.classList.add(\'active\');
            }
        });

        // Initialize theme on page load
        document.addEventListener(\'DOMContentLoaded\', function() {
            initializeTheme();
        });

        // Add CSS animations for notifications
        const notificationStyles = document.createElement(\'style\');
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
';

// Output the sidebar structure with dashboard content
    echo $sidebarStructure;
} catch (Exception $e) {
    error_log("DNMS Dashboard Error: " . $e->getMessage());

    // Check if it's a database setup issue
    if (strpos($e->getMessage(), 'Table') !== false || strpos($e->getMessage(), 'dnms_') !== false) {
        showSetupRequiredError($e->getMessage());
    } else {
        showDashboardError("Failed to load dashboard: " . $e->getMessage());
    }
}

// Dashboard data functions (following search_foods.php pattern)
function getDashboardStats() {
    $db = getDNMSDBConnection();
    $userId = $_SESSION['user_id'];
    $today = date('Y-m-d');

    return [
        'total_foods' => getTotalFoods($db),
        'total_meals' => getTotalMeals($db, $userId),
        'daily_calories' => getDailyCalories($db, $userId, $today),
        'daily_protein' => getDailyProtein($db, $userId, $today),
        'daily_carbs' => getDailyCarbs($db, $userId, $today),
        'daily_fat' => getDailyFat($db, $userId, $today),
        'goals_achieved' => getGoalsAchieved($db, $userId),
        'nutrition_score' => calculateNutritionScore($db, $userId, $today),
        'meal_streak' => getMealStreak($db, $userId),
    ];
}

function getTotalFoods($db) {
    try {
        $stmt = $db->query("SELECT COUNT(*) as count FROM dnms_foods");
        return $stmt->fetch()['count'];
    } catch (Exception $e) {
        return 0;
    }
}

function getTotalMeals($db, $userId) {
    try {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM dnms_meals WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch()['count'];
    } catch (Exception $e) {
        return 0;
    }
}

function getDailyCalories($db, $userId, $date) {
    try {
        $stmt = $db->prepare("SELECT SUM(total_calories) as total FROM dnms_meals WHERE user_id = ? AND DATE(meal_date) = ?");
        $stmt->execute([$userId, $date]);
        return $stmt->fetch()['total'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

function getDailyProtein($db, $userId, $date) {
    try {
        $stmt = $db->prepare("SELECT SUM(total_protein) as total FROM dnms_meals WHERE user_id = ? AND DATE(meal_date) = ?");
        $stmt->execute([$userId, $date]);
        return $stmt->fetch()['total'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

function getDailyCarbs($db, $userId, $date) {
    try {
        $stmt = $db->prepare("SELECT SUM(total_carbs) as total FROM dnms_meals WHERE user_id = ? AND DATE(meal_date) = ?");
        $stmt->execute([$userId, $date]);
        return $stmt->fetch()['total'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

function getDailyFat($db, $userId, $date) {
    try {
        $stmt = $db->prepare("SELECT SUM(total_fat) as total FROM dnms_meals WHERE user_id = ? AND DATE(meal_date) = ?");
        $stmt->execute([$userId, $date]);
        return $stmt->fetch()['total'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

function getGoalsAchieved($db, $userId) {
    try {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM dnms_nutrition_goals WHERE user_id = ? AND status = 'completed'");
        $stmt->execute([$userId]);
        return $stmt->fetch()['count'];
    } catch (Exception $e) {
        return 0;
    }
}

function calculateNutritionScore($db, $userId, $date) {
    try {
        $calories = getDailyCalories($db, $userId, $date);
        $protein = getDailyProtein($db, $userId, $date);

        // Simple scoring algorithm
        $score = 0;
        if ($calories > 0) $score += min(100, ($calories / 2000) * 100);
        if ($protein > 0) $score += min(100, ($protein / 150) * 100);

        return round($score / 2);
    } catch (Exception $e) {
        return 0;
    }
}

function getMealStreak($db, $userId) {
    try {
        $stmt = $db->prepare("
            SELECT meal_date, COUNT(*) as meals_per_day
            FROM dnms_meals
            WHERE user_id = ?
            GROUP BY meal_date
            ORDER BY meal_date DESC
            LIMIT 7
        ");
        $stmt->execute([$userId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $streak = 0;
        $today = date('Y-m-d');
        for ($i = 0; $i < 7; $i++) {
            $checkDate = date('Y-m-d', strtotime("-{$i} days", strtotime($today)));
            $found = false;
            foreach ($results as $result) {
                if ($result['meal_date'] === $checkDate && $result['meals_per_day'] > 0) {
                    $found = true;
                    break;
                }
            }
            if ($found) {
                $streak++;
            } else {
                break;
            }
        }
        return $streak;
    } catch (Exception $e) {
        return 0;
    }
}

function getRecentMeals($userId, $limit = 5) {
    $db = getDNMSDBConnection();
    try {
        $stmt = $db->prepare("SELECT * FROM dnms_meals WHERE user_id = ? ORDER BY meal_date DESC, meal_time DESC LIMIT ?");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getAchievements($userId) {
    $db = getDNMSDBConnection();
    $achievements = [];

    // Meal logging streak
    $streak = getMealStreak($db, $userId);
    if ($streak >= 7) {
        $achievements[] = ['icon' => '🏆', 'title' => '7-Day Streak', 'description' => 'Logged meals for 7 consecutive days!'];
    }

    // First meal logged
    $totalMeals = getTotalMeals($db, $userId);
    if ($totalMeals >= 1) {
        $achievements[] = ['icon' => '🎯', 'title' => 'Getting Started', 'description' => 'Logged your first meal!'];
    }

    // Nutrition goal achiever
    $goalsAchieved = getGoalsAchieved($db, $userId);
    if ($goalsAchieved >= 5) {
        $achievements[] = ['icon' => '⭐', 'title' => 'Goal Crusher', 'description' => 'Achieved 5 nutrition goals!'];
    }

    return $achievements;
}

function getNutritionInsights($userId) {
    $db = getDNMSDBConnection();
    $insights = [];

    // Average daily calories this week
    $weeklyCalories = getWeeklyCalories($db, $userId);
    $avgDaily = $weeklyCalories / 7;

    if ($avgDaily > 2500) {
        $insights[] = ['type' => 'warning', 'message' => 'Your average daily calories are high this week. Consider portion control.'];
    } elseif ($avgDaily < 1500) {
        $insights[] = ['type' => 'info', 'message' => 'Your calorie intake is quite low. Make sure you\'re getting enough nutrients.'];
    } else {
        $insights[] = ['type' => 'success', 'message' => 'Great job maintaining a balanced calorie intake!'];
    }

    return $insights;
}

function getWeeklyCalories($db, $userId) {
    try {
        $weekAgo = date('Y-m-d', strtotime('-7 days'));
        $stmt = $db->prepare("SELECT SUM(total_calories) as total FROM dnms_meals WHERE user_id = ? AND meal_date >= ?");
        $stmt->execute([$userId, $weekAgo]);
        return $stmt->fetch()['total'] ?? 0;
    } catch (Exception $e) {
        return 0;
    }
}

function getGoalProgress($userId) {
    $db = getDNMSDBConnection();
    $goals = [];

    try {
        // Example goals - in real implementation, this would come from user goals table
        $goals[] = [
            'name' => 'Daily Protein',
            'current' => getDailyProtein($db, $userId, date('Y-m-d')),
            'target' => 150,
            'unit' => 'g'
        ];

        $goals[] = [
            'name' => 'Daily Calories',
            'current' => getDailyCalories($db, $userId, date('Y-m-d')),
            'target' => 2000,
            'unit' => 'kcal'
        ];
    } catch (Exception $e) {
        // Return empty array on error
    }

    return $goals;
}

function getWeeklyData($userId) {
    $db = getDNMSDBConnection();
    $data = ['dates' => [], 'calories' => [], 'protein' => []];

    try {
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $data['dates'][] = date('M j', strtotime($date));

            $calories = getDailyCalories($db, $userId, $date);
            $protein = getDailyProtein($db, $userId, $date);

            $data['calories'][] = $calories;
            $data['protein'][] = $protein;
        }
    } catch (Exception $e) {
        // Return empty data on error
    }

    return $data;
}

function getTopFoods($userId) {
    $db = getDNMSDBConnection();
    $foods = [];

    try {
        $stmt = $db->prepare("
            SELECT f.name, COUNT(mf.food_id) as frequency
            FROM dnms_meal_foods mf
            JOIN dnms_foods f ON mf.food_id = f.id
            JOIN dnms_meals m ON mf.meal_id = m.id
            WHERE m.user_id = ?
            GROUP BY f.id, f.name
            ORDER BY frequency DESC
            LIMIT 5
        ");
        $stmt->execute([$userId]);
        $foods = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Return empty array on error
    }

    return $foods;
}

function showSetupRequiredError($message) {
    ?>
    <!DOCTYPE html>
    <html lang="en" data-theme="light">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>DNMS Setup Required - NutriTrack Pro</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            :root {
                --primary-color: #4ecdc4;
                --warning-color: #f59e0b;
                --danger-color: #f56565;
                --bg-primary: #ffffff;
                --text-primary: #212529;
                --text-secondary: #6c757d;
                --border-color: #dee2e6;
                --shadow-md: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            }

            body {
                font-family: 'Inter', sans-serif;
                background: var(--bg-primary);
                color: var(--text-primary);
                margin: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .setup-container {
                background: var(--bg-primary);
                padding: 3rem;
                border-radius: 16px;
                box-shadow: var(--shadow-md);
                text-align: center;
                max-width: 700px;
                border: 1px solid var(--border-color);
            }

            .setup-container h1 {
                color: var(--warning-color);
                margin-bottom: 1rem;
            }

            .error-details {
                background: #fef3c7;
                border: 1px solid #f59e0b;
                border-radius: 8px;
                padding: 1rem;
                margin: 1rem 0;
                text-align: left;
            }

            .btn {
                background: var(--primary-color);
                color: white;
                padding: 0.75rem 1.5rem;
                border: none;
                border-radius: 8px;
                text-decoration: none;
                display: inline-block;
                margin: 0.5rem;
                transition: all 0.3s ease;
            }

            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 15px rgba(78, 205, 196, 0.3);
            }

            .btn-secondary {
                background: var(--text-secondary);
            }

            .steps {
                text-align: left;
                margin: 2rem 0;
            }

            .step {
                margin: 1rem 0;
                padding: 1rem;
                background: #f8f9fa;
                border-radius: 8px;
                border-left: 4px solid var(--primary-color);
            }

            .step-number {
                display: inline-block;
                width: 24px;
                height: 24px;
                background: var(--primary-color);
                color: white;
                border-radius: 50%;
                text-align: center;
                line-height: 24px;
                font-weight: bold;
                margin-right: 0.5rem;
            }
        </style>
    </head>
    <body>
        <div class="setup-container">
            <h1>🚨 DNMS Database Setup Required</h1>
            <p>Your DNMS dashboard needs database tables to be set up before it can display data.</p>

            <div class="error-details">
                <strong>Database Error:</strong><br>
                <?php echo htmlspecialchars($message); ?>
            </div>

            <div class="steps">
                <h3>To fix this issue:</h3>

                <div class="step">
                    <span class="step-number">1</span>
                    <strong>Run the database setup script:</strong><br>
                    Access <a href="../setup_database.php" class="btn">Database Setup</a> to create the required tables.
                </div>

                <div class="step">
                    <span class="step-number">2</span>
                    <strong>Or run the SQL file manually:</strong><br>
                    Execute the SQL file <code>dnms-php/database/dnms_setup.sql</code> in your MySQL database.
                </div>

                <div class="step">
                    <span class="step-number">3</span>
                    <strong>Refresh this page:</strong><br>
                    Once the tables are created, refresh the dashboard to see your nutrition data.
                </div>
            </div>

            <div>
                <a href="../setup_database.php" class="btn">Run Database Setup</a>
                <a href="../index.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>

            <p><small>After setting up the database, the DNMS dashboard will show your nutrition statistics, meal history, and achievements.</small></p>
        </div>
    </body>
    </html>
    <?php
    exit();
}

function showDashboardError($message) {
    ?>
    <!DOCTYPE html>
    <html lang="en" data-theme="light">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard Error - NutriTrack Pro</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            :root {
                --primary-color: #4ecdc4;
                --danger-color: #f56565;
                --bg-primary: #ffffff;
                --text-primary: #212529;
                --text-secondary: #6c757d;
                --border-color: #dee2e6;
                --shadow-md: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            }

            body {
                font-family: 'Inter', sans-serif;
                background: var(--bg-primary);
                color: var(--text-primary);
                margin: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .error-container {
                background: var(--bg-primary);
                padding: 3rem;
                border-radius: 16px;
                box-shadow: var(--shadow-md);
                text-align: center;
                max-width: 600px;
                border: 1px solid var(--border-color);
            }

            .error-container h1 {
                color: var(--danger-color);
                margin-bottom: 1rem;
            }

            .btn {
                background: var(--primary-color);
                color: white;
                padding: 0.75rem 1.5rem;
                border: none;
                border-radius: 8px;
                text-decoration: none;
                display: inline-block;
                margin: 0.5rem;
                transition: all 0.3s ease;
            }

            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 15px rgba(78, 205, 196, 0.3);
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1>❌ Dashboard Error</h1>
            <p><?php echo htmlspecialchars($message); ?></p>
            <a href="../index.php" class="btn">Back to Dashboard</a>
        </div>
    </body>
    </html>
    <?php
    exit();
}
?>
