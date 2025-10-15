<?php
/**
 * User Class
 * DNMS-PHP - Diet & Nutrition Management System
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/environment.php';

class User {
    private $db;
    
    public function __construct() {
        try {
            $this->db = getDNMSDBConnection();
        } catch (Exception $e) {
            $this->db = null;
        }
    }
    
    public function authenticate($username, $password) {
        if (!$this->db) return false;

        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = ? AND status = 'active'");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $this->updateLastLogin($user['id']);
                unset($user['password']);
                return $user;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function create($data) {
        if (!$this->db) return false;

        try {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            $data['created_at'] = date(DATETIME_FORMAT);
            return $this->db->insert('users', $data);
        } catch (Exception $e) {
            return false;
        }
    }

    public function update($id, $data) {
        if (!$this->db) return false;

        try {
            $data['updated_at'] = date(DATETIME_FORMAT);
            return $this->db->update('users', $data, 'id = :id', ['id' => $id]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function getById($id) {
        if (!$this->db) return false;

        try {
            $stmt = $this->db->prepare("SELECT id, user_id, email, role, full_name, created_at FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }
    }

    public function getProfile($id) {
        $user = $this->getById($id);

        if ($user) {
            try {
                // Calculate nutritional needs
                $user['bmi'] = calculateBMI($user['weight'], $user['height']);
                $user['bmi_category'] = getBMICategory($user['bmi']);
                $user['bmr'] = calculateBMR($user['weight'], $user['height'], $user['age'], $user['gender']);
                $user['tdee'] = calculateTDEE($user['bmr'], $user['activity_level']);
                $user['target_calories'] = calculateTargetCalories($user['tdee'], $user['goal']);
                $user['macros'] = calculateMacros($user['target_calories']);
                $user['water_intake'] = calculateWaterIntake($user['weight']);
            } catch (Exception $e) {
                // Set default values if calculations fail
                $user['bmi'] = 0;
                $user['bmi_category'] = 'unknown';
                $user['bmr'] = 0;
                $user['tdee'] = 0;
                $user['target_calories'] = 0;
                $user['macros'] = ['protein' => 0, 'carbs' => 0, 'fat' => 0];
                $user['water_intake'] = 0;
            }
        }

        return $user;
    }

    private function updateLastLogin($id) {
        if (!$this->db) return;

        try {
            $stmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$id]);
        } catch (Exception $e) {
            // Ignore update errors
        }
    }

    /**
     * Get user goals and preferences for AI recommendations
     */
    public function getUserGoals($userId) {
        if (!$this->db) return [];

        try {
            // Get user profile data that represents goals
            $stmt = $this->db->prepare("
                SELECT weight, height, age, gender, activity_level, goal,
                       target_weight, target_date
                FROM users
                WHERE id = ?
            ");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) return [];

            // Calculate goals based on profile
            $goals = [];

            // Calorie goal based on profile
            if ($user['weight'] && $user['height'] && $user['age'] && $user['gender']) {
                $bmi = calculateBMI($user['weight'], $user['height']);
                $bmr = calculateBMR($user['weight'], $user['height'], $user['age'], $user['gender']);
                $tdee = calculateTDEE($bmr, $user['activity_level'] ?? 'moderate');
                $goals['calorie_goal'] = calculateTargetCalories($tdee, $user['goal'] ?? 'maintenance');
            }

            // Goal type
            if ($user['goal']) {
                $goals['goal_type'] = $user['goal'];
            }

            // Weight goal
            if ($user['target_weight'] && $user['target_date']) {
                $goals['target_weight'] = $user['target_weight'];
                $goals['target_date'] = $user['target_date'];
            }

            return $goals;

        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get user preferences for personalized recommendations
     */
    public function getUserPreferences($userId) {
        if (!$this->db) return [];

        try {
            // Get user preferences (if stored) or infer from profile
            $preferences = [];

            // Check if user has explicit preferences stored
            $stmt = $this->db->prepare("
                SELECT dietary_restrictions, allergies, preferences
                FROM user_preferences
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            $prefs = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($prefs) {
                $preferences = array_merge($preferences, $prefs);
            }

            // Infer preferences from profile if not explicitly set
            $user = $this->getById($userId);
            if ($user) {
                // Low carb preference if user is trying to lose weight
                if (($user['goal'] ?? '') === 'weight_loss') {
                    $preferences['low_carb'] = 'yes';
                }

                // High protein preference if user is trying to build muscle
                if (($user['goal'] ?? '') === 'muscle_gain') {
                    $preferences['high_protein'] = 'yes';
                }
            }

            return $preferences;

        } catch (Exception $e) {
            return [];
        }
    }
}
