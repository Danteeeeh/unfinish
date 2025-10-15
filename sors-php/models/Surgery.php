<?php
/**
 * Surgery Model
 * Handles surgery scheduling and management
 */

class Surgery {
    private $db;
    private $table = 'sors_surgeries';

    public function __construct() {
        $this->db = SORSDatabase::getInstance();
    }

    public function getAll($filters = []) {
        $conn = $this->db->getConnection();
        $sql = "SELECT s.*, 
                       p.full_name as patient_name, p.mrn,
                       r.room_number as room_name,
                       u.full_name as surgeon_name
                FROM {$this->table} s
                LEFT JOIN patients p ON s.patient_id = p.id
                LEFT JOIN sors_operating_rooms r ON s.operating_room_id = r.id
                LEFT JOIN staff u ON s.surgeon_id = u.id";
        
        $where = [];
        $params = [];
        
        if (!empty($filters['status'])) {
            $where[] = "s.status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['date'])) {
            $where[] = "s.scheduled_date = :date";
            $params['date'] = $filters['date'];
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $sql .= " ORDER BY s.scheduled_date DESC, s.start_time DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT s.*, 
                       p.full_name as patient_name, p.mrn, p.age, p.gender,
                       r.room_number as room_name, r.room_number,
                       u.full_name as surgeon_name
                FROM {$this->table} s
                LEFT JOIN patients p ON s.patient_id = p.id
                LEFT JOIN sors_operating_rooms r ON s.operating_room_id = r.id
                LEFT JOIN staff u ON s.surgeon_id = u.id
                WHERE s.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $conn = $this->db->getConnection();
        $data['created_at'] = date('Y-m-d H:i:s');
        
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $conn->prepare($sql);
        $stmt->execute($data);
        
        return $conn->lastInsertId();
    }

    public function update($id, $data) {
        $conn = $this->db->getConnection();
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $setParts = [];
        foreach (array_keys($data) as $key) {
            $setParts[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $setParts);
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = :id";
        $params = array_merge($data, ['id' => $id]);
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->rowCount();
    }

    public function delete($id) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }

    public function getScheduleByDate($date) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT s.*, 
                       p.full_name as patient_name,
                       r.room_number as room_name, r.room_number,
                       u.full_name as surgeon_name
                FROM {$this->table} s
                LEFT JOIN patients p ON s.patient_id = p.id
                LEFT JOIN sors_operating_rooms r ON s.operating_room_id = r.id
                LEFT JOIN staff u ON s.surgeon_id = u.id
                WHERE s.scheduled_date = ?
                ORDER BY s.scheduled_date ASC, s.actual_start_time ASC");
        $stmt->execute([$date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecent($limit = 5) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT s.*, 
                       p.full_name as patient_name, p.patient_id,
                       r.room_number as room_name,
                       u.full_name as surgeon_name
                FROM {$this->table} s
                LEFT JOIN patients p ON s.patient_id = p.id
                LEFT JOIN sors_operating_rooms r ON s.operating_room_id = r.id
                LEFT JOIN staff u ON s.surgeon_id = u.id
                ORDER BY s.scheduled_date DESC
                LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkRoomAvailability($roomId, $date, $startTime, $endTime, $excludeId = null) {
        $conn = $this->db->getConnection();
        $sql = "SELECT COUNT(*) as count FROM {$this->table}
                WHERE operating_room_id = :room_id
                AND scheduled_date = :date
                AND status NOT IN ('cancelled', 'completed')
                AND (
                    (start_time >= :start_time AND start_time < :end_time)
                    OR (end_time > :start_time AND end_time <= :end_time)
                    OR (start_time <= :start_time AND end_time >= :end_time)
                )";
        
        $params = [
            'room_id' => $roomId,
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime
        ];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] == 0;
    }
}
