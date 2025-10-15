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
require_once __DIR__ . '/../../includes/auth_functions.php';

require_once __DIR__ . '/../../classes/User.php';
require_once __DIR__ . '/../../classes/Meal.php';
require_once __DIR__ . '/../../classes/Nutrition.php';

class GoalsController {
    private $db;
    private $user;
    private $meal;
    private $nutrition;

    public function __construct() {
        try {
            $this->db = getDNMSDBConnection();
            $this->user = new User();
            $this->meal = new Meal();
            $this->nutrition = new Nutrition();
            $this->testDatabaseSetup();
        } catch (Exception $e) {
            error_log("DNMS Goals Controller Error: " . $e->getMessage());
            $this->showError("System initialization failed: " . $e->getMessage());
        }
    }

    public function index() {
        try {
            $user = getCurrentUser();
            $userId = $user['id'];

            $activeGoals = $this->getActiveGoals($userId);
            $completedGoals = $this->getCompletedGoals($userId);
            $goalSuggestions = $this->getGoalSuggestions($userId);
            $progressStats = $this->getProgressStats($userId);

            include __DIR__ . '/../../views/goals/index.php';
        } catch (Exception $e) {
            error_log("DNMS Goals Error: " . $e->getMessage());
            $this->showError("Failed to load goals: " . $e->getMessage());
        }
    }

    public function create() {
        try {
            $user = getCurrentUser();
            $userId = $user['id'];

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $goalData = [
                    'goal_type' => $_POST['goal_type'],
                    'target_value' => $_POST['target_value'],
                    'current_value' => 0,
                    'target_date' => $_POST['target_date'],
                    'status' => 'active'
                ];

                $this->createGoal($userId, $goalData);
                header('Location: index.php?success=1');
                exit();
            }

            include __DIR__ . '/../../views/goals/create.php';
        } catch (Exception $e) {
            $this->showError("Failed to create goal: " . $e->getMessage());
        }
    }

    private function getActiveGoals($userId) {
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

    private function getCompletedGoals($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM dnms_nutrition_goals
                WHERE user_id = ? AND status = 'completed'
                ORDER BY achieved_date DESC
                LIMIT 10
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    private function getGoalSuggestions($userId) {
        // Suggest goals based on user's current nutrition patterns
        $suggestions = [
            [
                'type' => 'daily_calories',
                'title' => 'Maintain Healthy Calorie Intake',
                'description' => 'Aim for 2,000-2,500 calories per day',
                'target_value' => 2200,
                'unit' => 'calories'
            ],
            [
                'type' => 'protein_intake',
                'title' => 'Increase Protein Consumption',
                'description' => 'Target 1.6g of protein per kg of body weight',
                'target_value' => 120,
                'unit' => 'grams'
            ],
            [
                'type' => 'meal_frequency',
                'title' => 'Regular Meal Times',
                'description' => 'Eat at least 3 meals per day',
                'target_value' => 3,
                'unit' => 'meals'
            ]
        ];

        return $suggestions;
    }

    private function getProgressStats($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT
                    COUNT(*) as total_goals,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_goals,
                    AVG(CASE WHEN current_value > 0 THEN (current_value / target_value) * 100 ELSE 0 END) as avg_progress
                FROM dnms_nutrition_goals
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return ['total_goals' => 0, 'completed_goals' => 0, 'avg_progress' => 0];
        }
    }

    private function createGoal($userId, $goalData) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO dnms_nutrition_goals (user_id, goal_type, target_value, current_value, target_date, status, created_date)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $userId,
                $goalData['goal_type'],
                $goalData['target_value'],
                $goalData['current_value'],
                $goalData['target_date'],
                $goalData['status']
            ]);
        } catch (Exception $e) {
            throw new Exception('Failed to create goal: ' . $e->getMessage());
        }
    }

    private function testDatabaseSetup() {
        $tableCheck = checkDNMSTablesExist();
        if ($tableCheck['status'] !== 'complete') {
            $missingTables = implode(', ', $tableCheck['missing_tables']);
            throw new Exception("DNMS database setup incomplete. Missing tables: {$missingTables}");
        }
    }

    private function showError($message) {
        ?>
        <!DOCTYPE html>
        <html lang="en" data-theme="light">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Goals Error - NutriTrack Pro</title>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
            <style>
                :root {
                    --primary-color: #4ecdc4;
                    --danger-color: #f56565;
                    --bg-primary: #ffffff;
                    --text-primary: #212529;
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
                .error-container h1 { color: var(--danger-color); margin-bottom: 1rem; }
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
                .btn:hover { transform: translateY(-2px); }
            </style>
        </head>
        <body>
            <div class="error-container">
                <h1>🎯 Goals Error</h1>
                <p><?php echo htmlspecialchars($message); ?></p>
                <a href="index.php" class="btn">Back to Goals</a>
            </div>
        </body>
        </html>
        <?php
        exit();
    }
}

// Handle different actions
$action = $_GET['action'] ?? 'index';

try {
    $controller = new GoalsController();

    switch ($action) {
        case 'create':
            $controller->create();
            break;
        default:
            $controller->index();
            break;
    }
} catch (Exception $e) {
    error_log("DNMS Goals Controller Instantiation Error: " . $e->getMessage());
    die("System error occurred. Please contact administrator.");
}
?>
