<?php $pageTitle = 'Meal Log'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - NutriTrack Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Enhanced DNMS Styling with LIS-inspired Design */
        :root {
            --nutrition-gradient: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
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
            --sidebar-bg: #f8f9fa;
        }

        /* Dark Theme Variables */
        [data-theme="dark"] {
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
            box-shadow: 0 4px 15px rgba(78, 205, 196, 0.3);
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
            background: rgba(78, 205, 196, 0.1);
            transform: translateX(8px);
        }

        .nav-item.active {
            background: var(--nutrition-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(78, 205, 196, 0.3);
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

        /* Meal Log Page Styling */
        .meal-log-page {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .log-header {
            text-align: center;
            margin-bottom: 4rem;
            position: relative;
        }

        .log-title {
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

        .log-subtitle {
            font-size: 1.25rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
            animation: fadeInUp 0.6s ease-out 0.1s both;
        }

        /* Enhanced Meal Logging Content */
        .meal-log-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .meal-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .meal-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            background: var(--nutrition-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .meal-header p {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        .meal-form-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
            animation: slideInUp 0.6s ease-out;
        }

        .form-section {
            margin-bottom: 2.5rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid var(--border-color);
        }

        .form-section:last-of-type {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .form-section h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-group input, .form-group select {
            padding: 0.875rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            font-size: 1rem;
            background: var(--bg-primary);
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: var(--nutrition-gradient);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .food-items-container {
            margin-bottom: 1.5rem;
        }

        .food-item {
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .food-item:hover {
            box-shadow: var(--shadow-md);
            border-color: var(--nutrition-gradient);
        }

        .food-item-header {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .food-select {
            flex: 1;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-primary);
            color: var(--text-primary);
        }

        .remove-food-btn {
            background: var(--danger-gradient);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.75rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .remove-food-btn:hover {
            transform: scale(1.05);
        }

        .food-quantity {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .quantity-input {
            flex: 1;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-primary);
            color: var(--text-primary);
        }

        .quantity-unit {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .food-nutrition-preview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 0.75rem;
            padding: 1rem;
            background: var(--bg-primary);
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .nutrition-preview-item {
            text-align: center;
        }

        .preview-label {
            display: block;
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
            text-transform: uppercase;
            font-weight: 600;
        }

        .preview-value {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .add-food-btn {
            background: var(--nutrition-gradient);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 1rem 2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0 auto;
        }

        .add-food-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .meal-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            padding: 1.5rem;
            background: var(--bg-secondary);
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        .summary-item {
            text-align: center;
        }

        .summary-label {
            display: block;
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
            text-transform: uppercase;
            font-weight: 600;
        }

        .summary-value {
            display: block;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .form-actions {
            text-align: center;
            padding-top: 2rem;
        }

        .submit-btn {
            background: var(--nutrition-gradient);
            color: white;
            border: none;
            border-radius: 16px;
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-xl);
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

            .meal-log-page {
                padding: 1rem;
            }

            .log-title {
                font-size: 2.5rem;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .food-nutrition-preview {
                grid-template-columns: repeat(2, 1fr);
            }

            .meal-summary {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .log-title {
                font-size: 2rem;
            }

            .food-nutrition-preview {
                grid-template-columns: 1fr;
            }

            .meal-summary {
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
            <a href="planner.php" class="nav-item">
                <i class="fas fa-calendar-alt"></i>
                <span>Meal Planner</span>
            </a>
            <a href="log.php" class="nav-item active">
                <i class="fas fa-book"></i>
                <span>Meal Log</span>
            </a>
            <a href="history.php" class="nav-item">
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
        <div class="meal-log-page">
            <div class="log-header">
                <h1 class="log-title">📝 Log Meal</h1>
                <p class="log-subtitle">Record your meals and track your nutrition intake with precision</p>
            </div>
<div class="meal-log-container">
    <div class="meal-header">
        <h1>Log a Meal</h1>
        <p>Record your meals and track your nutrition intake</p>
    </div>

    <div class="meal-form-card">
        <form method="POST" class="meal-form">
            <!-- Meal Basic Info -->
            <div class="form-section">
                <h3>Meal Information</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="meal_name">Meal Name *</label>
                        <input type="text" id="meal_name" name="meal_name" required
                               placeholder="e.g., Breakfast, Lunch, Dinner">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="meal_date">Date *</label>
                        <input type="date" id="meal_date" name="meal_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="meal_time">Time *</label>
                        <input type="time" id="meal_time" name="meal_time" value="<?php echo date('H:i'); ?>" required>
                    </div>
                </div>
            </div>

            <!-- Food Selection -->
            <div class="form-section">
                <h3>Foods in this Meal</h3>

                <div id="food-items" class="food-items-container">
                    <div class="food-item" data-index="0">
                        <div class="food-item-header">
                            <select name="foods[0][food_id]" class="food-select" required>
                                <option value="">Select Food</option>
                                <?php if (isset($foods)): foreach ($foods as $food): ?>
                                    <option value="<?php echo $food['id']; ?>" data-calories="<?php echo $food['calories_per_100g'] ?? 0; ?>"
                                            data-protein="<?php echo $food['protein_per_100g'] ?? 0; ?>"
                                            data-carbs="<?php echo $food['carbs_per_100g'] ?? 0; ?>"
                                            data-fat="<?php echo $food['fat_per_100g'] ?? 0; ?>">
                                        <?php echo htmlspecialchars($food['name']); ?>
                                    </option>
                                <?php endforeach; endif; ?>
                            </select>
                            <button type="button" class="remove-food-btn" onclick="removeFoodItem(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <div class="food-quantity">
                            <input type="number" name="foods[0][quantity]" placeholder="Quantity (g)" min="1" step="0.1"
                                   class="quantity-input" required>
                            <span class="quantity-unit">grams</span>
                        </div>

                        <div class="food-nutrition-preview">
                            <div class="nutrition-preview-item">
                                <span class="preview-label">Calories:</span>
                                <span class="preview-value calories-value">0 kcal</span>
                            </div>
                            <div class="nutrition-preview-item">
                                <span class="preview-label">Protein:</span>
                                <span class="preview-value protein-value">0g</span>
                            </div>
                            <div class="nutrition-preview-item">
                                <span class="preview-label">Carbs:</span>
                                <span class="preview-value carbs-value">0g</span>
                            </div>
                            <div class="nutrition-preview-item">
                                <span class="preview-label">Fat:</span>
                                <span class="preview-value fat-value">0g</span>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="add-food-btn" onclick="addFoodItem()">
                    <i class="fas fa-plus"></i>
                    Add Another Food
                </button>
            </div>

            <!-- Meal Summary -->
            <div class="form-section">
                <h3>Meal Summary</h3>

                <div class="meal-summary">
                    <div class="summary-item">
                        <span class="summary-label">Total Calories:</span>
                        <span class="summary-value total-calories">0 kcal</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Total Protein:</span>
                        <span class="summary-value total-protein">0g</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Total Carbs:</span>
                        <span class="summary-value total-carbs">0g</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Total Fat:</span>
                        <span class="summary-value total-fat">0g</span>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="form-actions">
                <button type="submit" class="submit-btn">
                    <i class="fas fa-save"></i>
                    Log Meal
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Meal Logging Styling with LIS Design */
    .meal-log-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .meal-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .meal-header h1 {
        font-size: 32px;
        font-weight: 800;
        background: var(--nutrition-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 10px;
    }

    .meal-header p {
        color: var(--text-secondary);
        font-size: 16px;
    }

    .meal-form-card {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 30px;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border-color);
    }

    .form-section {
        margin-bottom: 40px;
        padding-bottom: 30px;
        border-bottom: 1px solid var(--border-color);
    }

    .form-section:last-of-type {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .form-section h3 {
        font-size: 20px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-group input, .form-group select {
        padding: 12px 15px;
        border: 2px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
        background: var(--bg-primary);
        color: var(--text-primary);
        transition: border-color 0.3s ease;
    }

    .form-group input:focus, .form-group select:focus {
        outline: none;
        border-color: var(--nutrition-gradient);
    }

    .food-items-container {
        margin-bottom: 20px;
    }

    .food-item {
        background: var(--bg-secondary);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid var(--border-color);
    }

    .food-item-header {
        display: flex;
        gap: 15px;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .food-select {
        flex: 1;
        padding: 10px 12px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        background: var(--bg-primary);
        color: var(--text-primary);
    }

    .remove-food-btn {
        background: var(--danger-gradient);
        color: white;
        border: none;
        border-radius: 6px;
        padding: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .remove-food-btn:hover {
        transform: scale(1.05);
    }

    .food-quantity {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }

    .quantity-input {
        flex: 1;
        padding: 10px 12px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        background: var(--bg-primary);
        color: var(--text-primary);
    }

    .quantity-unit {
        color: var(--text-secondary);
        font-size: 14px;
    }

    .food-nutrition-preview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 10px;
        padding: 10px;
        background: var(--bg-primary);
        border-radius: 6px;
        border: 1px solid var(--border-color);
    }

    .nutrition-preview-item {
        text-align: center;
    }

    .preview-label {
        display: block;
        font-size: 11px;
        color: var(--text-secondary);
        margin-bottom: 2px;
        text-transform: uppercase;
    }

    .preview-value {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .add-food-btn {
        background: var(--nutrition-gradient);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 12px 20px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0 auto;
    }

    .add-food-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(78, 205, 196, 0.3);
    }

    .meal-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        padding: 20px;
        background: var(--bg-secondary);
        border-radius: 8px;
        border: 1px solid var(--border-color);
    }

    .summary-item {
        text-align: center;
    }

    .summary-label {
        display: block;
        font-size: 12px;
        color: var(--text-secondary);
        margin-bottom: 5px;
        text-transform: uppercase;
        font-weight: 600;
    }

    .summary-value {
        display: block;
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
    }

    .form-actions {
        text-align: center;
        padding-top: 20px;
    }

    .submit-btn {
        background: var(--nutrition-gradient);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 15px 30px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(78, 205, 196, 0.3);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .food-nutrition-preview {
            grid-template-columns: repeat(2, 1fr);
        }

        .meal-summary {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .food-nutrition-preview {
            grid-template-columns: 1fr;
        }

        .meal-summary {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
let foodItemIndex = 1;

function addFoodItem() {
    const container = document.getElementById('food-items');
    const template = container.querySelector('.food-item');

    const newItem = template.cloneNode(true);
    newItem.setAttribute('data-index', foodItemIndex);

    // Update indices in form fields
    const select = newItem.querySelector('.food-select');
    const quantityInput = newItem.querySelector('.quantity-input');
    const previewValues = newItem.querySelectorAll('.preview-value');

    select.name = `foods[${foodItemIndex}][food_id]`;
    quantityInput.name = `foods[${foodItemIndex}][quantity]`;

    // Reset values
    select.selectedIndex = 0;
    quantityInput.value = '';
    previewValues.forEach(preview => preview.textContent = '0');

    container.appendChild(newItem);
    foodItemIndex++;

    // Re-add event listeners
    addFoodItemEventListeners(newItem);
}

function removeFoodItem(button) {
    const foodItem = button.closest('.food-item');
    if (document.querySelectorAll('.food-item').length > 1) {
        foodItem.remove();
        updateMealSummary();
    }
}

function addFoodItemEventListeners(foodItem) {
    const select = foodItem.querySelector('.food-select');
    const quantityInput = foodItem.querySelector('.quantity-input');

    select.addEventListener('change', function() {
        updateNutritionPreview(foodItem, this, quantityInput);
        updateMealSummary();
    });

    quantityInput.addEventListener('input', function() {
        updateNutritionPreview(foodItem, select, this);
        updateMealSummary();
    });
}

function updateNutritionPreview(foodItem, select, quantityInput) {
    const selectedOption = select.options[select.selectedIndex];
    const quantity = parseFloat(quantityInput.value) || 0;

    if (selectedOption.value && quantity > 0) {
        const caloriesPer100g = parseFloat(selectedOption.getAttribute('data-calories')) || 0;
        const proteinPer100g = parseFloat(selectedOption.getAttribute('data-protein')) || 0;
        const carbsPer100g = parseFloat(selectedOption.getAttribute('data-carbs')) || 0;
        const fatPer100g = parseFloat(selectedOption.getAttribute('data-fat')) || 0;

        const calories = (caloriesPer100g * quantity / 100).toFixed(1);
        const protein = (proteinPer100g * quantity / 100).toFixed(1);
        const carbs = (carbsPer100g * quantity / 100).toFixed(1);
        const fat = (fatPer100g * quantity / 100).toFixed(1);

        foodItem.querySelector('.calories-value').textContent = `${calories} kcal`;
        foodItem.querySelector('.protein-value').textContent = `${protein}g`;
        foodItem.querySelector('.carbs-value').textContent = `${carbs}g`;
        foodItem.querySelector('.fat-value').textContent = `${fat}g`;
    } else {
        foodItem.querySelector('.calories-value').textContent = '0 kcal';
        foodItem.querySelector('.protein-value').textContent = '0g';
        foodItem.querySelector('.carbs-value').textContent = '0g';
        foodItem.querySelector('.fat-value').textContent = '0g';
    }
}

function updateMealSummary() {
    let totalCalories = 0;
    let totalProtein = 0;
    let totalCarbs = 0;
    let totalFat = 0;

    document.querySelectorAll('.food-item').forEach(item => {
        const caloriesText = item.querySelector('.calories-value').textContent;
        const proteinText = item.querySelector('.protein-value').textContent;
        const carbsText = item.querySelector('.carbs-value').textContent;
        const fatText = item.querySelector('.fat-value').textContent;

        totalCalories += parseFloat(caloriesText.replace(' kcal', '')) || 0;
        totalProtein += parseFloat(proteinText.replace('g', '')) || 0;
        totalCarbs += parseFloat(carbsText.replace('g', '')) || 0;
        totalFat += parseFloat(fatText.replace('g', '')) || 0;
    });

    document.querySelector('.total-calories').textContent = `${Math.round(totalCalories)} kcal`;
    document.querySelector('.total-protein').textContent = `${totalProtein.toFixed(1)}g`;
    document.querySelector('.total-carbs').textContent = `${totalCarbs.toFixed(1)}g`;
    document.querySelector('.total-fat').textContent = `${totalFat.toFixed(1)}g`;
}

// Initialize event listeners for existing food items
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.food-item').forEach(addFoodItemEventListeners);
});
</script>

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
                font-weight: 500;
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
        });

        // Add smooth scroll behavior for better UX
        document.documentElement.style.scrollBehavior = 'smooth';
    </script>
</body>
</html>
