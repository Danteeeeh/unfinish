<?php
/**
 * Body Measurements Tracking Class
 * Handles body measurements logging and tracking
 */

class BodyMeasurements {
    private $db;

    public function __construct() {
        $this->db = getDNMSDBConnection();
    }

    /**
     * Log body measurements
     */
    public function logMeasurements($userId, $data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO dnms_body_measurements (
                    user_id, measurement_date, weight_kg, height_cm, body_fat_percentage,
                    muscle_mass_kg, bmi, waist_cm, chest_cm, hips_cm, arms_cm, thighs_cm,
                    notes, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $userId,
                $data['measurement_date'] ?? date('Y-m-d'),
                $data['weight_kg'] ?? null,
                $data['height_cm'] ?? null,
                $data['body_fat_percentage'] ?? null,
                $data['muscle_mass_kg'] ?? null,
                $data['bmi'] ?? null,
                $data['waist_cm'] ?? null,
                $data['chest_cm'] ?? null,
                $data['hips_cm'] ?? null,
                $data['arms_cm'] ?? null,
                $data['thighs_cm'] ?? null,
                $data['notes'] ?? ''
            ]);

            return ['success' => true, 'measurement_id' => $this->db->lastInsertId()];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get user's body measurements
     */
    public function getUserMeasurements($userId, $limit = 50, $offset = 0) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM dnms_body_measurements
                WHERE user_id = ?
                ORDER BY measurement_date DESC, created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$userId, $limit, $offset]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get latest measurements
     */
    public function getLatestMeasurements($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM dnms_body_measurements
                WHERE user_id = ?
                ORDER BY measurement_date DESC, created_at DESC
                LIMIT 1
            ");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get measurement history for charts
     */
    public function getMeasurementHistory($userId, $days = 30) {
        try {
            $stmt = $this->db->prepare("
                SELECT measurement_date, weight_kg, body_fat_percentage, muscle_mass_kg, bmi
                FROM dnms_body_measurements
                WHERE user_id = ? AND measurement_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                ORDER BY measurement_date ASC
            ");
            $stmt->execute([$userId, $days]);

            $data = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[] = [
                    'date' => $row['measurement_date'],
                    'weight' => $row['weight_kg'],
                    'body_fat' => $row['body_fat_percentage'],
                    'muscle_mass' => $row['muscle_mass_kg'],
                    'bmi' => $row['bmi']
                ];
            }

            return $data;

        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Update measurements
     */
    public function updateMeasurements($measurementId, $userId, $data) {
        try {
            $fields = [];
            $values = [];

            $allowedFields = [
                'measurement_date', 'weight_kg', 'height_cm', 'body_fat_percentage',
                'muscle_mass_kg', 'bmi', 'waist_cm', 'chest_cm', 'hips_cm',
                'arms_cm', 'thighs_cm', 'notes'
            ];

            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "{$field} = ?";
                    $values[] = $data[$field];
                }
            }

            if (empty($fields)) {
                return ['success' => false, 'error' => 'No fields to update'];
            }

            $values[] = $measurementId;
            $values[] = $userId;

            $sql = "UPDATE dnms_body_measurements SET " . implode(', ', $fields) . " WHERE id = ? AND user_id = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($values);

            return ['success' => true];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Delete measurements
     */
    public function deleteMeasurements($measurementId, $userId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM dnms_body_measurements WHERE id = ? AND user_id = ?");
            $stmt->execute([$measurementId, $userId]);
            return ['success' => true];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Calculate BMI
     */
    public function calculateBMI($weightKg, $heightCm) {
        if (!$weightKg || !$heightCm) {
            return null;
        }

        $heightM = $heightCm / 100;
        return round($weightKg / ($heightM * $heightM), 2);
    }

    /**
     * Get measurement statistics
     */
    public function getMeasurementStats($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT
                    COUNT(*) as total_measurements,
                    AVG(weight_kg) as avg_weight,
                    MIN(weight_kg) as min_weight,
                    MAX(weight_kg) as max_weight,
                    AVG(body_fat_percentage) as avg_body_fat,
                    AVG(muscle_mass_kg) as avg_muscle_mass,
                    MIN(measurement_date) as first_measurement,
                    MAX(measurement_date) as last_measurement
                FROM dnms_body_measurements
                WHERE user_id = ? AND weight_kg IS NOT NULL
            ");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            return [
                'total_measurements' => 0,
                'avg_weight' => 0,
                'min_weight' => 0,
                'max_weight' => 0,
                'avg_body_fat' => 0,
                'avg_muscle_mass' => 0,
                'first_measurement' => null,
                'last_measurement' => null
            ];
        }
    }

    /**
     * Get weight progress data
     */
    public function getWeightProgress($userId, $days = 30) {
        try {
            $stmt = $this->db->prepare("
                SELECT measurement_date, weight_kg
                FROM dnms_body_measurements
                WHERE user_id = ? AND weight_kg IS NOT NULL
                AND measurement_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                ORDER BY measurement_date ASC
            ");
            $stmt->execute([$userId, $days]);

            $data = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[] = [
                    'date' => $row['measurement_date'],
                    'weight' => $row['weight_kg']
                ];
            }

            return $data;

        } catch (Exception $e) {
            return [];
        }
    }
}
?>
