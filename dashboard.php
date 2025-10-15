<?php
require_once 'config.php';
requireLogin();

// Initialize database connection with error handling
try {
    $conn = getDBConnection();
} catch (Exception $e) {
    die("Database connection failed. Please contact administrator.");
}

// Get user information with error handling
try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("s", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        die("User data not found. Please contact administrator.");
    }
} catch (Exception $e) {
    die("Error retrieving user data. Please contact administrator.");
}

// Function to get module-specific database connection
function getModuleConnection($module) {
    $connections = [
        'dnms' => __DIR__ . '/dnms-php/config/database.php',
        'lis' => __DIR__ . '/lis-php/config/database.php',
        'pms' => __DIR__ . '/pms-php/config/database.php',
        'ris' => __DIR__ . '/ris-php/config/database.php',
        'sors' => __DIR__ . '/sors-php/models/Database.php'
    ];

    if (isset($connections[$module]) && file_exists($connections[$module])) {
        try {
            require_once $connections[$module];

            // Use module-specific function names
            switch ($module) {
                case 'dnms':
                    return getDNMSDBConnection();
                case 'sors':
                    // SORS uses singleton pattern with getInstance()
                    return SORSDatabase::getInstance();
                case 'pms':
                    // PMS might use different function name, try common ones
                    if (function_exists('getPMSDBConnection')) {
                        return getPMSDBConnection();
                    } elseif (function_exists('getDBConnection')) {
                        return getDBConnection();
                    }
                    return null;
                case 'lis':
                case 'ris':
                    // These modules might use different approaches, try common function names
                    if (function_exists('getDBConnection')) {
                        return getDBConnection();
                    }
                    return null;
                default:
                    return null;
            }
        } catch (Exception $e) {
            error_log("Module connection error for {$module}: " . $e->getMessage());
            return null;
        }
    }
    return null;
}

// Function to get module stats safely
function getModuleStats($module, $tables) {
    $connection = getModuleConnection($module);
    $stats = [];

    if ($connection) {
        foreach ($tables as $key => $table) {
            try {
                // Handle different connection types
                if ($module === 'sors' && method_exists($connection, 'getConnection')) {
                    // SORS returns Database instance, get PDO connection
                    $pdo = $connection->getConnection();

                    // Check if table exists first
                    if ($pdo && method_exists($connection, 'tableExists') && $connection->tableExists($table)) {
                        $stmt = $pdo->query("SELECT COUNT(*) as count FROM {$table}");
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        $stats[$key] = $row['count'] ?? 0;
                    } else {
                        $stats[$key] = 0;
                    }
                } elseif ($connection instanceof PDO) {
                    // Direct PDO connection (like DNMS)
                    $stmt = $connection->query("SELECT COUNT(*) as count FROM {$table}");
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $stats[$key] = $row['count'] ?? 0;
                } else {
                    // MySQLi connection (fallback)
                    $result = $connection->query("SELECT COUNT(*) as count FROM {$table}");
                    if ($result) {
                        $stats[$key] = $result->fetch_assoc()['count'];
                    } else {
                        $stats[$key] = 0;
                    }
                }
            } catch (Exception $e) {
                $stats[$key] = 0;
            }
        }
    } else {
        // Fallback to main connection if module connection fails
        global $conn;
        foreach ($tables as $key => $table) {
            try {
                $result = $conn->query("SELECT COUNT(*) as count FROM {$table}");
                if ($result) {
                    $stats[$key] = $result->fetch_assoc()['count'];
                } else {
                    $stats[$key] = 0;
                }
            } catch (Exception $e) {
                $stats[$key] = 0;
            }
        }
    }

    return $stats;
}

// Fetch stats for each module using their own connections
$stats = [];

// DNMS (Nutrition) stats
$stats = array_merge($stats, getModuleStats('dnms', [
    'dnms_foods' => 'dnms_foods',
    'dnms_meals' => 'dnms_meals',
    'dnms_nutrition_goals' => 'dnms_nutrition_goals',
    'dnms_meal_plans' => 'dnms_meal_plans'
]));

// LIS (Laboratory) stats
$stats = array_merge($stats, getModuleStats('lis', [
    'lis_tests' => 'lis_tests',
    'lis_results' => 'lis_results'
]));

// PMS (Pharmacy) stats
$stats = array_merge($stats, getModuleStats('pms', [
    'pms_medicines' => 'pms_medicines',
    'pms_inventory' => 'pms_inventory'
]));

// Handle missing SORS tables gracefully
if (!isset($stats['sors_rooms']) || $stats['sors_rooms'] === null) {
    $stats['sors_rooms'] = 0;
}
if (!isset($stats['sors_surgeries']) || $stats['sors_surgeries'] === null) {
    $stats['sors_surgeries'] = 0;
}

// RIS (Radiology) stats
$stats = array_merge($stats, getModuleStats('ris', [
    'ris_equipment' => 'ris_equipment',
    'ris_exams' => 'ris_exams'
]));

// SORS (Surgery) stats
$stats = array_merge($stats, getModuleStats('sors', [
    'sors_rooms' => 'sors_operating_rooms',
    'sors_surgeries' => 'sors_surgeries'
]));

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CORE TRANSACTION 2 Management Dashboard</title>
    <style>
        /* Enhanced Modern Dashboard Styles with Dark Mode */
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);

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
            margin: 0;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
            transition: background-color 0.3s ease;
        }

        /* Enhanced Sidebar */
        .sidebar {
            width: 300px;
            background: var(--bg-secondary);
            color: var(--text-primary);
            padding: 2rem 1.5rem;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            box-shadow: var(--shadow-md);
            border-right: 1px solid var(--border-color);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .sidebar h3 {
            font-size: 1.8rem;
            margin-bottom: 2rem;
            text-align: center;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar li {
            margin-bottom: 0.5rem;
        }

        .sidebar a {
            text-decoration: none;
            color: var(--text-secondary);
            display: block;
            padding: 1rem 1.25rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .sidebar a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--primary-gradient);
            transition: left 0.3s ease;
            z-index: -1;
            opacity: 0.1;
        }

        .sidebar a:hover::before {
            left: 0;
        }

        .sidebar a:hover {
            color: var(--text-primary);
            background: rgba(102, 126, 234, 0.1);
            transform: translateX(8px);
        }

        .sidebar a.active {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        /* Enhanced Main Content */
        .main-content {
            margin-left: 300px;
            padding: 2rem;
            flex: 1;
            background: var(--bg-primary);
            transition: background-color 0.3s ease;
        }

        /* Modern Navbar */
        .navbar {
            background: var(--navbar-bg);
            backdrop-filter: blur(20px);
            padding: 1.5rem 2rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid var(--border-color);
        }

        .nav-brand {
            font-size: 2rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-menu {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .user-info {
            color: var(--text-secondary);
            margin-right: 1rem;
            font-weight: 500;
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
            margin-right: 1rem;
        }

        .theme-toggle:hover {
            background: var(--bg-secondary);
            border-color: #667eea;
            transform: scale(1.05);
        }

        .theme-toggle .toggle-ball {
            width: 20px;
            height: 20px;
            background: var(--primary-gradient);
            border-radius: 50%;
            transition: transform 0.3s ease;
            transform: translateX(0);
        }

        [data-theme="dark"] .theme-toggle .toggle-ball {
            transform: translateX(24px);
        }

        .theme-toggle i {
            font-size: 0.875rem;
            color: var(--text-primary);
        }

        /* Enhanced Buttons */
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }

        .btn-secondary {
            background: var(--bg-secondary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--border-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        /* Enhanced Module Cards */
        .module-card {
            background: var(--card-bg);
            margin-bottom: 2rem;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .module-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
        }

        .module-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(102, 126, 234, 0.3);
        }

        .module-card h2 {
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .module-card h2::after {
            content: '';
            flex: 1;
            height: 2px;
            background: linear-gradient(90deg, var(--border-color), transparent);
        }

        /* Enhanced Statistics Grid */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .stat-item {
            background: var(--primary-gradient);
            color: white;
            padding: 1.5rem;
            border-radius: 16px;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            transition: transform 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-4px) scale(1.02);
        }

        .stat-item::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            display: block;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stat-label {
            font-size: 0.875rem;
            opacity: 0.9;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Enhanced Animations */
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

        .module-card {
            animation: fadeInUp 0.6s ease forwards;
        }

        .module-card:nth-child(1) { animation-delay: 0.1s; }
        .module-card:nth-child(2) { animation-delay: 0.2s; }
        .module-card:nth-child(3) { animation-delay: 0.3s; }
        .module-card:nth-child(4) { animation-delay: 0.4s; }
        .module-card:nth-child(5) { animation-delay: 0.5s; }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .sidebar {
                width: 250px;
            }

            .main-content {
                margin-left: 250px;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1000;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .navbar {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .nav-menu {
                width: 100%;
                justify-content: space-between;
            }

            .theme-toggle {
                order: -1;
            }
        }

        /* Dark mode specific enhancements */
        [data-theme="dark"] .module-card {
            border-color: var(--border-color);
        }

        [data-theme="dark"] .sidebar a:hover {
            background: rgba(102, 126, 234, 0.15);
        }

        /* Loading animation */
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid var(--border-color);
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h3>🏥 CORE 2 HUB</h3>
            <ul>
                <li><a href="#dnms">🥗 DNMS (Nutrition)</a></li>
                <li><a href="#lis">🧪 LIS (Laboratory)</a></li>
                <li><a href="#pms">💊 PMS (Pharmacy)</a></li>
                <li><a href="#ris">📊 RIS (Radiology)</a></li>
                <li><a href="#sors">🏥 SORS (Surgery)</a></li>
                <?php if (isAdmin()): ?>
                <li><a href="admin.php">⚙️ Admin Panel</a></li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="main-content">
            <nav class="navbar">
                <div class="nav-brand">DASHBOARD</div>
                <div class="nav-menu">
                    <span class="user-info">Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?> (<?php echo ucfirst($_SESSION['user_role'] ?? 'user'); ?>)</span>

                    <!-- Theme Toggle -->
                    <div class="theme-toggle" onclick="toggleTheme()" title="Toggle Dark Mode">
                        <i class="fas fa-sun"></i>
                        <div class="toggle-ball"></div>
                        <i class="fas fa-moon"></i>
                    </div>

                    <a href="logout.php" class="btn btn-secondary">Logout</a>
                </div>
            </nav>


                <!-- DNMS (Nutrition) Section -->
                <div id="dnms" class="module-card">
                    <h2>🥗 DNMS - Nutrition Management</h2>
                    <div class="stat-grid">
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['dnms_foods']; ?></div>
                            <div class="stat-label">Foods</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['dnms_meals']; ?></div>
                            <div class="stat-label">Meals Logged</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['dnms_nutrition_goals']; ?></div>
                            <div class="stat-label">Active Goals</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['dnms_meal_plans']; ?></div>
                            <div class="stat-label">Meal Plans</div>
                        </div>
                    </div>
                    <p>Manage food database, meal tracking, nutrition goals, and personalized meal planning.</p>
                    <a href="dnms-php/controllers/user/dashboard.php" class="btn btn-primary">Go to DNMS</a>
                </div>

                <!-- LIS (Laboratory) Section -->
                <div id="lis" class="module-card">
                    <h2>🧪 LIS - Laboratory Information System</h2>
                    <div class="stat-grid">
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['lis_tests']; ?></div>
                            <div class="stat-label">Available Tests</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['lis_results']; ?></div>
                            <div class="stat-label">Test Results</div>
                        </div>
                    </div>
                    <p>Manage lab tests, results, and patient diagnostics.</p>
                    <a href="lis-php/index.php" class="btn btn-primary">Go to LIS</a>
                </div>

                <!-- PMS (Pharmacy) Section -->
                <div id="pms" class="module-card">
                    <h2>💊 PMS - Pharmacy Management System</h2>
                    <div class="stat-grid">
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['pms_medicines']; ?></div>
                            <div class="stat-label">Medicines</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['pms_inventory']; ?></div>
                            <div class="stat-label">Inventory Items</div>
                        </div>
                    </div>
                    <p>Manage medications, inventory, and prescriptions.</p>
                    <a href="pms-php/index.php" class="btn btn-primary">Go to PMS</a>
                </div>

                <!-- RIS (Radiology) Section -->
                <div id="ris" class="module-card">
                    <h2>📊 RIS - Radiology Information System</h2>
                    <div class="stat-grid">
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['ris_equipment']; ?></div>
                            <div class="stat-label">Equipment</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['ris_exams']; ?></div>
                            <div class="stat-label">Exams</div>
                        </div>
                    </div>
                    <p>Manage radiology equipment and imaging exams.</p>
                    <a href="ris-php/index.php" class="btn btn-primary">Go to RIS</a>
                </div>

                <!-- SORS (Surgery) Section -->
                <div id="sors" class="module-card">
                    <h2>🏥 SORS - Surgery Operating Room System</h2>
                    <div class="stat-grid">
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['sors_rooms']; ?></div>
                            <div class="stat-label">Operating Rooms</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $stats['sors_surgeries']; ?></div>
                            <div class="stat-label">Surgeries</div>
                        </div>
                    </div>
                    <p>Manage operating rooms and surgical procedures.</p>
                    <a href="sors-php/index.php" class="btn btn-primary">Go to SORS</a>
                </div>
            </div>
        </div>
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
                toggleBall.style.transform = 'translateX(24px)';
            } else {
                toggleBall.style.transform = 'translateX(0)';
            }

            // Show notification
            showNotification(`Switched to ${newTheme} mode!`, 'success');
        }

        function initializeTheme() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);

            // Update toggle button on load
            const toggleBall = document.querySelector('.toggle-ball');
            if (savedTheme === 'dark') {
                toggleBall.style.transform = 'translateX(24px)';
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

            // Add staggered animations to cards
            const cards = document.querySelectorAll('.module-card');
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
    </script>
</body>
</html>
