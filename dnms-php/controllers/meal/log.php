<?php
// Load main config for authentication functions
try {
    require_once __DIR__ . '/../../../config.php';
} catch (Exception $e) {
    header('Location: ../../../../database_setup.php');
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../../login.php');
    exit();
}

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/environment.php';
require_once __DIR__ . '/../../classes/Food.php';
require_once __DIR__ . '/../../classes/Meal.php';

class MealLogController {
    private $food;
    private $meal;

    public function __construct() {
        try {
            $this->food = new Food();
            $this->meal = new Meal();
        } catch (Exception $e) {
            error_log("DNMS Meal Controller Error: " . $e->getMessage());
            die("System error occurred. Please contact administrator.");
        }
    }

    public function index() {
        include __DIR__ . '/../../views/meal/log.php';
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: log.php');
            exit();
        }

        $userId = $_SESSION['user_id'];
        $mealName = trim($_POST['meal_name']);
        $mealDate = $_POST['meal_date'];
        $mealTime = $_POST['meal_time'];
        $mealType = $_POST['meal_type'];
        $notes = trim($_POST['notes']);

        // Validate required fields
        if (empty($mealName) || empty($mealDate) || empty($mealTime) || empty($mealType)) {
            $this->redirectWithError('Please fill in all required fields.');
            return;
        }

        // Calculate nutrition totals from selected foods
        $totalCalories = 0;
        $totalProtein = 0;
        $totalCarbs = 0;
        $totalFat = 0;
        $foods = [];

        // In a real implementation, this would come from form data
        // For now, simulate with sample data
        $sampleFoods = [
            ['food_id' => 1, 'quantity_grams' => 150, 'calories' => 78, 'protein' => 0.45, 'carbs' => 21, 'fat' => 0.3],
            ['food_id' => 2, 'quantity_grams' => 100, 'calories' => 165, 'protein' => 31, 'carbs' => 0, 'fat' => 3.6]
        ];

        foreach ($sampleFoods as $food) {
            $totalCalories += $food['calories'];
            $totalProtein += $food['protein'];
            $totalCarbs += $food['carbs'];
            $totalFat += $food['fat'];
            $foods[] = $food;
        }

        $mealData = [
            'user_id' => $userId,
            'meal_name' => $mealName,
            'meal_date' => $mealDate,
            'meal_time' => $mealTime,
            'meal_type' => $mealType,
            'total_calories' => $totalCalories,
            'total_protein' => $totalProtein,
            'total_carbs' => $totalCarbs,
            'total_fat' => $totalFat,
            'notes' => $notes,
            'foods' => $foods
        ];

        $mealId = $this->meal->createMeal($mealData);

        if ($mealId) {
            $this->redirectWithSuccess('Meal logged successfully!');
        } else {
            $this->redirectWithError('Failed to save meal. Please try again.');
        }
    }

    private function redirectWithSuccess($message) {
        $_SESSION['flash'] = ['type' => 'success', 'message' => $message];
        header('Location: log.php');
        exit();
    }

    private function redirectWithError($message) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => $message];
        header('Location: log.php');
        exit();
    }
}

// Handle actions
$action = $_GET['action'] ?? 'index';

$controller = new MealLogController();
if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    $controller->index();
}
?>
