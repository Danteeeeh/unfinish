<?php
/**
 * DNMS Meal Planner Controller
 * Smart meal planning and AI-powered recommendations
 */

// Load main config for authentication functions
try {
    require_once __DIR__ . '/../../../config.php';
} catch (Exception $e) {
    // If main config fails, redirect to setup
    header('Location: ../../../../database_setup.php');
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../../login.php');
    exit();
}

// Verify session is still valid
if (!function_exists('isLoggedIn') || !isLoggedIn()) {
    header('Location: ../../../../login.php');
    exit();
}

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/environment.php';
require_once __DIR__ . '/../../includes/auth_functions.php';
require_once __DIR__ . '/../../classes/User.php';
require_once __DIR__ . '/../../classes/Food.php';
require_once __DIR__ . '/../../classes/Meal.php';
require_once __DIR__ . '/../../classes/Nutrition.php';

class MealPlannerController {
    private $db;
    private $user;
    private $food;
    private $meal;
    private $nutrition;

    public function __construct() {
        try {
            $this->db = getDNMSDBConnection();
            $this->user = new User();
            $this->food = new Food();
            $this->meal = new Meal();
            $this->nutrition = new Nutrition();

            // Test database connection
            $this->testDatabaseSetup();
        } catch (Exception $e) {
            error_log("DNMS Meal Planner Controller Error: " . $e->getMessage());
            $this->showError("System initialization failed: " . $e->getMessage());
        }
    }

    public function index() {
        try {
            $user = getCurrentUser();
            $userId = $user['id'];

            // Get user preferences and goals
            $preferences = $this->getUserPreferences($userId);
            $goals = $this->getUserGoals($userId);

            // Generate meal plan
            $mealPlan = $this->generateMealPlan($userId, $preferences, $goals);

            // Get available foods for customization
            $availableFoods = $this->getAvailableFoods($preferences);

            // Get meal suggestions
            $suggestions = $this->getMealSuggestions($userId, $preferences);

            include __DIR__ . '/../../views/meal/planner.php';
        } catch (Exception $e) {
            error_log("DNMS Meal Planner Error: " . $e->getMessage());
            $this->showError("Failed to load meal planner: " . $e->getMessage());
        }
    }

    public function generate() {
        try {
            $user = getCurrentUser();
            $userId = $user['id'];

            $preferences = $this->getUserPreferences($userId);
            $goals = $this->getUserGoals($userId);

            $mealPlan = $this->generateMealPlan($userId, $preferences, $goals);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'meal_plan' => $mealPlan
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit();
    }

    private function getUserPreferences($userId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM user_preferences WHERE user_id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?? [];
        } catch (Exception $e) {
            return [];
        }
    }

    private function getUserGoals($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM dnms_nutrition_goals
                WHERE user_id = ? AND status = 'active'
                ORDER BY created_date DESC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    private function generateMealPlan($userId, $preferences, $goals) {
        // Calculate daily calorie needs based on goals
        $dailyCalories = $this->calculateDailyCalories($userId, $goals);

        // Get user's dietary restrictions
        $restrictions = $this->getDietaryRestrictions($preferences);

        // Generate meal plan for 7 days
        $mealPlan = [];
        for ($day = 0; $day < 7; $day++) {
            $date = date('Y-m-d', strtotime("+$day days"));
            $mealPlan[$date] = $this->generateDailyMeals($userId, $dailyCalories, $restrictions, $preferences);
        }

        return $mealPlan;
    }

    private function calculateDailyCalories($userId, $goals) {
        // Base calculation - in a real system, this would be more sophisticated
        $baseCalories = 2000; // Default BMR

        foreach ($goals as $goal) {
            if ($goal['goal_type'] === 'weight_loss') {
                $baseCalories -= 500; // Deficit for weight loss
            } elseif ($goal['goal_type'] === 'weight_gain') {
                $baseCalories += 300; // Surplus for weight gain
            }
        }

        return max($baseCalories, 1200); // Minimum healthy calories
    }

    private function getDietaryRestrictions($preferences) {
        $restrictions = [];

        if (!empty($preferences['vegetarian'])) {
            $restrictions[] = 'vegetarian';
        }
        if (!empty($preferences['vegan'])) {
            $restrictions[] = 'vegan';
        }
        if (!empty($preferences['gluten_free'])) {
            $restrictions[] = 'gluten_free';
        }
        if (!empty($preferences['dairy_free'])) {
            $restrictions[] = 'dairy_free';
        }
        if (!empty($preferences['nut_free'])) {
            $restrictions[] = 'nut_free';
        }

        return $restrictions;
    }

    private function generateDailyMeals($userId, $dailyCalories, $restrictions, $preferences) {
        // Distribute calories across meals (40% breakfast, 30% lunch, 20% dinner, 10% snacks)
        $mealDistribution = [
            'breakfast' => $dailyCalories * 0.4,
            'lunch' => $dailyCalories * 0.3,
            'dinner' => $dailyCalories * 0.2,
            'snacks' => $dailyCalories * 0.1
        ];

        $dailyMeals = [];

        foreach ($mealDistribution as $mealType => $calories) {
            $foods = $this->selectFoodsForMeal($calories, $restrictions, $preferences, $mealType);
            $dailyMeals[$mealType] = [
                'target_calories' => round($calories),
                'foods' => $foods,
                'total_nutrition' => $this->calculateTotalNutrition($foods)
            ];
        }

        return $dailyMeals;
    }

    private function selectFoodsForMeal($targetCalories, $restrictions, $preferences, $mealType) {
        $foods = [];
        $currentCalories = 0;

        // Get suitable foods based on meal type and restrictions
        $availableFoods = $this->getFoodsByMealType($mealType, $restrictions);

        shuffle($availableFoods); // Randomize for variety

        foreach ($availableFoods as $food) {
            if ($currentCalories + $food['calories_per_serving'] <= $targetCalories * 1.1) {
                // Calculate serving size to fit calorie target
                $servingMultiplier = min(1, ($targetCalories - $currentCalories) / $food['calories_per_serving']);

                if ($servingMultiplier >= 0.5) { // At least half serving
                    $foods[] = [
                        'food' => $food,
                        'serving_size' => round($servingMultiplier * $food['serving_size'], 1),
                        'calories' => round($servingMultiplier * $food['calories_per_serving'])
                    ];

                    $currentCalories += $foods[count($foods) - 1]['calories'];

                    if ($currentCalories >= $targetCalories * 0.9) {
                        break; // Close enough to target
                    }
                }
            }
        }

        return $foods;
    }

    private function getFoodsByMealType($mealType, $restrictions) {
        try {
            $sql = "SELECT * FROM dnms_foods WHERE 1=1";
            $params = [];

            // Filter by meal type preferences (simplified logic)
            switch ($mealType) {
                case 'breakfast':
                    $sql .= " AND food_category IN ('Grains', 'Fruits', 'Dairy', 'Proteins')";
                    break;
                case 'lunch':
                case 'dinner':
                    $sql .= " AND food_category IN ('Proteins', 'Vegetables', 'Grains', 'Legumes')";
                    break;
                case 'snacks':
                    $sql .= " AND food_category IN ('Fruits', 'Nuts', 'Dairy')";
                    break;
            }

            // Apply dietary restrictions (simplified)
            if (in_array('vegetarian', $restrictions)) {
                $sql .= " AND food_category NOT IN ('Meat', 'Poultry', 'Seafood')";
            }
            if (in_array('vegan', $restrictions)) {
                $sql .= " AND food_category NOT IN ('Meat', 'Poultry', 'Seafood', 'Dairy', 'Eggs')";
            }

            $sql .= " ORDER BY RAND() LIMIT 20";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    private function calculateTotalNutrition($foods) {
        $total = [
            'calories' => 0,
            'protein' => 0,
            'carbs' => 0,
            'fat' => 0,
            'fiber' => 0
        ];

        foreach ($foods as $foodItem) {
            $food = $foodItem['food'];
            $multiplier = $foodItem['serving_size'] / $food['serving_size'];

            $total['calories'] += $foodItem['calories'];
            $total['protein'] += $food['protein_per_serving'] * $multiplier;
            $total['carbs'] += $food['carbs_per_serving'] * $multiplier;
            $total['fat'] += $food['fat_per_serving'] * $multiplier;
            $total['fiber'] += $food['fiber_per_serving'] * $multiplier;
        }

        return $total;
    }

    private function getAvailableFoods($preferences) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM dnms_foods
                ORDER BY food_name
                LIMIT 100
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    private function getMealSuggestions($userId, $preferences) {
        try {
            $stmt = $this->db->prepare("
                SELECT DISTINCT m.meal_name, AVG(m.total_calories) as avg_calories
                FROM dnms_meals m
                WHERE m.user_id = ?
                GROUP BY m.meal_name
                ORDER BY avg_calories DESC
                LIMIT 10
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function saveMealPlan() {
        try {
            $user = getCurrentUser();
            $userId = $user['id'];

            $mealPlanData = json_decode(file_get_contents('php://input'), true);

            // Save meal plan to database (simplified implementation)
            $stmt = $this->db->prepare("
                INSERT INTO dnms_meal_plans (user_id, plan_date, meal_data, created_date)
                VALUES (?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE meal_data = VALUES(meal_data)
            ");

            foreach ($mealPlanData as $date => $meals) {
                $stmt->execute([$userId, $date, json_encode($meals)]);
            }

            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit();
    }

    private function testDatabaseSetup() {
        $tableCheck = checkDNMSTablesExist();
        if ($tableCheck['status'] !== 'complete') {
            $missingTables = implode(', ', $tableCheck['missing_tables']);
            throw new Exception("DNMS database setup incomplete. Missing tables: {$missingTables}. Please run the DNMS database setup script.");
        }
    }

    private function showError($message) {
        ?>
        <!DOCTYPE html>
        <html lang="en" data-theme="light">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Meal Planner Error - NutriTrack Pro</title>
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
                <h1>🍽️ Meal Planner Error</h1>
                <p><?php echo htmlspecialchars($message); ?></p>
                <a href="../../index.php" class="btn">Back to Dashboard</a>
            </div>
        </body>
        </html>
        <?php
        exit();
    }
}

// Handle different actions
$action = $_GET['action'] ?? 'index';

$controller = new MealPlannerController();

switch ($action) {
    case 'generate':
        $controller->generate();
        break;
    case 'save':
        $controller->saveMealPlan();
        break;
    default:
        $controller->index();
        break;
}
?>
