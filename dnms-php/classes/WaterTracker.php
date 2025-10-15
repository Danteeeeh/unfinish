<?php
/**
 * Water Intake Tracking Class
 * Handles water intake logging and tracking
 */

class WaterTracker {
    private $db;

    public function __construct() {
        $this->db = getDNMSDBConnection();
    }

    /**
     * Log water intake
     */
    public function logWaterIntake($userId, $data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO dnms_water_intake (user_id, amount_ml, intake_time, intake_date, notes, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $userId,
                $data['amount_ml'],
                $data['intake_time'] ?? date('H:i:s'),
                $data['intake_date'] ?? date('Y-m-d'),
                $data['notes'] ?? ''
            ]);

            return ['success' => true, 'intake_id' => $this->db->lastInsertId()];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get user's water intake for a specific date
     */
    public function getWaterIntakeForDate($userId, $date) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM dnms_water_intake
                WHERE user_id = ? AND intake_date = ?
                ORDER BY intake_time ASC
            ");
            $stmt->execute([$userId, $date]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get daily water intake summary
     */
    public function getDailyWaterSummary($userId, $date) {
        try {
            $stmt = $this->db->prepare("
                SELECT
                    SUM(amount_ml) as total_ml,
                    COUNT(*) as intake_count,
                    AVG(amount_ml) as avg_intake
                FROM dnms_water_intake
                WHERE user_id = ? AND intake_date = ?
            ");
            $stmt->execute([$userId, $date]);
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            return ['total_ml' => 0, 'intake_count' => 0, 'avg_intake' => 0];
        }
    }

    /**
     * Get weekly water intake data for charts
     */
    public function getWeeklyWaterData($userId) {
        try {
            $data = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-{$i} days"));
                $dayName = date('D', strtotime($date));

                $summary = $this->getDailyWaterSummary($userId, $date);

                $data[] = [
                    'day' => $dayName,
                    'date' => $date,
                    'total_ml' => $summary['total_ml'] ?? 0,
                    'intake_count' => $summary['intake_count'] ?? 0
                ];
            }

            return $data;

        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Update water intake entry
     */
    public function updateWaterIntake($intakeId, $userId, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE dnms_water_intake
                SET amount_ml = ?, intake_time = ?, notes = ?
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([
                $data['amount_ml'],
                $data['intake_time'] ?? date('H:i:s'),
                $data['notes'] ?? '',
                $intakeId,
                $userId
            ]);

            return ['success' => true];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Delete water intake entry
     */
    public function deleteWaterIntake($intakeId, $userId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM dnms_water_intake WHERE id = ? AND user_id = ?");
            $stmt->execute([$intakeId, $userId]);
            return ['success' => true];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get water intake goal for user
     */
    public function getWaterGoal($userId) {
        try {
            // Default to 2000ml (8 glasses) if no custom goal set
            $defaultGoal = 2000;

            $stmt = $this->db->prepare("
                SELECT water_goal_ml FROM dnms_user_settings WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ? $result['water_goal_ml'] : $defaultGoal;

        } catch (Exception $e) {
            return 2000; // Default goal
        }
    }

    /**
     * Set water intake goal
     */
    public function setWaterGoal($userId, $goalMl) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO dnms_user_settings (user_id, water_goal_ml, updated_at)
                VALUES (?, ?, NOW())
                ON DUPLICATE KEY UPDATE water_goal_ml = ?, updated_at = NOW()
            ");
            $stmt->execute([$userId, $goalMl, $goalMl]);

            return ['success' => true];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get water intake statistics
     */
    public function getWaterStats($userId, $days = 7) {
        try {
            $stmt = $this->db->prepare("
                SELECT
                    AVG(daily_total) as avg_daily,
                    MAX(daily_total) as max_daily,
                    MIN(daily_total) as min_daily,
                    SUM(total_intakes) as total_intakes,
                    COUNT(DISTINCT intake_date) as days_tracked
                FROM (
                    SELECT
                        intake_date,
                        SUM(amount_ml) as daily_total,
                        COUNT(*) as total_intakes
                    FROM dnms_water_intake
                    WHERE user_id = ? AND intake_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                    GROUP BY intake_date
                ) as daily_stats
            ");
            $stmt->execute([$userId, $days]);
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            return [
                'avg_daily' => 0,
                'max_daily' => 0,
                'min_daily' => 0,
                'total_intakes' => 0,
                'days_tracked' => 0
            ];
        }
    }
}
?>
