<?php
/**
 * DNMS Analytics Controller
 * Advanced nutrition analytics and reporting system
 */

// Load main config for authentication functions
try {
    require_once __DIR__ . '/../../../config.php';
} catch (Exception $e) {
    // If main config fails, redirect to setup
    header('Location: ../../../database_setup.php');
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../login.php');
    exit();
}

// Verify session is still valid
if (!function_exists('isLoggedIn') || !isLoggedIn()) {
    header('Location: ../../../login.php');
    exit();
}

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/environment.php';
require_once __DIR__ . '/../../includes/auth_functions.php';
require_once __DIR__ . '/../../classes/User.php';
require_once __DIR__ . '/../../classes/Food.php';
require_once __DIR__ . '/../../classes/Meal.php';
require_once __DIR__ . '/../../classes/Report.php';

class AnalyticsController {
    private $db;
    private $user;
    private $food;
    private $meal;
    private $report;

    public function __construct() {
        try {
            $this->db = getDNMSDBConnection();
            $this->user = new User();
            $this->food = new Food();
            $this->meal = new Meal();
            $this->report = new Report();

            // Test database connection
            $this->testDatabaseSetup();
        } catch (Exception $e) {
            error_log("DNMS Analytics Controller Error: " . $e->getMessage());
            $this->showError("System initialization failed: " . $e->getMessage());
        }
    }

    public function index() {
        try {
            $user = getCurrentUser();
            $userId = $user['id'];

            // Get analytics data
            $analytics = $this->getAnalyticsData($userId);
            $charts = $this->getChartData($userId);
            $insights = $this->getNutritionInsights($userId);

            include __DIR__ . '/../../views/reports/analytics.php';
        } catch (Exception $e) {
            error_log("DNMS Analytics Error: " . $e->getMessage());
            $this->showError("Failed to load analytics: " . $e->getMessage());
        }
    }

    private function getAnalyticsData($userId) {
        $today = date('Y-m-d');
        $weekAgo = date('Y-m-d', strtotime('-7 days'));
        $monthAgo = date('Y-m-d', strtotime('-30 days'));

        return [
            'daily' => $this->getDailyAnalytics($userId, $today),
            'weekly' => $this->getWeeklyAnalytics($userId, $weekAgo),
            'monthly' => $this->getMonthlyAnalytics($userId, $monthAgo),
            'trends' => $this->getTrendAnalytics($userId),
            'goals' => $this->getGoalAnalytics($userId),
            'patterns' => $this->getPatternAnalytics($userId)
        ];
    }

    private function getDailyAnalytics($userId, $date) {
        try {
            $stmt = $this->db->prepare("
                SELECT
                    SUM(total_calories) as calories,
                    SUM(total_protein) as protein,
                    SUM(total_carbs) as carbs,
                    SUM(total_fat) as fat,
                    COUNT(*) as meal_count,
                    AVG(total_calories) as avg_calories_per_meal
                FROM dnms_meals
                WHERE user_id = ? AND DATE(meal_date) = ?
            ");
            $stmt->execute([$userId, $date]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?? [];
        } catch (Exception $e) {
            return [];
        }
    }

    private function getWeeklyAnalytics($userId, $weekAgo) {
        try {
            $stmt = $this->db->prepare("
                SELECT
                    DATE(meal_date) as date,
                    SUM(total_calories) as calories,
                    SUM(total_protein) as protein,
                    SUM(total_carbs) as carbs,
                    SUM(total_fat) as fat,
                    COUNT(*) as meals
                FROM dnms_meals
                WHERE user_id = ? AND meal_date >= ?
                GROUP BY DATE(meal_date)
                ORDER BY date DESC
            ");
            $stmt->execute([$userId, $weekAgo]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    private function getMonthlyAnalytics($userId, $monthAgo) {
        try {
            $stmt = $this->db->prepare("
                SELECT
                    COUNT(*) as total_meals,
                    AVG(total_calories) as avg_daily_calories,
                    SUM(total_calories) as total_calories,
                    MIN(meal_date) as first_meal,
                    MAX(meal_date) as last_meal
                FROM dnms_meals
                WHERE user_id = ? AND meal_date >= ?
            ");
            $stmt->execute([$userId, $monthAgo]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?? [];
        } catch (Exception $e) {
            return [];
        }
    }

    private function getTrendAnalytics($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT
                    DATE_FORMAT(meal_date, '%Y-%m-%d') as date,
                    AVG(total_calories) as avg_calories,
                    AVG(total_protein) as avg_protein,
                    AVG(total_carbs) as avg_carbs,
                    AVG(total_fat) as avg_fat
                FROM dnms_meals
                WHERE user_id = ? AND meal_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY DATE(meal_date)
                ORDER BY date
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    private function getGoalAnalytics($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT
                    goal_type,
                    target_value,
                    current_value,
                    achieved_date,
                    status
                FROM dnms_nutrition_goals
                WHERE user_id = ?
                ORDER BY created_date DESC
                LIMIT 10
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    private function getPatternAnalytics($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT
                    HOUR(meal_time) as hour,
                    COUNT(*) as meal_count,
                    AVG(total_calories) as avg_calories
                FROM dnms_meals
                WHERE user_id = ? AND meal_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                GROUP BY HOUR(meal_time)
                ORDER BY hour
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    private function getChartData($userId) {
        return [
            'calorie_trend' => $this->getCalorieTrend($userId),
            'macro_distribution' => $this->getMacroDistribution($userId),
            'meal_frequency' => $this->getMealFrequency($userId),
            'goal_progress' => $this->getGoalProgress($userId)
        ];
    }

    private function getCalorieTrend($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT
                    DATE(meal_date) as date,
                    SUM(total_calories) as total_calories
                FROM dnms_meals
                WHERE user_id = ? AND meal_date >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
                GROUP BY DATE(meal_date)
                ORDER BY date
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    private function getMacroDistribution($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT
                    'Protein' as macro,
                    AVG(total_protein) as value
                FROM dnms_meals
                WHERE user_id = ? AND meal_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)

                UNION ALL

                SELECT
                    'Carbs' as macro,
                    AVG(total_carbs) as value
                FROM dnms_meals
                WHERE user_id = ? AND meal_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)

                UNION ALL

                SELECT
                    'Fat' as macro,
                    AVG(total_fat) as value
                FROM dnms_meals
                WHERE user_id = ? AND meal_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            ");
            $stmt->execute([$userId, $userId, $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    private function getMealFrequency($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT
                    DAYNAME(meal_date) as day,
                    COUNT(*) as meals,
                    AVG(total_calories) as avg_calories
                FROM dnms_meals
                WHERE user_id = ? AND meal_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                GROUP BY DAYOFWEEK(meal_date), DAYNAME(meal_date)
                ORDER BY DAYOFWEEK(meal_date)
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    private function getGoalProgress($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT
                    goal_type,
                    target_value,
                    current_value,
                    (current_value / target_value * 100) as progress_percent
                FROM dnms_nutrition_goals
                WHERE user_id = ? AND status = 'active'
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    private function getNutritionInsights($userId) {
        $insights = [];

        // Calorie intake analysis
        $weeklyCalories = $this->getWeeklyTotalCalories($userId);
        $avgDailyCalories = $weeklyCalories / 7;

        if ($avgDailyCalories > 2500) {
            $insights[] = [
                'type' => 'warning',
                'title' => 'High Calorie Intake',
                'message' => 'Your average daily calorie intake is high. Consider portion control.'
            ];
        } elseif ($avgDailyCalories < 1500) {
            $insights[] = [
                'type' => 'info',
                'title' => 'Low Calorie Intake',
                'message' => 'Your calorie intake is quite low. Make sure you\'re getting enough nutrients.'
            ];
        } else {
            $insights[] = [
                'type' => 'success',
                'title' => 'Balanced Intake',
                'message' => 'Great job maintaining a balanced calorie intake!'
            ];
        }

        // Protein analysis
        $proteinRatio = $this->getMacroRatio($userId, 'protein');
        if ($proteinRatio < 15) {
            $insights[] = [
                'type' => 'info',
                'title' => 'Low Protein',
                'message' => 'Consider adding more protein-rich foods to your meals.'
            ];
        }

        // Meal frequency analysis
        $mealFrequency = $this->getAverageMealFrequency($userId);
        if ($mealFrequency < 2) {
            $insights[] = [
                'type' => 'warning',
                'title' => 'Irregular Meals',
                'message' => 'You might benefit from more regular meal times.'
            ];
        }

        return $insights;
    }

    private function getWeeklyTotalCalories($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT SUM(total_calories) as total
                FROM dnms_meals
                WHERE user_id = ? AND meal_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            ");
            $stmt->execute([$userId]);
            return $stmt->fetch()['total'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    private function getMacroRatio($userId, $macro) {
        try {
            $column = "total_$macro";
            $stmt = $this->db->prepare("
                SELECT AVG($column) as avg_macro
                FROM dnms_meals
                WHERE user_id = ? AND meal_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch();

            $totalCalories = $this->getWeeklyTotalCalories($userId);
            return $totalCalories > 0 ? (($result['avg_macro'] * 4) / $totalCalories) * 100 : 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    private function getAverageMealFrequency($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT AVG(daily_meals) as avg_frequency
                FROM (
                    SELECT DATE(meal_date) as date, COUNT(*) as daily_meals
                    FROM dnms_meals
                    WHERE user_id = ? AND meal_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                    GROUP BY DATE(meal_date)
                ) as daily_counts
            ");
            $stmt->execute([$userId]);
            return $stmt->fetch()['avg_frequency'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
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
            <title>Analytics Error - NutriTrack Pro</title>
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
                <h1>⚠️ Analytics Error</h1>
                <p><?php echo htmlspecialchars($message); ?></p>
                <a href="../index.php" class="btn">Back to Dashboard</a>
            </div>
        </body>
        </html>
        <?php
        exit();
    }
}

// Instantiate and run the analytics controller
try {
    $controller = new AnalyticsController();
    $controller->index();
} catch (Exception $e) {
    error_log("DNMS Analytics Controller Instantiation Error: " . $e->getMessage());
    die("System error occurred. Please contact administrator.");
}
?>
