<?php
/**
 * Exercise Tracking Class
 * Handles exercise logging, tracking, and management
 */

class Exercise {
    private $db;

    public function __construct() {
        $this->db = getDNMSDBConnection();
    }

    /**
     * Log an exercise session
     */
    public function logExercise($userId, $data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO dnms_exercises (user_id, exercise_name, exercise_type, duration_minutes, calories_burned,
                                          distance_km, sets, reps, weight_kg, notes, exercise_date, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $userId,
                $data['exercise_name'],
                $data['exercise_type'],
                $data['duration_minutes'] ?? 0,
                $data['calories_burned'] ?? 0,
                $data['distance_km'] ?? 0,
                $data['sets'] ?? 0,
                $data['reps'] ?? 0,
                $data['weight_kg'] ?? 0,
                $data['notes'] ?? '',
                $data['exercise_date'] ?? date('Y-m-d')
            ]);

            return ['success' => true, 'exercise_id' => $this->db->lastInsertId()];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get user's exercise history
     */
    public function getUserExercises($userId, $limit = 50, $offset = 0) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM dnms_exercises
                WHERE user_id = ?
                ORDER BY exercise_date DESC, created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$userId, $limit, $offset]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get exercise summary for dashboard
     */
    public function getExerciseSummary($userId, $days = 7) {
        try {
            $stmt = $this->db->prepare("
                SELECT
                    COUNT(*) as total_sessions,
                    SUM(duration_minutes) as total_minutes,
                    SUM(calories_burned) as total_calories,
                    AVG(calories_burned) as avg_calories_per_session,
                    SUM(distance_km) as total_distance
                FROM dnms_exercises
                WHERE user_id = ? AND exercise_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            ");
            $stmt->execute([$userId, $days]);
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            return [
                'total_sessions' => 0,
                'total_minutes' => 0,
                'total_calories' => 0,
                'avg_calories_per_session' => 0,
                'total_distance' => 0
            ];
        }
    }

    /**
     * Get exercise by ID
     */
    public function getExerciseById($exerciseId, $userId = null) {
        try {
            $sql = "SELECT * FROM dnms_exercises WHERE id = ?";
            $params = [$exerciseId];

            if ($userId) {
                $sql .= " AND user_id = ?";
                $params[] = $userId;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Update exercise entry
     */
    public function updateExercise($exerciseId, $userId, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE dnms_exercises
                SET exercise_name = ?, exercise_type = ?, duration_minutes = ?, calories_burned = ?,
                    distance_km = ?, sets = ?, reps = ?, weight_kg = ?, notes = ?
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([
                $data['exercise_name'],
                $data['exercise_type'],
                $data['duration_minutes'] ?? 0,
                $data['calories_burned'] ?? 0,
                $data['distance_km'] ?? 0,
                $data['sets'] ?? 0,
                $data['reps'] ?? 0,
                $data['weight_kg'] ?? 0,
                $data['notes'] ?? '',
                $exerciseId,
                $userId
            ]);

            return ['success' => true];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Delete exercise entry
     */
    public function deleteExercise($exerciseId, $userId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM dnms_exercises WHERE id = ? AND user_id = ?");
            $stmt->execute([$exerciseId, $userId]);
            return ['success' => true];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get exercise types for dropdown
     */
    public function getExerciseTypes() {
        return [
            'cardio' => 'Cardio',
            'strength' => 'Strength Training',
            'flexibility' => 'Flexibility/Stretching',
            'sports' => 'Sports',
            'walking' => 'Walking',
            'running' => 'Running',
            'cycling' => 'Cycling',
            'swimming' => 'Swimming',
            'yoga' => 'Yoga',
            'pilates' => 'Pilates',
            'other' => 'Other'
        ];
    }

    /**
     * Get weekly exercise data for charts
     */
    public function getWeeklyExerciseData($userId) {
        try {
            $data = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-{$i} days"));
                $dayName = date('D', strtotime($date));

                $stmt = $this->db->prepare("
                    SELECT SUM(duration_minutes) as minutes, SUM(calories_burned) as calories
                    FROM dnms_exercises
                    WHERE user_id = ? AND exercise_date = ?
                ");
                $stmt->execute([$userId, $date]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                $data[] = [
                    'day' => $dayName,
                    'date' => $date,
                    'minutes' => $result['minutes'] ?? 0,
                    'calories' => $result['calories'] ?? 0
                ];
            }

            return $data;

        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get exercise statistics
     */
    public function getExerciseStats($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT
                    exercise_type,
                    COUNT(*) as session_count,
                    SUM(duration_minutes) as total_minutes,
                    SUM(calories_burned) as total_calories,
                    AVG(calories_burned) as avg_calories_per_session
                FROM dnms_exercises
                WHERE user_id = ?
                GROUP BY exercise_type
                ORDER BY total_minutes DESC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            return [];
        }
    }
}
?>
