<?php
$pageTitle = 'Meal History';

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

// Authentication utility functions
function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? $_SESSION['user_id'],
            'email' => $_SESSION['email'] ?? '',
            'role' => $_SESSION['role'] ?? $_SESSION['user_role'] ?? 'user',
            'full_name' => $_SESSION['full_name'] ?? $_SESSION['user_name'] ?? $_SESSION['user_id']
        ];
    }
    return null;
}

function hasRole($roles) {
    if (!isLoggedIn()) {
        return false;
    }

    if (!is_array($roles)) {
        $roles = [$roles];
    }

    $userRole = $_SESSION['role'] ?? $_SESSION['user_role'] ?? 'user';
    return in_array($userRole, $roles);
}

function requireRole($roles) {
    requireLogin();

    if (!hasRole($roles)) {
        setFlash('error', 'You do not have permission to access this page');
        redirect('dashboard.php');
    }
}

function login($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['last_activity'] = time();

    session_regenerate_id(true);

    // Log activity if logging is available
    if (function_exists('logActivity')) {
        logActivity($user['id'], 'login', 'User logged in');
    }
}

function logout() {
    if (isLoggedIn()) {
        if (function_exists('logActivity')) {
            logActivity($_SESSION['user_id'], 'logout', 'User logged out');
        }
    }

    $_SESSION = [];

    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }

    session_destroy();
}

function displayFlashMessage() {
    $flash = getFlash();
    if ($flash) {
        $type = htmlspecialchars($flash['type']);
        $message = htmlspecialchars($flash['message']);
        return "<div class='alert alert-{$type} alert-dismissible fade show' role='alert'>
            {$message}
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>";
    }
    return '';
}

/**
 * Show meal detail view
 */
function showMealDetail($mealId) {
    global $meal, $userId;

    try {
        // Get meal with food items - Note: The database schema may not have dnms_meal_items table
        // For now, we'll work with the meals table directly and show meal details
        $stmt = getDNMSDBConnection()->prepare("
            SELECT * FROM dnms_meals WHERE id = ? AND user_id = ?
        ");

        $stmt->bindParam(1, $mealId, PDO::PARAM_INT);
        $stmt->bindParam(2, $userId, PDO::PARAM_INT);
        $stmt->execute();

        $mealData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$mealData) {
            return '<div class="error-message">Meal not found or access denied.</div>';
        }

        return '
        <div class="meal-detail-card">
            <div class="meal-detail-header">
                <div>
                    <h2 class="meal-detail-title">' . htmlspecialchars($mealData['meal_name']) . '</h2>
                    <div class="meal-detail-meta">
                        <span><i class="fas fa-utensils"></i> ' . ucfirst($mealData['meal_type']) . '</span>
                        <span><i class="fas fa-calendar"></i> ' . date('F j, Y \a\t g:i A', strtotime($mealData['meal_date'] . ' ' . $mealData['meal_time'])) . '</span>
                    </div>
                </div>
                <div>
                    <a href="history.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Back to History
                    </a>
                </div>
            </div>

            <div class="meal-detail-nutrition">
                <div class="nutrition-card">
                    <div class="nutrition-value">' . round($mealData['total_calories']) . '</div>
                    <div class="nutrition-label">Total Calories</div>
                </div>
                <div class="nutrition-card">
                    <div class="nutrition-value">' . round($mealData['total_protein']) . 'g</div>
                    <div class="nutrition-label">Protein</div>
                </div>
                <div class="nutrition-card">
                    <div class="nutrition-value">' . round($mealData['total_carbs']) . 'g</div>
                    <div class="nutrition-label">Carbohydrates</div>
                </div>
                <div class="nutrition-card">
                    <div class="nutrition-value">' . round($mealData['total_fat']) . 'g</div>
                    <div class="nutrition-label">Fat</div>
                </div>
            </div>

            ' . ($mealData['notes'] ? '
                <div class="meal-notes">
                    <h3>Notes</h3>
                    <p>' . htmlspecialchars($mealData['notes']) . '</p>
                </div>' : '') . '
        </div>';
    } catch (Exception $e) {
        error_log("Meal Detail Error: " . $e->getMessage());
        return '<div class="error-message">Error loading meal details.</div>';
    }
}

/**
 * Delete a meal
 */
function deleteMeal($mealId) {
    global $userId;

    try {
        // First check if meal belongs to user
        $checkStmt = getDNMSDBConnection()->prepare("SELECT id FROM dnms_meals WHERE id = ? AND user_id = ?");
        $checkStmt->bindParam(1, $mealId, PDO::PARAM_INT);
        $checkStmt->bindParam(2, $userId, PDO::PARAM_INT);
        $checkStmt->execute();

        if (!$checkStmt->fetch()) {
            setFlash('error', 'Meal not found or access denied');
            header('Location: history.php');
            exit();
        }

        // Delete the meal (no need to delete meal items since the table doesn't exist in schema)
        $deleteStmt = getDNMSDBConnection()->prepare("DELETE FROM dnms_meals WHERE id = ?");
        $deleteStmt->bindParam(1, $mealId, PDO::PARAM_INT);
        $deleteStmt->execute();

        setFlash('success', 'Meal deleted successfully');
        header('Location: history.php');
        exit();
    } catch (Exception $e) {
        error_log("Delete Meal Error: " . $e->getMessage());
        setFlash('error', 'Error deleting meal');
        header('Location: history.php');
        exit();
    }
}

/**
 * Show meal history overview
 */
function showMealHistory() {
    global $meal, $userId, $currentUser;

    try {
        // Get recent meals with pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Get meals from dnms_meals table (note: schema doesn't have dnms_meal_items table)
        $stmt = getDNMSDBConnection()->prepare("
            SELECT * FROM dnms_meals WHERE user_id = ? ORDER BY meal_date DESC, meal_time DESC LIMIT ? OFFSET ?
        ");

        $stmt->bindParam(1, $userId, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->bindParam(3, $offset, PDO::PARAM_INT);
        $stmt->execute();

        $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total count for pagination
        $countStmt = getDNMSDBConnection()->prepare("
            SELECT COUNT(*) as total FROM dnms_meals WHERE user_id = ?
        ");
        $countStmt->execute([$userId]);
        $totalMeals = $countStmt->fetch()['total'];
        $totalPages = ceil($totalMeals / $limit);

        // Get daily nutrition summary for the last 7 days
        $dailyStats = getDailyNutritionStats($userId, 7);

        return '
        <div class="meal-history-content">
            <!-- Daily Stats Cards -->
            ' . generateDailyStatsCards($dailyStats) . '

            <!-- Meals Grid -->
            <div class="meals-container">
                <div class="meals-header">
                    <h3>Recent Meals</h3>
                    <div class="meals-actions">
                        <a href="log.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i>
                            Add Meal
                        </a>
                    </div>
                </div>

                ' . (empty($meals) ? '
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h3>No Meals Logged Yet</h3>
                        <p>Start tracking your nutrition by logging your first meal!</p>
                        <a href="log.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Log Your First Meal
                        </a>
                    </div>' : '
                    <div class="meals-grid">
                        ' . generateMealsHtml($meals) . '
                    </div>') . '

                <!-- Pagination -->
                ' . generatePaginationHtml($totalPages, $page) . '
            </div>
        </div>';
    } catch (Exception $e) {
        error_log("Meal History Error: " . $e->getMessage());
        return '<div class="error-message">Error loading meal history. Please try again later.</div>';
    }
}

function generateDailyStatsCards($dailyStats) {
    if (empty($dailyStats)) {
        return '';
    }

    $totalCalories = array_sum(array_column($dailyStats, 'total_calories'));
    $totalProtein = array_sum(array_column($dailyStats, 'total_protein'));
    $totalCarbs = array_sum(array_column($dailyStats, 'total_carbs'));
    $totalFat = array_sum(array_column($dailyStats, 'total_fat'));
    $totalMeals = array_sum(array_column($dailyStats, 'meal_count'));
    $avgCalories = $totalMeals > 0 ? round($totalCalories / $totalMeals) : 0;

    return '
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-fire"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">' . $avgCalories . '</div>
                <div class="stat-label">Avg Daily Calories</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-drumstick-bite"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">' . round($totalProtein / max(count($dailyStats), 1)) . 'g</div>
                <div class="stat-label">Avg Daily Protein</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-bread-slice"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">' . round($totalCarbs / max(count($dailyStats), 1)) . 'g</div>
                <div class="stat-label">Avg Daily Carbs</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">' . $totalMeals . '</div>
                <div class="stat-label">Total Meals (7 days)</div>
            </div>
        </div>
    </div>';
}

function generateMealsHtml($meals) {
    $html = '';
    foreach ($meals as $meal) {
        $html .= '
        <div class="meal-card">
            <div class="meal-header">
                <div>
                    <h2 class="meal-name">' . htmlspecialchars($meal['meal_name']) . '</h2>
                    <div class="meal-meta">
                        <span class="meal-type">' . ucfirst($meal['meal_type']) . '</span>
                        <span class="meal-date">' . date('M j, Y', strtotime($meal['meal_date'])) . '</span>
                    </div>
                </div>
                <div>
                    <a href="history.php?action=view&id=' . $meal['id'] . '" class="btn btn-secondary">
                        <i class="fas fa-eye"></i>
                        View
                    </a>
                    <a href="history.php?action=delete&id=' . $meal['id'] . '" class="btn btn-danger">
                        <i class="fas fa-trash"></i>
                        Delete
                    </a>
                </div>
            </div>

            <div class="meal-nutrition">
                <div class="nutrition-item">
                    <div class="nutrition-label">Calories</div>
                    <div class="nutrition-value">' . round($meal['total_calories']) . '</div>
                </div>
                <div class="nutrition-item">
                    <div class="nutrition-label">Protein</div>
                    <div class="nutrition-value">' . round($meal['total_protein']) . 'g</div>
                </div>
                <div class="nutrition-item">
                    <div class="nutrition-label">Carbs</div>
                    <div class="nutrition-value">' . round($meal['total_carbs']) . 'g</div>
                </div>
                <div class="nutrition-item">
                    <div class="nutrition-label">Fat</div>
                    <div class="nutrition-value">' . round($meal['total_fat']) . 'g</div>
                </div>
            </div>

            ' . ($meal['notes'] ? '
                <div class="meal-notes">
                    <div class="meal-notes-title">Notes:</div>
                    <div class="notes-content">' . htmlspecialchars($meal['notes']) . '</div>
                </div>' : '') . '
        </div>';
    }
    return $html;
}

function generatePaginationHtml($totalPages, $currentPage) {
    if ($totalPages <= 1) {
        return '';
    }

    $html = '<div class="pagination">';

    // Previous button
    if ($currentPage > 1) {
        $html .= '<a href="history.php?page=' . ($currentPage - 1) . '" class="page-btn">Previous</a>';
    }

    // Page numbers
    for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++) {
        $active = $i === $currentPage ? 'active' : '';
        $html .= '<a href="history.php?page=' . $i . '" class="page-btn ' . $active . '">' . $i . '</a>';
    }

    // Next button
    if ($currentPage < $totalPages) {
        $html .= '<a href="history.php?page=' . ($currentPage + 1) . '" class="page-btn">Next</a>';
    }

    $html .= '</div>';
    return $html;
}

// Function to get daily nutrition stats
function getDailyNutritionStats($userId, $days = 7) {
    try {
        $stmt = getDNMSDBConnection()->prepare("
            SELECT DATE(meal_date) as date,
                   SUM(total_calories) as total_calories,
                   SUM(total_protein) as total_protein,
                   SUM(total_carbs) as total_carbs,
                   SUM(total_fat) as total_fat,
                   COUNT(*) as meal_count
            FROM dnms_meals
            WHERE user_id = ? AND meal_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            GROUP BY DATE(meal_date)
            ORDER BY date DESC
        ");

        $stmt->bindParam(1, $userId, PDO::PARAM_INT);
        $stmt->bindParam(2, $days, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Daily Nutrition Stats Error: " . $e->getMessage());
        return [];
    }
}

// Initialize classes
require_once __DIR__ . '/../../classes/Food.php';
require_once __DIR__ . '/../../classes/Meal.php';

$food = new Food();
$meal = new Meal();

// Get user data
$userId = $_SESSION['user_id'];
$currentUser = getCurrentUser();

// Handle different actions
$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'view':
        $mealId = (int)($_GET['id'] ?? 0);
        echo showMealDetail($mealId);
        break;
    case 'delete':
        $mealId = (int)($_GET['id'] ?? 0);
        deleteMeal($mealId);
        break;
    case 'export':
        // Handle export logic here
        header('Location: history.php');
        break;
    default:
        echo showMealHistory();
        break;
}
?>

<?php
// Function to get daily nutrition stats
function getDailyNutritionStats($userId, $days = 7) {
    try {
        $stmt = getDNMSDBConnection()->prepare("
            SELECT DATE(meal_date) as date,
                   SUM(total_calories) as total_calories,
                   SUM(total_protein) as total_protein,
                   SUM(total_carbs) as total_carbs,
                   SUM(total_fat) as total_fat,
                   COUNT(*) as meal_count
            FROM dnms_meals
            WHERE user_id = ? AND meal_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            GROUP BY DATE(meal_date)
            ORDER BY date DESC
        ");

        $stmt->bindParam(1, $userId, PDO::PARAM_INT);
        $stmt->bindParam(2, $days, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Daily Nutrition Stats Error: " . $e->getMessage());
        return [];
    }
}

/**
 * Show meal detail view
 */
function showMealDetail($mealId) {
    global $meal, $userId;

    try {
        // Get meal with food items
        $stmt = getDNMSDBConnection()->prepare("
            SELECT m.*, GROUP_CONCAT(f.name) as food_names,
                   GROUP_CONCAT(f.brand) as food_brands,
                   GROUP_CONCAT(mi.quantity_grams) as quantities,
                   GROUP_CONCAT(f.calories_per_100g) as calories_per_100g,
                   GROUP_CONCAT(f.protein_per_100g) as protein_per_100g,
                   GROUP_CONCAT(f.carbs_per_100g) as carbs_per_100g,
                   GROUP_CONCAT(f.fat_per_100g) as fat_per_100g
            FROM dnms_meals m
            LEFT JOIN dnms_meal_items mi ON m.id = mi.meal_id
            LEFT JOIN dnms_foods f ON mi.food_id = f.id
            WHERE m.id = ? AND m.user_id = ?
            GROUP BY m.id
        ");

        $stmt->bindParam(1, $mealId, PDO::PARAM_INT);
        $stmt->bindParam(2, $userId, PDO::PARAM_INT);
        $stmt->execute();

        $mealData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$mealData) {
            return '<div class="error-message">Meal not found or access denied.</div>';
        }

        return '
        <div class="meal-detail-card">
            <div class="meal-detail-header">
                <div>
                    <h2 class="meal-detail-title">' . htmlspecialchars($mealData['meal_name']) . '</h2>
                    <div class="meal-detail-meta">
                        <span><i class="fas fa-utensils"></i> ' . ucfirst($mealData['meal_type']) . '</span>
                        <span><i class="fas fa-calendar"></i> ' . date('F j, Y \a\t g:i A', strtotime($mealData['meal_date'] . ' ' . $mealData['meal_time'])) . '</span>
                    </div>
                </div>
                <div>
                    <a href="history.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Back to History
                    </a>
                </div>
            </div>

            <div class="meal-detail-nutrition">
                <div class="nutrition-card">
                    <div class="nutrition-value">' . round($mealData['total_calories']) . '</div>
                    <div class="nutrition-label">Total Calories</div>
                </div>
                <div class="nutrition-card">
                    <div class="nutrition-value">' . round($mealData['total_protein']) . 'g</div>
                    <div class="nutrition-label">Protein</div>
                </div>
                <div class="nutrition-card">
                    <div class="nutrition-value">' . round($mealData['total_carbs']) . 'g</div>
                    <div class="nutrition-label">Carbohydrates</div>
                </div>
                <div class="nutrition-card">
                    <div class="nutrition-value">' . round($mealData['total_fat']) . 'g</div>
                    <div class="nutrition-label">Fat</div>
                </div>
            </div>

            ' . ($mealData['food_names'] ? '
                <div class="food-items-section">
                    <h3>Food Items</h3>
                    <div class="food-items-grid">
                        ' . generateFoodItemsHtml($mealData) . '
                    </div>
                </div>' : '') . '
        </div>';
    } catch (Exception $e) {
        error_log("Meal Detail Error: " . $e->getMessage());
        return '<div class="error-message">Error loading meal details.</div>';
    }
}

function generateFoodItemsHtml($mealData) {
    $foodNames = explode(',', $mealData['food_names']);
    $foodBrands = explode(',', $mealData['food_brands']);
    $quantities = explode(',', $mealData['quantities']);
    $caloriesPer100g = explode(',', $mealData['calories_per_100g']);
    $proteinPer100g = explode(',', $mealData['protein_per_100g']);
    $carbsPer100g = explode(',', $mealData['carbs_per_100g']);
    $fatPer100g = explode(',', $mealData['fat_per_100g']);

    $html = '';
    foreach ($foodNames as $index => $foodName) {
        $quantity = isset($quantities[$index]) ? (float)$quantities[$index] : 0;
        $calories = isset($caloriesPer100g[$index]) ? ($caloriesPer100g[$index] * $quantity / 100) : 0;
        $protein = isset($proteinPer100g[$index]) ? ($proteinPer100g[$index] * $quantity / 100) : 0;
        $carbs = isset($carbsPer100g[$index]) ? ($carbsPer100g[$index] * $quantity / 100) : 0;
        $fat = isset($fatPer100g[$index]) ? ($fatPer100g[$index] * $quantity / 100) : 0;

        $html .= '
        <div class="food-item-card">
            <div class="food-item-header">
                <div class="food-item-name">
                    ' . htmlspecialchars($foodName) . '
                    ' . (isset($foodBrands[$index]) && !empty($foodBrands[$index]) ? '<small>by ' . htmlspecialchars($foodBrands[$index]) . '</small>' : '') . '
                </div>
                <div class="food-item-quantity">' . round($quantity) . 'g</div>
            </div>

            <div class="food-item-nutrition">
                <div class="nutrition-mini-item">
                    <div class="nutrition-mini-label">Calories</div>
                    <div class="nutrition-mini-value">' . round($calories) . '</div>
                </div>
                <div class="nutrition-mini-item">
                    <div class="nutrition-mini-label">Protein</div>
                    <div class="nutrition-mini-value">' . round($protein, 1) . 'g</div>
                </div>
                <div class="nutrition-mini-item">
                    <div class="nutrition-mini-label">Carbs</div>
                    <div class="nutrition-mini-value">' . round($carbs, 1) . 'g</div>
                </div>
                <div class="nutrition-mini-item">
                    <div class="nutrition-mini-label">Fat</div>
                    <div class="nutrition-mini-value">' . round($fat, 1) . 'g</div>
                </div>
            </div>
        </div>';
    }
    return $html;
}

/**
 * Delete a meal
 */
function deleteMeal($mealId) {
    global $userId;

    try {
        // First check if meal belongs to user
        $checkStmt = getDNMSDBConnection()->prepare("SELECT id FROM dnms_meals WHERE id = ? AND user_id = ?");
        $checkStmt->bindParam(1, $mealId, PDO::PARAM_INT);
        $checkStmt->bindParam(2, $userId, PDO::PARAM_INT);
        $checkStmt->execute();

        if (!$checkStmt->fetch()) {
            setFlash('error', 'Meal not found or access denied');
            header('Location: history.php');
            exit();
        }

        // Delete meal items first (foreign key constraint)
        $deleteItemsStmt = getDNMSDBConnection()->prepare("DELETE FROM dnms_meal_items WHERE meal_id = ?");
        $deleteItemsStmt->bindParam(1, $mealId, PDO::PARAM_INT);
        $deleteItemsStmt->execute();

        // Delete the meal
        $deleteStmt = getDNMSDBConnection()->prepare("DELETE FROM dnms_meals WHERE id = ?");
        $deleteStmt->bindParam(1, $mealId, PDO::PARAM_INT);
        $deleteStmt->execute();

        setFlash('success', 'Meal deleted successfully');
        header('Location: history.php');
        exit();
    } catch (Exception $e) {
        error_log("Delete Meal Error: " . $e->getMessage());
        setFlash('error', 'Error deleting meal');
        header('Location: history.php');
        exit();
    }
}

/**
 * Show meal history overview
 */
function showMealHistory() {
    global $meal, $userId, $currentUser;

    try {
        // Get recent meals with pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Get meals with food items
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

        $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total count for pagination
        $countStmt = getDNMSDBConnection()->prepare("
            SELECT COUNT(*) as total FROM dnms_meals WHERE user_id = ?
        ");
        $countStmt->execute([$userId]);
        $totalMeals = $countStmt->fetch()['total'];
        $totalPages = ceil($totalMeals / $limit);

        // Get daily nutrition summary for the last 7 days
        $dailyStats = getDailyNutritionStats($userId, 7);

        return '
        <div class="meal-history-content">
            <!-- Daily Stats Cards -->
            ' . generateDailyStatsCards($dailyStats) . '

            <!-- Meals Grid -->
            <div class="meals-container">
                <div class="meals-header">
                    <h3>Recent Meals</h3>
                    <div class="meals-actions">
                        <a href="log.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i>
                            Add Meal
                        </a>
                    </div>
                </div>

                ' . (empty($meals) ? '
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h3>No Meals Logged Yet</h3>
                        <p>Start tracking your nutrition by logging your first meal!</p>
                        <a href="log.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Log Your First Meal
                        </a>
                    </div>' : '
                    <div class="meals-grid">
                        ' . generateMealsHtml($meals) . '
                    </div>') . '

                <!-- Pagination -->
                ' . generatePaginationHtml($totalPages, $page) . '
            </div>
        </div>';
    } catch (Exception $e) {
        error_log("Meal History Error: " . $e->getMessage());
        return '<div class="error-message">Error loading meal history. Please try again later.</div>';
    }
}

function generateDailyStatsCards($dailyStats) {
    if (empty($dailyStats)) {
        return '';
    }

    $totalCalories = array_sum(array_column($dailyStats, 'total_calories'));
    $totalProtein = array_sum(array_column($dailyStats, 'total_protein'));
    $totalCarbs = array_sum(array_column($dailyStats, 'total_carbs'));
    $totalFat = array_sum(array_column($dailyStats, 'total_fat'));
    $totalMeals = array_sum(array_column($dailyStats, 'meal_count'));
    $avgCalories = $totalMeals > 0 ? round($totalCalories / $totalMeals) : 0;

    return '
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-fire"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">' . $avgCalories . '</div>
                <div class="stat-label">Avg Daily Calories</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-drumstick-bite"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">' . round($totalProtein / max(count($dailyStats), 1)) . 'g</div>
                <div class="stat-label">Avg Daily Protein</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-bread-slice"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">' . round($totalCarbs / max(count($dailyStats), 1)) . 'g</div>
                <div class="stat-label">Avg Daily Carbs</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">' . $totalMeals . '</div>
                <div class="stat-label">Total Meals (7 days)</div>
            </div>
        </div>
    </div>';
}

function generateMealsHtml($meals) {
    $html = '';
    foreach ($meals as $meal) {
        $html .= '
        <div class="meal-card">
            <div class="meal-header">
                <div>
                    <h2 class="meal-name">' . htmlspecialchars($meal['meal_name']) . '</h2>
                    <div class="meal-meta">
                        <span class="meal-type">' . ucfirst($meal['meal_type']) . '</span>
                        <span class="meal-date">' . date('M j, Y', strtotime($meal['meal_date'])) . '</span>
                    </div>
                </div>
                <div>
                    <a href="history.php?action=view&id=' . $meal['id'] . '" class="btn btn-secondary">
                        <i class="fas fa-eye"></i>
                        View
                    </a>
                    <a href="history.php?action=delete&id=' . $meal['id'] . '" class="btn btn-danger">
                        <i class="fas fa-trash"></i>
                        Delete
                    </a>
                </div>
            </div>

            <div class="meal-nutrition">
                <div class="nutrition-item">
                    <div class="nutrition-label">Calories</div>
                    <div class="nutrition-value">' . round($meal['total_calories']) . '</div>
                </div>
                <div class="nutrition-item">
                    <div class="nutrition-label">Protein</div>
                    <div class="nutrition-value">' . round($meal['total_protein']) . 'g</div>
                </div>
                <div class="nutrition-item">
                    <div class="nutrition-label">Carbs</div>
                    <div class="nutrition-value">' . round($meal['total_carbs']) . 'g</div>
                </div>
                <div class="nutrition-item">
                    <div class="nutrition-label">Fat</div>
                    <div class="nutrition-value">' . round($meal['total_fat']) . 'g</div>
                </div>
            </div>

            ' . ($meal['food_names'] ? '
                <div class="meal-foods">
                    <div class="meal-foods-title">Foods:</div>
                    <div class="food-list">
                        ' . generateFoodTags($meal['food_names']) . '
                    </div>
                </div>' : '') . '
        </div>';
    }
    return $html;
}

function generateFoodTags($foodNames) {
    $foods = explode(',', $foodNames);
    $html = '';
    foreach ($foods as $food) {
        $html .= '<span class="food-tag">' . htmlspecialchars(trim($food)) . '</span>';
    }
    return $html;
}

function generatePaginationHtml($totalPages, $currentPage) {
    if ($totalPages <= 1) {
        return '';
    }

    $html = '<div class="pagination">';

    // Previous button
    if ($currentPage > 1) {
        $html .= '<a href="history.php?page=' . ($currentPage - 1) . '" class="page-btn">Previous</a>';
    }

    // Page numbers
    for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++) {
        $active = $i === $currentPage ? 'active' : '';
        $html .= '<a href="history.php?page=' . $i . '" class="page-btn ' . $active . '">' . $i . '</a>';
    }

    // Next button
    if ($currentPage < $totalPages) {
        $html .= '<a href="history.php?page=' . ($currentPage + 1) . '" class="page-btn">Next</a>';
    }

    $html .= '</div>';
    return $html;
}


function generateDailyStatsCards($dailyStats) {

/**
 * Delete a meal
 */
function deleteMeal($mealId) {
    global $userId;

    try {
        // First check if meal belongs to user
        $checkStmt = getDNMSDBConnection()->prepare("SELECT id FROM dnms_meals WHERE id = ? AND user_id = ?");
        $checkStmt->bindParam(1, $mealId, PDO::PARAM_INT);
        $checkStmt->bindParam(2, $userId, PDO::PARAM_INT);
        $checkStmt->execute();

        if (!$checkStmt->fetch()) {
            showError('Meal not found or access denied');
            return;
        }

        // Delete meal items first (foreign key constraint)
        $deleteItemsStmt = getDNMSDBConnection()->prepare("DELETE FROM dnms_meal_items WHERE meal_id = ?");
        $deleteItemsStmt->bindParam(1, $mealId, PDO::PARAM_INT);
        $deleteItemsStmt->execute();

        // Delete the meal
        $deleteStmt = getDNMSDBConnection()->prepare("DELETE FROM dnms_meals WHERE id = ?");
        $deleteStmt->bindParam(1, $mealId, PDO::PARAM_INT);
        $deleteStmt->execute();

        header('Location: history.php');
        exit();
    } catch (Exception $e) {
        error_log("Delete Meal Error: " . $e->getMessage());
        echo showError('Error deleting meal');
    }
}

function showError($message) {

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error - NutriTrack Pro</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: 'Inter', sans-serif;
                background: #f8fafc;
                color: #1e293b;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                margin: 0;
            }
            .error-container {
                text-align: center;
                padding: 3rem;
                background: white;
                border-radius: 20px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
                max-width: 500px;
            }
            .error-icon {
                width: 80px;
                height: 80px;
                background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 2rem;
                font-size: 2rem;
                color: white;
            }
            .error-title {
                font-size: 2rem;
                font-weight: 700;
                margin: 0;
            }
            .error-message {
                color: #64748b;
                margin-bottom: 2rem;
                line-height: 1.6;
            }
            .btn {
                padding: 0.75rem 1.5rem;
                border: none;
                border-radius: 12px;
                font-weight: 600;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                font-size: 1rem;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            .btn-primary {
                background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
                color: white;
            }
            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h1 class="error-title">Oops! Something went wrong</h1>
            <p class="error-message"><?php echo htmlspecialchars($message); ?></p>
            <a href="history.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i>
                Back to History
            </a>
        </div>
    </body>
    </html>
    <?php
    exit();
}

function showError($message) {
    return '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error - NutriTrack Pro</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: \'Inter\', sans-serif;
                background: #f8fafc;
                color: #1e293b;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                margin: 0;
            }
            .error-container {
                text-align: center;
                padding: 3rem;
                background: white;
                border-radius: 20px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
                max-width: 500px;
            }
            .error-icon {
                width: 80px;
                height: 80px;
                background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 2rem;
                font-size: 2rem;
                color: white;
            }
            .error-title {
                font-size: 2rem;
                font-weight: 700;
                margin: 0;
            }
            .error-message {
                color: #64748b;
                margin-bottom: 2rem;
                line-height: 1.6;
            }
            .btn {
                padding: 0.75rem 1.5rem;
                border: none;
                border-radius: 12px;
                font-weight: 600;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                font-size: 1rem;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            .btn-primary {
                background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
                color: white;
            }
            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h1 class="error-title">Oops! Something went wrong</h1>
            <p class="error-message">' . htmlspecialchars($message) . '</p>
            <a href="history.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i>
                Back to History
            </a>
        </div>
    </body>
    </html>';
}

// Initialize classes
require_once __DIR__ . '/../../classes/Food.php';
require_once __DIR__ . '/../../classes/Meal.php';

$food = new Food();
$meal = new Meal();

// Get user data
$userId = $_SESSION['user_id'];
$currentUser = getCurrentUser();

// Handle different actions
$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'view':
        $mealId = (int)($_GET['id'] ?? 0);
        showMealDetail($mealId);
        break;
    case 'delete':
        $mealId = (int)($_GET['id'] ?? 0);
        deleteMeal($mealId);
        break;
    case 'export':
        // Handle export logic here
        header('Location: history.php');
        break;
    default:
        showMealHistory();
        break;
}
?>

/**
 * Show meal history overview
 */
function showMealHistory() {
    global $meal, $userId, $currentUser;

    try {
        // Get recent meals with pagination
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Get meals with food items
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

        $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total count for pagination
        $countStmt = getDNMSDBConnection()->prepare("
            SELECT COUNT(*) as total FROM dnms_meals WHERE user_id = ?
        ");
        $countStmt->execute([$userId]);
        $totalMeals = $countStmt->fetch()['total'];
        $totalPages = ceil($totalMeals / $limit);

        // Get daily nutrition summary for the last 7 days
        $dailyStats = getDailyNutritionStats($userId, 7);

    } catch (Exception $e) {
        error_log("Meal History Error: " . $e->getMessage());
        $meals = [];
        $dailyStats = [];
    }

    $pageTitle = 'Meal History';
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
                /* Light Theme */
                --navbar-bg: rgba(255, 255, 255, 0.95);
                --sidebar-bg: rgba(248, 250, 252, 0.95);
                --glass-gradient: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.1) 100%);
                --bg-card: rgba(255, 255, 255, 0.8);
                --bg-secondary: #f8fafc;
                --bg-tertiary: #f1f5f9;
                --text-primary: #1e293b;
                --text-secondary: #64748b;
                --border-color: #e2e8f0;
                --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);

                /* Enhanced Gradients */
                --nutrition-gradient: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
                --primary-gradient: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
                --rainbow-gradient: linear-gradient(135deg, #ff6b6b 0%, #ffa500 25%, #ffff00 50%, #32cd32 75%, #1e90ff 100%);
                --accent-color: #10b981;
                --warning-color: #f59e0b;
                --danger-color: #ef4444;
                --success-color: #10b981;
            }

            /* Dark Theme */
            [data-theme="dark"] {
                --navbar-bg: rgba(15, 23, 42, 0.95);
                --sidebar-bg: rgba(30, 41, 59, 0.95);
                --glass-gradient: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.1) 100%);
                --bg-card: rgba(30, 41, 59, 0.8);
                --bg-secondary: #1e293b;
                --bg-tertiary: #334155;
                --text-primary: #f1f5f9;
                --text-secondary: #cbd5e1;
                --border-color: #334155;
                --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
                --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4), 0 2px 4px -1px rgba(0, 0, 0, 0.3);
                --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.4), 0 4px 6px -2px rgba(0, 0, 0, 0.3);
                --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.4), 0 10px 10px -5px rgba(0, 0, 0, 0.2);

                --nutrition-gradient: linear-gradient(135deg, #64b5f6 0%, #42a5f5 100%);
                --primary-gradient: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
                --accent-color: #4ade80;
                --warning-color: #fbbf24;
                --danger-color: #f87171;
                --success-color: #4ade80;
            }

            body {
                font-family: 'Inter', sans-serif;
                background: var(--bg-secondary);
                color: var(--text-primary);
                line-height: 1.6;
                margin: 0;
                padding: 0;
                transition: background-color 0.3s ease, color 0.3s ease;
            }

            body::before {
                content: '';
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: radial-gradient(circle at 20% 50%, rgba(120, 119, 198, 0.05) 0%, transparent 50%),
                            radial-gradient(circle at 80% 20%, rgba(255, 177, 153, 0.05) 0%, transparent 50%),
                            radial-gradient(circle at 40% 80%, rgba(42, 252, 152, 0.05) 0%, transparent 50%);
                pointer-events: none;
                z-index: -1;
                transition: opacity 0.3s ease;
            }

            [data-theme="dark"] body::before {
                opacity: 0.8;
            }

            /* Enhanced Top Header */
            .top-header {
                background: linear-gradient(135deg, var(--navbar-bg) 0%, rgba(255, 255, 255, 0.9) 100%);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
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
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
            }

            [data-theme="dark"] .top-header {
                background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.9) 100%);
            }

            .header-left {
                display: flex;
                align-items: center;
                gap: 1.5rem;
            }

            .menu-toggle {
                background: linear-gradient(135deg, var(--bg-secondary) 0%, rgba(255, 255, 255, 0.1) 100%);
                border: 2px solid var(--border-color);
                border-radius: 12px;
                padding: 0.75rem;
                color: var(--text-primary);
                cursor: pointer;
                transition: all 0.3s ease;
                font-size: 1.2rem;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 45px;
                height: 45px;
                backdrop-filter: blur(10px);
            }

            .menu-toggle:hover {
                background: var(--nutrition-gradient);
                border-color: transparent;
                color: white;
                transform: scale(1.05) rotate(90deg);
                box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
            }

            .brand {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 1.5rem;
            }

            .btn {
                padding: 0.75rem 1.5rem;
                border: none;
                border-radius: 12px;
                font-weight: 600;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                font-size: 1rem;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .btn-primary {
                background: var(--nutrition-gradient);
                color: white;
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: var(--shadow-md);
            }

            .btn-secondary {
                background: var(--bg-tertiary);
                color: var(--text-primary);
                border: 1px solid var(--border-color);
            }

            .btn-secondary:hover {
                background: var(--border-color);
            }

            .user-name {
                color: var(--text-secondary);
                font-weight: 500;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.5rem 1rem;
                background: linear-gradient(135deg, var(--bg-secondary) 0%, rgba(255, 255, 255, 0.05) 100%);
                border-radius: 8px;
                border: 1px solid var(--border-color);
                backdrop-filter: blur(10px);
                transition: all 0.3s ease;
            }

            .user-name:hover {
                background: var(--nutrition-gradient);
                color: white;
                border-color: transparent;
            }

            /* Theme Toggle */
            .theme-toggle {
                background: linear-gradient(135deg, var(--bg-card) 0%, rgba(255, 255, 255, 0.1) 100%);
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
                box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
            }

            [data-theme="dark"] .toggle-ball {
                transform: translateX(24px);
            }

            /* Enhanced Sidebar */
            .sidebar {
                width: 280px;
                background: var(--sidebar-bg);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                padding: 5rem 1.5rem 2rem;
                border-right: 1px solid var(--border-color);
                transform: translateX(0);
                transition: transform 0.3s ease;
                z-index: 999;
                overflow-y: auto;
                scrollbar-width: thin;
                scrollbar-color: var(--border-color) transparent;
            }

            .sidebar::-webkit-scrollbar {
                width: 6px;
            }

            .sidebar::-webkit-scrollbar-track {
                background: transparent;
            }

            .sidebar::-webkit-scrollbar-thumb {
                background: var(--border-color);
                border-radius: 3px;
            }

            .sidebar::-webkit-scrollbar-thumb:hover {
                background: var(--text-secondary);
            }

            .sidebar-nav {
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
            }

            .nav-item {
                display: flex;
                align-items: center;
                gap: 1rem;
                padding: 1rem 1.25rem;
                color: var(--text-secondary);
                text-decoration: none;
                border-radius: 12px;
                font-weight: 500;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }

            .nav-item::before {
                content: '';
                position: absolute;
                left: 0;
                top: 0;
                bottom: 0;
                width: 0;
                background: var(--nutrition-gradient);
                transition: width 0.3s ease;
                z-index: -1;
            }

            .nav-item:hover::before,
            .nav-item.active::before {
                width: 4px;
            }

            .nav-item:hover {
                background: linear-gradient(135deg, rgba(37, 99, 235, 0.1) 0%, rgba(52, 211, 153, 0.05) 100%);
                color: var(--text-primary);
                transform: translateX(4px);
            }

            .nav-item.active {
                background: var(--nutrition-gradient);
                color: white;
                box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            }

            .nav-item i {
                font-size: 1.25rem;
                width: 20px;
                text-align: center;
            }

            /* Main Content */
            .main-content {
                margin-left: 280px;
                min-height: 100vh;
                padding: 5rem 2rem 2rem;
                transition: margin-left 0.3s ease;
            }

            /* Legacy styles for meal history content */
            .container {
                max-width: 1400px;
                margin: 0 auto;
                padding: 2rem 1rem;
            }

            /* Header with Theme Toggle */
            .page-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 3rem;
                position: relative;
            }

            .theme-toggle {
                background: var(--bg-primary);
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
            }

            [data-theme="dark"] .toggle-ball {
                transform: translateX(24px);
            }

            .page-title {
                font-size: 3rem;
                font-weight: 800;
                background: var(--nutrition-gradient);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                margin: 0;
            }

            /* Stats Cards */
            .stats-container {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 1.5rem;
                margin-bottom: 3rem;
            }

            .stat-card {
                background: var(--bg-primary);
                padding: 2rem;
                border-radius: 20px;
                box-shadow: var(--shadow-md);
                border: 1px solid var(--border-color);
                text-align: center;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                position: relative;
                overflow: hidden;
            }

            .stat-card:hover {
                transform: translateY(-5px);
                box-shadow: var(--shadow-lg);
            }

            .stat-icon {
                width: 60px;
                height: 60px;
                background: var(--nutrition-gradient);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 1rem;
                font-size: 1.5rem;
                color: white;
            }

            .stat-value {
                font-size: 2.5rem;
                font-weight: 700;
                color: var(--text-primary);
                margin-bottom: 0.5rem;
            }

            .stat-label {
                font-size: 1.125rem;
                color: var(--text-secondary);
                font-weight: 500;
            }

            /* Meals Grid */
            .meals-container {
                background: var(--bg-primary);
                border-radius: 20px;
                padding: 2rem;
                box-shadow: var(--shadow-md);
                border: 1px solid var(--border-color);
            }

            .meals-actions {
                display: flex;
                gap: 1rem;
            }

            .add-meal-btn, .export-btn {
                padding: 0.75rem 1.5rem;
                border: none;
                border-radius: 12px;
                font-weight: 600;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                font-size: 0.875rem;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .add-meal-btn {
                background: var(--nutrition-gradient);
                color: white;
            }

            .add-meal-btn:hover {
                transform: translateY(-2px);
                box-shadow: var(--shadow-md);
            }

            .export-btn {
                background: var(--bg-tertiary);
                color: var(--text-primary);
                border: 1px solid var(--border-color);
            }

            .export-btn:hover {
                background: var(--border-color);
                transform: translateY(-1px);
            }

            .meals-grid {
                display: grid;
                gap: 1.5rem;
            }

            .meal-card {
                background: var(--bg-secondary);
                border-radius: 16px;
                padding: 1.5rem;
                border: 1px solid var(--border-color);
                transition: all 0.3s ease;
                position: relative;
            }

            .meal-card:hover {
                transform: translateY(-2px);
                box-shadow: var(--shadow-md);
                border-color: var(--nutrition-gradient);
            }

            .meal-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 1rem;
            }

            .meal-name {
                font-size: 1.25rem;
                font-weight: 600;
                color: var(--text-primary);
                margin: 0;
            }

            .meal-meta {
                display: flex;
                gap: 1rem;
                font-size: 0.875rem;
                color: var(--text-secondary);
            }

            .meal-type {
                background: var(--bg-tertiary);
                padding: 0.25rem 0.75rem;
                border-radius: 12px;
                font-weight: 500;
            }

            .meal-date {
                color: var(--text-secondary);
            }

            .meal-nutrition {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
                gap: 1rem;
                margin-bottom: 1rem;
            }

            .nutrition-item {
                text-align: center;
                padding: 0.75rem;
                background: var(--bg-primary);
                border-radius: 8px;
                border: 1px solid var(--border-color);
            }

            .nutrition-label {
                font-size: 0.75rem;
                color: var(--text-secondary);
                margin-bottom: 0.25rem;
                text-transform: uppercase;
                font-weight: 600;
            }

            .nutrition-value {
                font-size: 1rem;
                font-weight: 700;
                color: var(--text-primary);
            }

            .meal-foods {
                margin-top: 1rem;
                padding-top: 1rem;
                border-top: 1px solid var(--border-color);
            }

            .meal-foods-title {
                font-size: 0.875rem;
                font-weight: 600;
                color: var(--text-secondary);
                margin-bottom: 0.5rem;
                text-transform: uppercase;
            }

            .food-list {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .food-tag {
                background: var(--bg-tertiary);
                color: var(--text-secondary);
                padding: 0.25rem 0.5rem;
                border-radius: 8px;
                font-size: 0.75rem;
                border: 1px solid var(--border-color);
            }

            .meal-actions {
                display: flex;
                gap: 0.5rem;
                justify-content: flex-end;
                margin-top: 1rem;
            }

            .btn {
                padding: 0.5rem 1rem;
                border: none;
                border-radius: 8px;
                font-weight: 500;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 0.25rem;
                font-size: 0.875rem;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .btn-primary {
                background: var(--nutrition-gradient);
                color: white;
            }

            .btn-primary:hover {
                background: var(--primary-gradient);
            }

            .btn-secondary {
                background: var(--bg-tertiary);
                color: var(--text-primary);
                border: 1px solid var(--border-color);
            }

            .btn-secondary:hover {
                background: var(--border-color);
            }

            .btn-danger {
                background: var(--danger-color);
                color: white;
            }

            .btn-danger:hover {
                background: #dc2626;
            }

            /* Pagination */
            .pagination {
                display: flex;
                justify-content: center;
                gap: 0.5rem;
                margin-top: 2rem;
            }

            .page-btn {
                padding: 0.5rem 1rem;
                background: var(--bg-tertiary);
                color: var(--text-primary);
                border: 1px solid var(--border-color);
                border-radius: 8px;
                text-decoration: none;
                transition: all 0.3s ease;
            }

            .page-btn:hover, .page-btn.active {
                background: var(--nutrition-gradient);
                color: white;
                border-color: var(--nutrition-gradient);
            }

            /* Empty State */
            .empty-state {
                text-align: center;
                padding: 4rem 2rem;
                background: var(--bg-primary);
                border-radius: 20px;
                box-shadow: var(--shadow-md);
                border: 1px solid var(--border-color);
            }

            .empty-icon {
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

            .empty-title {
                font-size: 1.5rem;
                font-weight: 600;
                color: var(--text-primary);
                margin-bottom: 0.5rem;
            }

            .empty-description {
                color: var(--text-secondary);
                margin-bottom: 2rem;
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

            .stat-card, .meal-card, .meals-container {
                animation: fadeInUp 0.6s ease-out;
            }

            .stat-card:nth-child(1) { animation-delay: 0.1s; }
            .stat-card:nth-child(2) { animation-delay: 0.2s; }
            .stat-card:nth-child(3) { animation-delay: 0.3s; }
            .stat-card:nth-child(4) { animation-delay: 0.4s; }

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

                .week-view {
                    grid-template-columns: 1fr;
                    gap: 1.5rem;
                }

                .day-card {
                    padding: 2rem;
                }

                .date-nav {
                    padding: 1rem;
                    flex-direction: column;
                    gap: 1rem;
                }

                .current-date {
                    min-width: auto;
                    font-size: 1.25rem;
                }
            }

            @media (max-width: 768px) {
                .container {
                    padding: 1rem;
                }

                .page-header {
                    flex-direction: column;
                    gap: 1rem;
                    align-items: stretch;
                }

                .theme-toggle {
                    align-self: flex-end;
                }

                .page-title {
                    font-size: 2.5rem;
                    text-align: center;
                }

                .stats-container {
                    grid-template-columns: repeat(2, 1fr);
                }

                .meals-header {
                    flex-direction: column;
                    gap: 1rem;
                    align-items: stretch;
                }

                .meals-actions {
                    justify-content: center;
                }
            }

            @media (max-width: 480px) {
                .stats-container {
                    grid-template-columns: 1fr;
                }

                .meal-nutrition {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body>
        <div class="meal-history-content">
            <!-- Daily Stats Cards -->
            ' . generateDailyStatsCards($dailyStats) . '

            <!-- Meals Grid -->
            <div class="meals-container">
                <div class="meals-header">
                    <h3>Recent Meals</h3>
                    <div class="meals-actions">
                        <a href="log.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i>
                            Add Meal
                        </a>
                    </div>
                </div>

                ' . (empty($meals) ? '
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h3>No Meals Logged Yet</h3>
                        <p>Start tracking your nutrition by logging your first meal!</p>
                        <a href="log.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Log Your First Meal
                        </a>
                    </div>' : '
                    <div class="meals-grid">
                        ' . generateMealsHtml($meals) . '
                    </div>') . '

                <!-- Pagination -->
                ' . generatePaginationHtml($totalPages, $page) . '
            </div>
        </div>
    </body>
    </html>';
}

// Initialize classes
require_once __DIR__ . '/../../classes/Food.php';
require_once __DIR__ . '/../../classes/Meal.php';

$food = new Food();
$meal = new Meal();

// Get user data
$userId = $_SESSION['user_id'];
$currentUser = getCurrentUser();

// Handle different actions
$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'view':
        $mealId = (int)($_GET['id'] ?? 0);
        showMealDetail($mealId);
        break;
    case 'delete':
        $mealId = (int)($_GET['id'] ?? 0);
        deleteMeal($mealId);
        break;
    case 'export':
        // Handle export logic here
        header('Location: history.php');
        break;
    default:
        showMealHistory();
        break;
}
?>
