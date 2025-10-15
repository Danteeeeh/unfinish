<?php $pageTitle = 'Food Search'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - NutriTrack Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
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

        /* Enhanced Food Search Styling */
        .food-search-page {
            max-width: 1600px;
            margin: 0 auto;
            padding: 2rem 1rem;
            position: relative;
        }

        .search-header {
            text-align: center;
            margin-bottom: 4rem;
            position: relative;
        }

        .theme-toggle {
            position: absolute;
            top: 0;
            right: 0;
            background: var(--bg-card);
            border: 2px solid var(--border-color);
            border-radius: 50px;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
        }

        .theme-toggle:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow-md);
        }

        .theme-toggle i {
            font-size: 1.125rem;
            color: var(--text-secondary);
            transition: color 0.3s ease;
        }

        .toggle-ball {
            width: 24px;
            height: 24px;
            background: var(--nutrition-gradient);
            border-radius: 50%;
            transition: transform 0.3s ease;
            position: relative;
        }

        [data-theme="dark"] .toggle-ball {
            transform: translateX(24px);
        }

        .search-title {
            font-size: 3.5rem;
            font-weight: 800;
            background: var(--nutrition-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
            animation: fadeInUp 0.6s ease-out;
        }

        .search-subtitle {
            font-size: 1.25rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
            animation: fadeInUp 0.6s ease-out 0.1s both;
        }

        /* Enhanced Search Section */
        .search-section {
            background: var(--bg-card);
            border-radius: 24px;
            padding: 3rem;
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--border-color);
            margin-bottom: 3rem;
            animation: slideInUp 0.6s ease-out 0.2s both;
            position: relative;
            overflow: hidden;
        }

        .search-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--nutrition-gradient);
        }

        .search-form {
            margin: 0;
        }

        .search-input-group {
            display: flex;
            gap: 1.5rem;
            align-items: center;
            max-width: 800px;
            margin: 0 auto;
        }

        .search-input {
            flex: 1;
            padding: 1.25rem 1.5rem;
            border: 2px solid var(--border-color);
            border-radius: 16px;
            font-size: 1.125rem;
            background: var(--bg-primary);
            color: var(--text-primary);
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--nutrition-gradient);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            transform: translateY(-1px);
        }

        .search-input::placeholder {
            color: var(--text-muted);
        }

        .search-btn {
            padding: 1.25rem 2.5rem;
            background: var(--nutrition-gradient);
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 1.125rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
        }

        .search-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.3s ease, height 0.3s ease;
        }

        .search-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .search-btn:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        /* Enhanced Results Section */
        .results-section {
            background: var(--bg-card);
            border-radius: 24px;
            padding: 3rem;
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--border-color);
            margin-bottom: 3rem;
            animation: slideInUp 0.6s ease-out 0.3s both;
        }

        .results-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .results-header h3 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }

        .results-count {
            background: var(--nutrition-gradient);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .food-results {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
        }

        .food-card {
            background: linear-gradient(135deg, var(--bg-primary) 0%, rgba(78, 205, 196, 0.02) 100%);
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out;
        }

        .food-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--nutrition-gradient);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .food-card:hover::before {
            transform: scaleX(1);
        }

        .food-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
            border-color: var(--nutrition-gradient);
        }

        .food-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .food-header h4 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            flex: 1;
        }

        .food-category {
            background: rgba(78, 205, 196, 0.2);
            color: var(--nutrition-gradient);
            padding: 0.375rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            border: 1px solid rgba(78, 205, 196, 0.3);
            white-space: nowrap;
        }

        .food-nutrition {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding: 1.5rem;
            background: var(--bg-secondary);
            border-radius: 12px;
        }

        .nutrition-item {
            text-align: center;
            padding: 0.75rem;
            background: var(--bg-primary);
            border-radius: 8px;
            border: 1px solid rgba(226, 232, 240, 0.5);
        }

        .nutrition-label {
            display: block;
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
            text-transform: uppercase;
            font-weight: 600;
        }

        .nutrition-value {
            display: block;
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .calories .nutrition-value { color: var(--danger-color); }
        .protein .nutrition-value { color: var(--success-color); }
        .carbs .nutrition-value { color: var(--warning-color); }
        .fat .nutrition-value { color: #8b5cf6; }

        .food-description {
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: var(--bg-secondary);
            border-radius: 8px;
            border-left: 4px solid var(--nutrition-gradient);
        }

        .food-description p {
            color: var(--text-secondary);
            line-height: 1.6;
            margin: 0;
        }

        .food-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.3s ease, height 0.3s ease;
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary {
            background: var(--nutrition-gradient);
            color: white;
            box-shadow: var(--shadow-md);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .btn-secondary {
            background: var(--bg-tertiary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--border-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Enhanced Empty States */
        .no-results, .welcome-section {
            text-align: center;
            padding: 5rem 2rem;
            background: linear-gradient(135deg, var(--bg-tertiary) 0%, rgba(78, 205, 196, 0.05) 100%);
            border-radius: 20px;
            border: 2px dashed var(--border-color);
        }

        .no-results-icon, .welcome-icon {
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

        .no-results h3, .welcome-section h3 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }

        .no-results p, .welcome-section p {
            color: var(--text-secondary);
            font-size: 1.125rem;
            margin-bottom: 2rem;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Loading State */
        .loading-state {
            text-align: center;
            padding: 5rem 2rem;
        }

        .loading-icon {
            width: 80px;
            height: 80px;
            background: var(--nutrition-gradient);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            margin-bottom: 2rem;
            animation: pulse 2s infinite;
        }

        .loading-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .loading-description {
            color: var(--text-secondary);
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

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
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

            .food-search-page {
                padding: 1rem;
            }

            .search-header {
                margin-bottom: 2rem;
            }

            .theme-toggle {
                position: relative;
                top: auto;
                right: auto;
                margin: 1rem auto;
            }

            .search-title {
                font-size: 2.5rem;
            }

            .search-section {
                padding: 2rem 1.5rem;
            }

            .search-input-group {
                flex-direction: column;
                gap: 1rem;
            }

            .results-section {
                padding: 2rem 1.5rem;
            }

            .food-results {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .food-nutrition {
                grid-template-columns: repeat(2, 1fr);
            }

            .food-header {
                flex-direction: column;
                gap: 0.75rem;
            }

            .food-actions {
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .search-title {
                font-size: 2rem;
            }

            .food-nutrition {
                grid-template-columns: 1fr;
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
        <div class="food-search-page">
            <div class="search-header">
            </div>

            <div class="search-section">
                <form method="GET" class="search-form">
                    <div class="search-input-group">
                        <input type="text" name="q" class="search-input"
                               value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>"
                               placeholder="🔍 Search thousands of foods by name, category, or nutrients...">
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i>
                            Search Foods
                        </button>
                    </div>
                </form>
            </div>

            <?php if (isset($foods) && !empty($foods)): ?>
                <div class="results-section">
                    <div class="results-header">
                        <h3>
                            🍎 Search Results
                            <span class="results-count"><?php echo count($foods); ?> foods found</span>
                        </h3>
                    </div>

                    <div class="food-results">
                        <?php foreach ($foods as $food): ?>
                            <div class="food-card">
                                <div class="food-header">
                                    <h4><?php echo htmlspecialchars($food['name']); ?></h4>
                                    <span class="food-category"><?php echo htmlspecialchars($food['category'] ?? 'General'); ?></span>
                                </div>

                                <div class="food-nutrition">
                                    <div class="nutrition-item calories">
                                        <span class="nutrition-label">Calories</span>
                                        <span class="nutrition-value"><?php echo htmlspecialchars($food['calories_per_100g'] ?? 'N/A'); ?> kcal</span>
                                    </div>
                                    <div class="nutrition-item protein">
                                        <span class="nutrition-label">Protein</span>
                                        <span class="nutrition-value"><?php echo htmlspecialchars($food['protein_per_100g'] ?? 'N/A'); ?>g</span>
                                    </div>
                                    <div class="nutrition-item carbs">
                                        <span class="nutrition-label">Carbs</span>
                                        <span class="nutrition-value"><?php echo htmlspecialchars($food['carbs_per_100g'] ?? 'N/A'); ?>g</span>
                                    </div>
                                    <div class="nutrition-item fat">
                                        <span class="nutrition-label">Fat</span>
                                        <span class="nutrition-value"><?php echo htmlspecialchars($food['fat_per_100g'] ?? 'N/A'); ?>g</span>
                                    </div>
                                </div>

                                <?php if (isset($food['description'])): ?>
                                    <div class="food-description">
                                        <p><?php echo htmlspecialchars($food['description']); ?></p>
                                    </div>
                                <?php endif; ?>

                                <div class="food-actions">
                                    <a href="../controllers/meal/add.php?food_id=<?php echo $food['id']; ?>" class="btn btn-primary">
                                        <i class="fas fa-plus"></i>
                                        Add to Meal
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php elseif (isset($_GET['q'])): ?>
                <div class="results-section">
                    <div class="no-results">
                        <div class="no-results-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3>No Foods Found</h3>
                        <p>No foods match your search criteria. Try different keywords or browse all available foods in our database.</p>
                        <a href="?" class="btn btn-secondary">
                            <i class="fas fa-list"></i>
                            Browse All Foods
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="results-section">
                    <div class="welcome-section">
                        <div class="welcome-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h3>Discover Amazing Foods</h3>
                        <p>Search through our comprehensive database of foods with detailed nutritional information. Find calories, macros, and add foods to your meals.</p>
                    </div>
                </div>
            <?php endif; ?>
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
                success: 'linear-gradient(135deg, #06b6d4 0%, #0891b2 100%)',
                error: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
                warning: 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                info: 'linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%)'
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

        // Set active nav item
        const currentPath = window.location.href;
        document.querySelectorAll('.nav-item').forEach(item => {
            if (item.href === currentPath) {
                document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
                item.classList.add('active');
            }
        });

        // Initialize theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializeTheme();

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

            // Enhanced search functionality
            const searchInput = document.querySelector('.search-input');
            if (searchInput) {
                // Add search suggestions functionality
                searchInput.addEventListener('input', function(e) {
                    const query = e.target.value.trim();
                    if (query.length > 2) {
                        // Could implement live search suggestions here
                        console.log('Searching for:', query);
                    }
                });
            }
        });

        // Add smooth scroll behavior for better UX
        document.documentElement.style.scrollBehavior = 'smooth';
    </script>
</body>
</html>
