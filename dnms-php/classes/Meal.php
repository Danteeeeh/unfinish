<?php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/environment.php';

class Meal {
    private $db;

    public function __construct() {
        try {
            $this->db = getDNMSDBConnection();
        } catch (Exception $e) {
            $this->db = null;
        }
    }

    public function getRecent($userId, $limit = 5) {
        if (!$this->db) return [];

        try {
            $stmt = $this->db->prepare("SELECT * FROM dnms_meals WHERE user_id = ? ORDER BY meal_date DESC LIMIT ?");
            $stmt->execute([$userId, $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function getUserMeals($userId, $limit = 10) {
        if (!$this->db) return [];

        try {
            $stmt = $this->db->prepare("SELECT * FROM dnms_meals WHERE user_id = ? ORDER BY meal_date DESC, meal_time DESC LIMIT ?");
            $stmt->execute([$userId, $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function createMeal($data) {
        if (!$this->db) return false;

        try {
            $this->db->beginTransaction();

            // Insert meal
            $stmt = $this->db->prepare("INSERT INTO dnms_meals (user_id, meal_name, meal_date, meal_time, meal_type, total_calories, total_protein, total_carbs, total_fat, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['user_id'],
                $data['meal_name'],
                $data['meal_date'],
                $data['meal_time'],
                $data['meal_type'],
                $data['total_calories'],
                $data['total_protein'],
                $data['total_carbs'],
                $data['total_fat'],
                $data['notes'] ?? ''
            ]);

            $mealId = $this->db->lastInsertId();

            // Insert meal items
            if (isset($data['foods']) && is_array($data['foods'])) {
                foreach ($data['foods'] as $food) {
                    $stmt = $this->db->prepare("INSERT INTO dnms_meal_items (meal_id, food_id, quantity_grams, calories, protein, carbs, fat) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $mealId,
                        $food['food_id'],
                        $food['quantity_grams'],
                        $food['calories'],
                        $food['protein'],
                        $food['carbs'],
                        $food['fat']
                    ]);
                }
            }

            $this->db->commit();
            return $mealId;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getMealWithItems($mealId) {
        if (!$this->db) return null;

        try {
            // Get meal info
            $stmt = $this->db->prepare("SELECT * FROM dnms_meals WHERE id = ?");
            $stmt->execute([$mealId]);
            $meal = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$meal) return null;

            // Get meal items
            $stmt = $this->db->prepare("SELECT mi.*, f.food_name, f.food_category FROM dnms_meal_items mi JOIN dnms_foods f ON mi.food_id = f.id WHERE mi.meal_id = ?");
            $stmt->execute([$mealId]);
            $meal['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $meal;
        } catch (Exception $e) {
            return null;
        }
    }
}
