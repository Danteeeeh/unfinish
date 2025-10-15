<?php
/**
 * Meal History Controller
 * Handles meal history viewing and management
 */

// Prevent direct access issues
if (!defined('DNMS_ENTRY_POINT')) {
    define('DNMS_ENTRY_POINT', true);
}

// Load main config for authentication functions
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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../../login.php');
    exit();
}

// Initialize classes
require_once __DIR__ . '/../../classes/Food.php';
require_once __DIR__ . '/../../classes/Meal.php';

$food = new Food();
$meal = new Meal();

// Handle different actions
$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'view':
        showMealDetail($_GET['id'] ?? 0);
        break;
    case 'delete':
        deleteMeal($_GET['id'] ?? 0);
        break;
    default:
        showMealHistory();
        break;
}

/**
 * Enhanced Meal History Controller
 * Handles meal history viewing and management with improved error handling and security
 */

// Prevent direct access issues
if (!defined('DNMS_ENTRY_POINT')) {
    define('DNMS_ENTRY_POINT', true);
}

// Load main config for authentication functions
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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../../login.php');
    exit();
}

// Initialize classes with error handling
try {
    require_once __DIR__ . '/../../classes/Food.php';
    require_once __DIR__ . '/../../classes/Meal.php';

    $food = new Food();
    $meal = new Meal();
} catch (Exception $e) {
    error_log("DNMS Meal History Controller Initialization Error: " . $e->getMessage());
    showError("System initialization failed. Please contact support.");
}

// Rate limiting for delete operations
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $userId = $_SESSION['user_id'];
    $lastDeleteTime = $_SESSION['last_meal_delete'] ?? 0;
    $currentTime = time();

    // Allow deletion only once every 5 seconds to prevent accidental rapid deletions
    if ($currentTime - $lastDeleteTime < 5) {
        showError("Please wait a moment before deleting another meal.");
        return;
    }

    $_SESSION['last_meal_delete'] = $currentTime;
}

// Handle different actions with validation
$action = $_GET['action'] ?? 'index';

// Validate and sanitize action parameter
$validActions = ['index', 'view', 'delete', 'export'];
if (!in_array($action, $validActions)) {
    $action = 'index';
}

switch ($action) {
    case 'view':
        $mealId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($mealId === false || $mealId <= 0) {
            showError("Invalid meal ID provided.");
            break;
        }
        showMealDetail($mealId);
        break;
    case 'delete':
        $mealId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($mealId === false || $mealId <= 0) {
            showError("Invalid meal ID provided.");
            break;
        }
        deleteMeal($mealId);
        break;
    case 'export':
        exportMealHistory();
        break;
    default:
        showMealHistory();
        break;
}

/**
 * Show meal history overview with enhanced error handling and pagination
 */
function showMealHistory() {
    global $meal;

    $userId = $_SESSION['user_id'];

    // Validate user ID
    if (!$userId || !is_numeric($userId)) {
        showError("Invalid user session. Please log in again.");
        return;
    }

    try {
        // Get and validate pagination parameters
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, [
            'options' => ['default' => 1, 'min_range' => 1, 'max_range' => 1000]
        ]);

        $limit = 20; // Reasonable limit for performance
        $offset = ($page - 1) * $limit;

        // Get meals with enhanced error handling
        $meals = getUserMeals($userId, $limit, $offset);

        // Get total count for pagination with error handling
        $totalMeals = getTotalMealCount($userId);
        $totalPages = ceil($totalMeals / $limit);

        // Get daily nutrition summary with error handling
        $dailyStats = getDailyNutritionStats($userId, 7);

        // Get success/error messages from URL parameters
        $successMessage = $_GET['success'] ?? '';
        $errorMessage = $_GET['error'] ?? '';

    } catch (Exception $e) {
        error_log("Meal History Error: " . $e->getMessage());
        showError("Failed to load meal history. Please try again later.");
        return;
    }

    // Load layout and inject meal history content
    ob_start();
    include __DIR__ . '/../../views/meal/history.php';
    $historyContent = ob_get_clean();

    // Use the same layout structure as other controllers
    $layoutFile = __DIR__ . '/../../views/layouts/header.php';
    if (file_exists($layoutFile)) {
        $layoutContent = file_get_contents($layoutFile);
        // Replace the placeholder with meal history content
        $layoutContent = str_replace('<?php if (function_exists(\'displayFlashMessage\')) displayFlashMessage(); ?>', $historyContent, $layoutContent);
        echo $layoutContent;
    } else {
        // Fallback to simple layout if header.php doesn't exist
        echo $historyContent;
    }
}

/**
 * Show detailed meal view with enhanced security and validation
 */
function showMealDetail($mealId) {
    global $meal;

    // Validate meal ID
    if (!$mealId || !is_numeric($mealId) || $mealId <= 0) {
        showError("Invalid meal ID provided.");
        return;
    }

    $userId = $_SESSION['user_id'];

    try {
        // Get meal data with user ownership validation
        $mealData = $meal->getMealWithItems($mealId);

        // Verify the meal belongs to the current user
        if (!$mealData || $mealData['user_id'] != $userId) {
            showError("Meal not found or access denied.");
            return;
        }

        // Additional data validation
        if (empty($mealData['meal_name'])) {
            $mealData['meal_name'] = 'Untitled Meal';
        }

    } catch (Exception $e) {
        error_log("Meal Detail Error: " . $e->getMessage());
        showError("Failed to load meal details. Please try again later.");
        return;
    }

    // Load layout and inject meal detail content
    ob_start();
    include __DIR__ . '/../../views/meal/history.php';
    $detailContent = ob_get_clean();

    // Use the same layout structure as other controllers
    $layoutFile = __DIR__ . '/../../views/layouts/header.php';
    if (file_exists($layoutFile)) {
        $layoutContent = file_get_contents($layoutFile);
        // Replace the placeholder with meal detail content
        $layoutContent = str_replace('<?php if (function_exists(\'displayFlashMessage\')) displayFlashMessage(); ?>', $detailContent, $layoutContent);
        echo $layoutContent;
    } else {
        // Fallback to simple layout if header.php doesn't exist
        echo $detailContent;
    }
}

/**
 * Delete a meal with enhanced security and validation
 */
function deleteMeal($mealId) {
    // Validate meal ID
    if (!$mealId || !is_numeric($mealId) || $mealId <= 0) {
        showError("Invalid meal ID provided.");
        return;
    }

    $userId = $_SESSION['user_id'];

    try {
        // Verify meal ownership before deletion
        $checkStmt = getDNMSDBConnection()->prepare("SELECT user_id FROM dnms_meals WHERE id = ?");
        $checkStmt->execute([$mealId]);
        $mealOwner = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if (!$mealOwner || $mealOwner['user_id'] != $userId) {
            showError("Meal not found or access denied.");
            return;
        }

        // Delete the meal (this will cascade delete meal items due to foreign key constraints)
        $deleteStmt = getDNMSDBConnection()->prepare("DELETE FROM dnms_meals WHERE id = ? AND user_id = ?");
        $deleteStmt->execute([$mealId, $userId]);

        if ($deleteStmt->rowCount() > 0) {
            // Redirect with success message
            header('Location: ?success=meal_deleted');
        } else {
            header('Location: ?error=meal_not_found');
        }
    } catch (Exception $e) {
        error_log("Meal Deletion Error: " . $e->getMessage());
        header('Location: ?error=delete_failed');
    }
    exit();
}

/**
 * Get user meals with enhanced query and error handling
 */
function getUserMeals($userId, $limit, $offset) {
    try {
        $stmt = getDNMSDBConnection()->prepare("
            SELECT m.*, GROUP_CONCAT(f.name) as food_names,
                   GROUP_CONCAT(mi.quantity_grams) as quantities
            FROM dnms_meals m
            LEFT JOIN dnms_meal_items mi ON m.id = mi.meal_id
            LEFT JOIN dnms_foods f ON mi.food_id = f.id
            WHERE m.user_id = ?
            GROUP BY m.id
            ORDER BY m.meal_date DESC, m.meal_time DESC
            LIMIT ? OFFSET ?
        ");

        $stmt->bindParam(1, $userId, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->bindParam(3, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Get User Meals Error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get total meal count for pagination
 */
function getTotalMealCount($userId) {
    try {
        $stmt = getDNMSDBConnection()->prepare("
            SELECT COUNT(*) as total FROM dnms_meals WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        return (int)$stmt->fetch()['total'];
    } catch (Exception $e) {
        error_log("Get Total Meal Count Error: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get daily nutrition statistics with enhanced error handling
 */
function getDailyNutritionStats($userId, $days = 7) {
    try {
        // Validate days parameter
        $days = max(1, min(30, (int)$days)); // Limit to 1-30 days

        $stmt = getDNMSDBConnection()->prepare("
            SELECT
                DATE(meal_date) as date,
                AVG(total_calories) as avg_calories,
                AVG(total_protein) as avg_protein,
                AVG(total_carbs) as avg_carbs,
                AVG(total_fat) as avg_fat,
                COUNT(*) as meal_count
            FROM dnms_meals
            WHERE user_id = ? AND meal_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            GROUP BY DATE(meal_date)
            ORDER BY date DESC
        ");

        $stmt->execute([$userId, $days]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Get Daily Nutrition Stats Error: " . $e->getMessage());
        return [];
    }
}

/**
 * Export meal history to CSV format
 */
function exportMealHistory() {
    $userId = $_SESSION['user_id'];

    // Validate user ID
    if (!$userId || !is_numeric($userId)) {
        showError("Invalid user session. Please log in again.");
        return;
    }

    try {
        // Get all user meals for export
        $stmt = getDNMSDBConnection()->prepare("
            SELECT m.*, GROUP_CONCAT(f.name) as food_names,
                   GROUP_CONCAT(mi.quantity_grams) as quantities
            FROM dnms_meals m
            LEFT JOIN dnms_meal_items mi ON m.id = mi.meal_id
            LEFT JOIN dnms_foods f ON mi.food_id = f.id
            WHERE m.user_id = ?
            GROUP BY m.id
            ORDER BY m.meal_date DESC, m.meal_time DESC
        ");

        $stmt->execute([$userId]);
        $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($meals)) {
            showError("No meals found to export.");
            return;
        }

        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="meal_history_' . date('Y-m-d') . '.csv"');

        // Create output stream
        $output = fopen('php://output', 'w');

        // Add BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // CSV headers
        fputcsv($output, [
            'Date',
            'Time',
            'Meal Name',
            'Meal Type',
            'Total Calories',
            'Total Protein (g)',
            'Total Carbs (g)',
            'Total Fat (g)',
            'Foods',
            'Quantities (g)'
        ]);

        // Add meal data
        foreach ($meals as $mealData) {
            fputcsv($output, [
                date('Y-m-d', strtotime($mealData['meal_date'])),
                date('H:i', strtotime($mealData['meal_time'])),
                $mealData['meal_name'] ?? 'Untitled Meal',
                ucfirst($mealData['meal_type']),
                round($mealData['total_calories']),
                round($mealData['total_protein']),
                round($mealData['total_carbs']),
                round($mealData['total_fat']),
                $mealData['food_names'] ?? '',
                $mealData['quantities'] ?? ''
            ]);
        }

        fclose($output);
        exit();

    } catch (Exception $e) {
        error_log("Meal History Export Error: " . $e->getMessage());
        showError("Failed to export meal history. Please try again later.");
    }
}

/**
 * Enhanced error page with better styling and user feedback
 */
function showError($message) {
    ?>
    <!DOCTYPE html>
    <html lang="en" data-theme="light">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Meal History Error - NutriTrack Pro</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <style>
            :root {
                --nutrition-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                --primary-color: #ef4444;
                --bg-primary: #ffffff;
                --bg-secondary: #f8fafc;
                --text-primary: #1e293b;
                --text-secondary: #64748b;
                --border-color: #e2e8f0;
                --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }

            [data-theme="dark"] {
                --nutrition-gradient: linear-gradient(135deg, #f87171 0%, #dc2626 100%);
                --primary-color: #f87171;
                --bg-primary: #0f172a;
                --bg-secondary: #1e293b;
                --text-primary: #f1f5f9;
                --text-secondary: #cbd5e1;
                --border-color: #334155;
                --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4), 0 2px 4px -1px rgba(0, 0, 0, 0.3);
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Inter', sans-serif;
                background: var(--bg-secondary);
                color: var(--text-primary);
                margin: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: background-color 0.3s ease, color 0.3s ease;
            }

            .error-container {
                background: var(--bg-primary);
                padding: 3rem;
                border-radius: 20px;
                box-shadow: var(--shadow-md);
                text-align: center;
                max-width: 600px;
                border: 2px solid var(--border-color);
                position: relative;
                overflow: hidden;
            }

            .error-container::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: var(--nutrition-gradient);
            }

            .error-icon {
                width: 80px;
                height: 80px;
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

            .error-title {
                font-size: 2rem;
                font-weight: 700;
                color: var(--text-primary);
                margin-bottom: 1rem;
            }

            .error-message {
                color: var(--text-secondary);
                font-size: 1.125rem;
                margin-bottom: 2rem;
                line-height: 1.6;
            }

            .error-actions {
                display: flex;
                gap: 1rem;
                justify-content: center;
                flex-wrap: wrap;
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

            .btn-primary {
                background: var(--nutrition-gradient);
                color: white;
                box-shadow: var(--shadow-md);
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
            }

            .btn-secondary {
                background: var(--bg-secondary);
                color: var(--text-primary);
                border: 1px solid var(--border-color);
            }

            .btn-secondary:hover {
                background: var(--border-color);
                transform: translateY(-1px);
            }

            @keyframes pulse {
                0%, 100% {
                    transform: scale(1);
                }
                50% {
                    transform: scale(1.05);
                }
            }

            @media (max-width: 768px) {
                .error-container {
                    padding: 2rem 1.5rem;
                    margin: 1rem;
                }

                .error-actions {
                    flex-direction: column;
                    gap: 0.75rem;
                }

                .btn {
                    justify-content: center;
                }
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h1 class="error-title">⚠️ Something Went Wrong</h1>
            <p class="error-message"><?php echo htmlspecialchars($message); ?></p>
            <div class="error-actions">
                <a href="?" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i>
                    Back to Meal History
                </a>
                <a href="../food/search_foods.php" class="btn btn-secondary">
                    <i class="fas fa-search"></i>
                    Search Foods
                </a>
            </div>
        </div>

        <script>
            // Initialize theme
            document.addEventListener('DOMContentLoaded', function() {
                const savedTheme = localStorage.getItem('theme') || 'light';
                document.documentElement.setAttribute('data-theme', savedTheme);
            });
        </script>
    </body>
    </html>
    <?php
    exit();
}
?>
