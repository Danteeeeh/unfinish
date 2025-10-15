<?php
/**
 * Add Food to Meal Controller
 * Handles adding specific foods to user meals
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
    require_once __DIR__ . '/../../config/database.php';
    require_once __DIR__ . '/../../config/constants.php';
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

$food = new Food();
$meal = new Meal();

// Get and validate food_id
$foodId = filter_input(INPUT_GET, 'food_id', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1]
]);

if (!$foodId) {
    showError("Invalid food ID provided.");
    exit();
}

try {
    // Get food details
    $foodDetails = $food->getFoodById($foodId);

    if (!$foodDetails) {
        showError("Food not found.");
        exit();
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        handleAddToMeal($foodDetails, $meal);
    } else {
        // Show add to meal form
        showAddToMealForm($foodDetails);
    }

} catch (Exception $e) {
    error_log("DNMS Add Food Error: " . $e->getMessage());
    showError("Failed to process request: " . htmlspecialchars($e->getMessage()));
}

/**
 * Handle adding food to meal
 */
function handleAddToMeal($foodDetails, $meal) {
    $userId = $_SESSION['user_id'];
    $foodId = $foodDetails['id'];
    
    // Validate and sanitize inputs
    $servingSize = filter_input(INPUT_POST, 'serving_size', FILTER_VALIDATE_FLOAT, [
        'options' => ['min_range' => 1, 'max_range' => 10000]
    ]);
    
    $mealType = filter_input(INPUT_POST, 'meal_type', FILTER_SANITIZE_STRING);
    $validMealTypes = ['breakfast', 'lunch', 'dinner', 'snack'];
    
    $mealDate = filter_input(INPUT_POST, 'meal_date', FILTER_SANITIZE_STRING);
    $mealName = filter_input(INPUT_POST, 'meal_name', FILTER_SANITIZE_STRING);

    // Validate input
    if (!$servingSize) {
        showAddToMealForm($foodDetails, "Invalid serving size. Please enter a value between 1 and 10000.");
        return;
    }

    if (!in_array($mealType, $validMealTypes)) {
        showAddToMealForm($foodDetails, "Invalid meal type.");
        return;
    }

    try {
        // Calculate nutrition based on serving size
        $servingRatio = $servingSize / ($foodDetails['serving_size'] ?? 100);

        $nutrition = [
            'calories' => round(($foodDetails['calories_per_serving'] ?? 0) * $servingRatio, 2),
            'protein' => round(($foodDetails['protein_per_serving'] ?? 0) * $servingRatio, 2),
            'carbs' => round(($foodDetails['carbs_per_serving'] ?? 0) * $servingRatio, 2),
            'fat' => round(($foodDetails['fat_per_serving'] ?? 0) * $servingRatio, 2),
            'fiber' => round(($foodDetails['fiber_per_serving'] ?? 0) * $servingRatio, 2)
        ];

        // Create or update meal
        $mealData = [
            'user_id' => $userId,
            'meal_name' => $mealName ?: ucfirst($mealType) . ' - ' . date('M j'),
            'meal_date' => $mealDate ?: date('Y-m-d'),
            'meal_time' => date('H:i:s'),
            'meal_type' => $mealType,
            'total_calories' => $nutrition['calories'],
            'total_protein' => $nutrition['protein'],
            'total_carbs' => $nutrition['carbs'],
            'total_fat' => $nutrition['fat'],
            'foods' => [[
                'food_id' => $foodId,
                'quantity_grams' => $servingSize,
                'calories' => $nutrition['calories'],
                'protein' => $nutrition['protein'],
                'carbs' => $nutrition['carbs'],
                'fat' => $nutrition['fat']
            ]]
        ];

        $mealId = $meal->createMeal($mealData);

        if ($mealId) {
            // Redirect to meal view or dashboard
            header('Location: ../../user/dashboard.php?success=food_added');
            exit();
        } else {
            showAddToMealForm($foodDetails, "Failed to save meal. Please try again.");
        }

    } catch (Exception $e) {
        showAddToMealForm($foodDetails, "Error saving meal: " . htmlspecialchars($e->getMessage()));
    }
}

/**
 * Show the add food to meal form
 */
function showAddToMealForm($foodDetails, $errorMessage = '') {
    $pageTitle = 'Add ' . htmlspecialchars($foodDetails['name']) . ' to Meal';
    ?>
    <!DOCTYPE html>
    <html lang="en" data-theme="light">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $pageTitle; ?> - NutriTrack Pro</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <style>
            :root {
                /* Light Theme Colors */
                --nutrition-gradient: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
                --primary-color: #4ecdc4;
                --secondary-color: #44a08d;
                --accent-color: #093637;
                --success-color: #10b981;
                --warning-color: #f59e0b;
                --danger-color: #ef4444;
                --info-color: #3b82f6;

                --bg-primary: #ffffff;
                --bg-secondary: #f8fafc;
                --bg-tertiary: #f1f5f9;
                --bg-card: #ffffff;
                --text-primary: #1e293b;
                --text-secondary: #64748b;
                --text-muted: #94a3b8;
                --border-color: #e2e8f0;
                --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            }

            /* Dark Theme Colors */
            [data-theme="dark"] {
                --nutrition-gradient: linear-gradient(135deg, #64b5f6 0%, #42a5f5 100%);
                --primary-color: #64b5f6;
                --secondary-color: #42a5f5;
                --accent-color: #1e3a8a;
                --success-color: #4ade80;
                --warning-color: #fbbf24;
                --danger-color: #f87171;
                --info-color: #60a5fa;

                --bg-primary: #0f172a;
                --bg-secondary: #1e293b;
                --bg-tertiary: #334155;
                --bg-card: #1e293b;
                --text-primary: #f1f5f9;
                --text-secondary: #cbd5e1;
                --text-muted: #94a3b8;
                --border-color: #334155;
                --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
                --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4), 0 2px 4px -1px rgba(0, 0, 0, 0.3);
                --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.4), 0 4px 6px -2px rgba(0, 0, 0, 0.3);
                --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 10px 10px -5px rgba(0, 0, 0, 0.3);
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            html {
                transition: background-color 0.3s ease, color 0.3s ease;
            }

            body {
                font-family: 'Inter', sans-serif;
                background: var(--bg-secondary);
                color: var(--text-primary);
                line-height: 1.6;
                min-height: 100vh;
                transition: background-color 0.3s ease, color 0.3s ease;
            }

            /* Animated Background */
            body::before {
                content: '';
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: radial-gradient(circle at 20% 50%, rgba(78, 205, 196, 0.03) 0%, transparent 50%),
                            radial-gradient(circle at 80% 20%, rgba(100, 181, 246, 0.03) 0%, transparent 50%),
                            radial-gradient(circle at 40% 80%, rgba(78, 205, 196, 0.03) 0%, transparent 50%);
                pointer-events: none;
                z-index: -1;
                transition: opacity 0.3s ease;
            }

            [data-theme="dark"] body::before {
                opacity: 0.5;
            }

            .container {
                max-width: 900px;
                margin: 0 auto;
                padding: 2rem 1rem;
                position: relative;
            }

            /* Header with Theme Toggle */
            .page-header {
                text-align: center;
                margin-bottom: 3rem;
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

            .page-title {
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

            .page-subtitle {
                font-size: 1.25rem;
                color: var(--text-secondary);
                animation: fadeInUp 0.6s ease-out 0.1s both;
            }

            .form-card {
                background: var(--bg-card);
                border-radius: 24px;
                padding: 3rem;
                box-shadow: var(--shadow-xl);
                border: 1px solid var(--border-color);
                animation: slideInUp 0.6s ease-out 0.2s both;
                position: relative;
                overflow: hidden;
            }

            .form-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: var(--nutrition-gradient);
            }

            .form-header {
                text-align: center;
                margin-bottom: 2.5rem;
            }

            .form-title {
                font-size: 2.25rem;
                font-weight: 700;
                color: var(--text-primary);
                margin-bottom: 0.5rem;
                animation: fadeInUp 0.6s ease-out 0.3s both;
            }

            .food-info {
                background: linear-gradient(135deg, rgba(78, 205, 196, 0.1) 0%, rgba(100, 181, 246, 0.1) 100%);
                padding: 1.5rem;
                border-radius: 16px;
                margin-bottom: 2rem;
                display: flex;
                align-items: center;
                gap: 1.5rem;
                border: 1px solid rgba(78, 205, 196, 0.2);
                animation: fadeInUp 0.6s ease-out 0.4s both;
            }

            .food-icon {
                width: 60px;
                height: 60px;
                background: var(--nutrition-gradient);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 1.5rem;
                box-shadow: var(--shadow-md);
                animation: pulse 2s infinite;
            }

            .food-details h3 {
                font-size: 1.5rem;
                font-weight: 600;
                color: var(--text-primary);
                margin-bottom: 0.5rem;
            }

            .food-category {
                background: rgba(78, 205, 196, 0.2);
                color: var(--primary-color);
                padding: 0.375rem 1rem;
                border-radius: 20px;
                font-size: 0.875rem;
                font-weight: 500;
                border: 1px solid rgba(78, 205, 196, 0.3);
            }

            .form-group {
                margin-bottom: 2rem;
                animation: fadeInUp 0.6s ease-out 0.5s both;
            }

            .form-label {
                display: block;
                font-weight: 600;
                color: var(--text-primary);
                margin-bottom: 0.75rem;
                font-size: 0.95rem;
            }

            .form-input, .form-select {
                width: 100%;
                padding: 1rem 1.25rem;
                border: 2px solid var(--border-color);
                border-radius: 12px;
                font-size: 1rem;
                background: var(--bg-primary);
                color: var(--text-primary);
                transition: all 0.3s ease;
                font-family: inherit;
            }

            .form-input:focus, .form-select:focus {
                outline: none;
                border-color: var(--primary-color);
                box-shadow: 0 0 0 3px rgba(78, 205, 196, 0.1);
                transform: translateY(-1px);
            }

            .form-input:hover, .form-select:hover {
                border-color: rgba(78, 205, 196, 0.5);
            }

            .nutrition-preview {
                background: linear-gradient(135deg, var(--bg-tertiary) 0%, rgba(78, 205, 196, 0.05) 100%);
                padding: 1.5rem;
                border-radius: 16px;
                margin-top: 1.5rem;
                border: 1px solid rgba(78, 205, 196, 0.2);
                animation: fadeInUp 0.6s ease-out 0.6s both;
            }

            .nutrition-preview h4 {
                font-size: 1.125rem;
                font-weight: 600;
                color: var(--text-primary);
                margin-bottom: 1rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .nutrition-preview h4::before {
                content: '📊';
                font-size: 1.25rem;
            }

            .nutrition-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
                gap: 1rem;
                margin-top: 1rem;
            }

            .nutrition-item {
                text-align: center;
                padding: 1rem;
                background: var(--bg-primary);
                border-radius: 12px;
                border: 1px solid var(--border-color);
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }

            .nutrition-item::before {
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

            .nutrition-item:hover::before {
                transform: scaleX(1);
            }

            .nutrition-item:hover {
                transform: translateY(-2px);
                box-shadow: var(--shadow-md);
            }

            .nutrition-label {
                font-size: 0.75rem;
                color: var(--text-secondary);
                margin-bottom: 0.5rem;
                text-transform: uppercase;
                font-weight: 600;
                letter-spacing: 0.05em;
            }

            .nutrition-value {
                font-size: 1.25rem;
                font-weight: 700;
                color: var(--text-primary);
                transition: color 0.3s ease;
            }

            .calories .nutrition-value { color: #ef4444; }
            .protein .nutrition-value { color: #10b981; }
            .carbs .nutrition-value { color: #f59e0b; }
            .fat .nutrition-value { color: #8b5cf6; }

            .form-actions {
                display: flex;
                gap: 1rem;
                justify-content: flex-end;
                margin-top: 3rem;
                padding-top: 2rem;
                border-top: 1px solid var(--border-color);
                animation: fadeInUp 0.6s ease-out 0.7s both;
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

            .error-message {
                background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.05) 100%);
                border: 1px solid rgba(239, 68, 68, 0.3);
                color: var(--danger-color);
                padding: 1rem;
                border-radius: 12px;
                margin-bottom: 1.5rem;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                animation: shake 0.5s ease-in-out;
            }

            /* Loading Animation */
            .loading-state {
                text-align: center;
                padding: 4rem 2rem;
                animation: fadeInUp 0.6s ease-out;
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
                margin-bottom: 1.5rem;
                animation: pulse 2s infinite;
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

            @keyframes shake {
                0%, 100% {
                    transform: translateX(0);
                }
                25% {
                    transform: translateX(-5px);
                }
                75% {
                    transform: translateX(5px);
                }
            }

            @keyframes bounce {
                0%, 20%, 53%, 80%, 100% {
                    transform: translate3d(0,0,0);
                }
                40%, 43% {
                    transform: translate3d(0, -8px, 0);
                }
                70% {
                    transform: translate3d(0, -4px, 0);
                }
                90% {
                    transform: translate3d(0, -2px, 0);
                }
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .container {
                    padding: 1rem;
                }

                .page-header {
                    margin-bottom: 2rem;
                }

                .theme-toggle {
                    position: relative;
                    top: auto;
                    right: auto;
                    margin: 1rem auto;
                }

                .page-title {
                    font-size: 2.5rem;
                }

                .form-card {
                    padding: 2rem 1.5rem;
                    border-radius: 20px;
                }

                .food-info {
                    flex-direction: column;
                    text-align: center;
                    gap: 1rem;
                }

                .nutrition-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .form-actions {
                    flex-direction: column;
                    gap: 0.75rem;
                }

                .btn {
                    justify-content: center;
                }
            }

            @media (max-width: 480px) {
                .page-title {
                    font-size: 2rem;
                }

                .form-title {
                    font-size: 1.75rem;
                }

                .nutrition-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="page-header">
                <div class="theme-toggle" onclick="toggleTheme()" title="Toggle Dark Mode">
                    <i class="fas fa-sun"></i>
                    <div class="toggle-ball"></div>
                    <i class="fas fa-moon"></i>
                </div>
            </div>

            <div class="form-card">
                <div class="form-header">
                    <h1 class="form-title">🍽️ Add to Meal</h1>
                </div>

                <?php if ($errorMessage): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo htmlspecialchars($errorMessage); ?>
                    </div>
                <?php endif; ?>

                <div class="food-info">
                    <div class="food-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="food-details">
                        <h3><?php echo htmlspecialchars($foodDetails['name']); ?></h3>
                        <span class="food-category"><?php echo htmlspecialchars($foodDetails['category'] ?? 'General'); ?></span>
                    </div>
                </div>

                <form method="POST" id="addToMealForm">
                    <div class="form-group">
                        <label for="serving_size" class="form-label">
                            <i class="fas fa-weight"></i>
                            Serving Size (<?php echo htmlspecialchars($foodDetails['serving_unit'] ?? 'g'); ?>)
                        </label>
                        <input type="number" id="serving_size" name="serving_size" class="form-input"
                               value="100" min="1" max="10000" step="0.1" required
                               oninput="updateNutritionPreview()" placeholder="Enter serving size...">
                    </div>

                    <div class="form-group">
                        <label for="meal_type" class="form-label">
                            <i class="fas fa-utensils"></i>
                            Meal Type
                        </label>
                        <select id="meal_type" name="meal_type" class="form-select" onchange="updateMealName()">
                            <option value="breakfast">🌅 Breakfast</option>
                            <option value="lunch" selected>☀️ Lunch</option>
                            <option value="dinner">🌙 Dinner</option>
                            <option value="snack">🍎 Snack</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="meal_date" class="form-label">
                            <i class="fas fa-calendar-alt"></i>
                            Date
                        </label>
                        <input type="date" id="meal_date" name="meal_date" class="form-input"
                               value="<?php echo date('Y-m-d'); ?>" required onchange="updateMealName()">
                    </div>

                    <div class="form-group">
                        <label for="meal_name" class="form-label">
                            <i class="fas fa-tag"></i>
                            Meal Name (Optional)
                        </label>
                        <input type="text" id="meal_name" name="meal_name" class="form-input"
                               placeholder="Custom meal name..." oninput="updateMealName()">
                    </div>

                    <div class="nutrition-preview">
                        <h4>📊 Live Nutrition Preview</h4>
                        <div class="nutrition-grid" id="nutritionGrid">
                            <div class="nutrition-item calories">
                                <div class="nutrition-label">Calories</div>
                                <div class="nutrition-value" id="calories">0</div>
                            </div>
                            <div class="nutrition-item protein">
                                <div class="nutrition-label">Protein</div>
                                <div class="nutrition-value" id="protein">0g</div>
                            </div>
                            <div class="nutrition-item carbs">
                                <div class="nutrition-label">Carbohydrates</div>
                                <div class="nutrition-value" id="carbs">0g</div>
                            </div>
                            <div class="nutrition-item fat">
                                <div class="nutrition-label">Fat</div>
                                <div class="nutrition-value" id="fat">0g</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="../../food/search_foods.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Back to Search
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add to Meal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            // Theme Management
            function toggleTheme() {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);

                // Show notification
                showNotification(`Switched to ${newTheme} mode!`, 'success');
            }

            function initializeTheme() {
                const savedTheme = localStorage.getItem('theme') || 'light';
                document.documentElement.setAttribute('data-theme', savedTheme);
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

            // Food nutrition data (per serving)
            const foodData = {
                calories_per_serving: <?php echo $foodDetails['calories_per_serving'] ?? 0; ?>,
                protein_per_serving: <?php echo $foodDetails['protein_per_serving'] ?? 0; ?>,
                carbs_per_serving: <?php echo $foodDetails['carbs_per_serving'] ?? 0; ?>,
                fat_per_serving: <?php echo $foodDetails['fat_per_serving'] ?? 0; ?>
            };

            function updateNutritionPreview() {
                const servingSize = parseFloat(document.getElementById('serving_size').value) || 0;
                const ratio = servingSize / 100; // Assuming base serving is 100g

                const calories = Math.round(foodData.calories_per_serving * ratio);
                const protein = (foodData.protein_per_serving * ratio).toFixed(1);
                const carbs = (foodData.carbs_per_serving * ratio).toFixed(1);
                const fat = (foodData.fat_per_serving * ratio).toFixed(1);

                document.getElementById('calories').textContent = calories;
                document.getElementById('protein').textContent = protein + 'g';
                document.getElementById('carbs').textContent = carbs + 'g';
                document.getElementById('fat').textContent = fat + 'g';

                // Animate value changes
                animateValueChange('calories', calories);
                animateValueChange('protein', protein);
                animateValueChange('carbs', carbs);
                animateValueChange('fat', fat);
            }

            function animateValueChange(elementId, newValue) {
                const element = document.getElementById(elementId);
                element.style.transform = 'scale(1.1)';
                setTimeout(() => {
                    element.style.transform = 'scale(1)';
                }, 200);
            }

            function updateMealName() {
                const mealType = document.getElementById('meal_type').value;
                const mealDate = document.getElementById('meal_date').value;
                const customName = document.getElementById('meal_name').value;

                if (!customName.trim()) {
                    const dateObj = new Date(mealDate + 'T00:00:00');
                    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    const month = monthNames[dateObj.getMonth()];
                    const day = dateObj.getDate();

                    const mealEmojis = {
                        'breakfast': '🌅',
                        'lunch': '☀️',
                        'dinner': '🌙',
                        'snack': '🍎'
                    };

                    document.getElementById('meal_name').value = mealEmojis[mealType] + ' ' + ucfirst(mealType) + ' - ' + month + ' ' + day;
                }
            }

            function ucfirst(str) {
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            // Initialize theme and nutrition preview
            document.addEventListener('DOMContentLoaded', function() {
                initializeTheme();
                updateNutritionPreview();
                updateMealName();

                // Add CSS animations
                const style = document.createElement('style');
                style.textContent = `
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
                document.head.appendChild(style);
            });

            // Form validation
            document.getElementById('addToMealForm').addEventListener('submit', function(e) {
                const servingSize = document.getElementById('serving_size').value;

                if (servingSize <= 0 || servingSize > 10000) {
                    e.preventDefault();
                    showNotification('Please enter a valid serving size (1-10000g)', 'warning');
                    return false;
                }

                // Show loading state
                const submitBtn = document.querySelector('.btn-primary');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
                submitBtn.disabled = true;

                // Re-enable after a delay (in case of success)
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 3000);
            });
        </script>
    </body>
    </html>
    <?php
}

/**
 * Show error page
 */
function showError($message) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add Food Error - NutriTrack Pro</title>
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
            <h1>❌ Add Food Error</h1>
            <p><?php echo htmlspecialchars($message); ?></p>
            <a href="../food/search_foods.php" class="btn">Back to Food Search</a>
        </div>
    </body>
    </html>
    <?php
    exit();
}
?>
