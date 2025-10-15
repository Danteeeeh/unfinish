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

class FoodSearchController {
    private $food;

    public function __construct() {
        try {
            $this->food = new Food();
        } catch (Exception $e) {
            error_log("DNMS Food Controller Error: " . $e->getMessage());
            die("System error occurred. Please contact administrator.");
        }
    }

    public function index() {
        // Sanitize and validate inputs
        $searchTerm = trim(strip_tags($_GET['search'] ?? ''));
        $category = trim(strip_tags($_GET['category'] ?? ''));
        $page = max(1, intval($_GET['page'] ?? 1));
        $perPage = min(50, max(1, intval($_GET['per_page'] ?? 12))); // Limit per page

        // Validate search term length
        if (strlen($searchTerm) > 100) {
            $searchTerm = substr($searchTerm, 0, 100);
        }

        // Validate category (should be alphanumeric or specific allowed values)
        $allowedCategories = ['fruits', 'vegetables', 'proteins', 'grains', 'dairy', 'other'];
        if (!empty($category) && !in_array(strtolower($category), $allowedCategories)) {
            $category = '';
        }

        try {
            if (!empty($searchTerm) || !empty($category)) {
                $foods = $this->food->searchFoods($searchTerm, $category, $perPage, $page);
                $totalFoods = $this->food->getSearchCount($searchTerm, $category);
            } else {
                $foods = $this->food->getFoods($perPage, $page);
                $totalFoods = $this->food->getTotalCount();
            }

            $totalPages = ceil($totalFoods / $perPage);

            include __DIR__ . '/../../views/food/search.php';
        } catch (Exception $e) {
            error_log("DNMS Food Search Error: " . $e->getMessage());
            $foods = [];
            $totalPages = 1;
            include __DIR__ . '/../../views/food/search.php';
        }
    }
}

// Instantiate and run the controller
try {
    $controller = new FoodSearchController();
    $controller->index();
} catch (Exception $e) {
    error_log("DNMS Controller Instantiation Error: " . $e->getMessage());
    die("System error occurred. Please contact administrator.");
}
?>
