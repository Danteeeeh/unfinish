<?php
/**
 * Audit Model
 * Handles audit trail and logging
 */

class Audit {
    private $db;
    private $table = 'audit_logs';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Log an action
     */
    public function log($userId, $action, $entityType, $entityId, $details = null, $ipAddress = null) {
        $data = [
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'details' => $details ? json_encode($details) : null,
            'ip_address' => $ipAddress ?? $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert($this->table, $data);
    }

    /**
     * Get all audit logs
     */
    public function getAll($page = 1, $perPage = 50, $filters = []) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT a.*, u.username, u.full_name
                FROM {$this->table} a
                LEFT JOIN users u ON a.user_id = u.id";
        
        $where = [];
        $params = [];
        
        if (!empty($filters['user_id'])) {
            $where[] = "a.user_id = :user_id";
            $params['user_id'] = $filters['user_id'];
        }
        
        if (!empty($filters['action'])) {
            $where[] = "a.action = :action";
            $params['action'] = $filters['action'];
        }
        
        if (!empty($filters['entity_type'])) {
            $where[] = "a.entity_type = :entity_type";
            $params['entity_type'] = $filters['entity_type'];
        }
        
        if (!empty($filters['date_from'])) {
            $where[] = "a.created_at >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $where[] = "a.created_at <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $sql .= " ORDER BY a.created_at DESC 
                  LIMIT :limit OFFSET :offset";
        
        $params['limit'] = $perPage;
        $params['offset'] = $offset;
        
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get logs for specific entity
     */
    public function getByEntity($entityType, $entityId) {
        $sql = "SELECT a.*, u.username, u.full_name
                FROM {$this->table} a
                LEFT JOIN users u ON a.user_id = u.id
                WHERE a.entity_type = :entity_type AND a.entity_id = :entity_id
                ORDER BY a.created_at DESC";
        
        return $this->db->fetchAll($sql, [
            'entity_type' => $entityType,
            'entity_id' => $entityId
        ]);
    }

    /**
     * Get logs for specific user
     */
    public function getByUserId($userId, $limit = 100) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC 
                LIMIT :limit";
        
        return $this->db->fetchAll($sql, [
            'user_id' => $userId,
            'limit' => $limit
        ]);
    }

    /**
     * Delete old logs
     */
    public function deleteOldLogs($daysToKeep = 365) {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$daysToKeep} days"));
        
        return $this->db->delete(
            $this->table,
            'created_at < :cutoff_date',
            ['cutoff_date' => $cutoffDate]
        );
    }

    /**
     * Get statistics
     */
    public function getStatistics($dateFrom = null, $dateTo = null) {
        $sql = "SELECT 
                    COUNT(*) as total_actions,
                    COUNT(DISTINCT user_id) as unique_users,
                    COUNT(DISTINCT entity_type) as entity_types,
                    action,
                    COUNT(*) as action_count
                FROM {$this->table}";
        
        $where = [];
        $params = [];
        
        if ($dateFrom) {
            $where[] = "created_at >= :date_from";
            $params['date_from'] = $dateFrom;
        }
        
        if ($dateTo) {
            $where[] = "created_at <= :date_to";
            $params['date_to'] = $dateTo;
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $sql .= " GROUP BY action";
        
        return $this->db->fetchAll($sql, $params);
    }
}
