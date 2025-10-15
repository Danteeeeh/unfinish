<?php
require_once __DIR__ . '/../config/database.php';

class Report {
    private $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    public function generateMealReport($userId, $startDate, $endDate) {
        $stmt = $this->db->prepare("SELECT * FROM dnms_meals WHERE user_id = ? AND meal_date BETWEEN ? AND ?");
        $stmt->execute([$userId, $startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function generateNutritionSummary($userId) {
        // Simplified summary
        return ['total_calories' => 1500, 'avg_protein' => 100];
    }
}
