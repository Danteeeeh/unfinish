<?php
/**
 * User Class
 * Handles user operations
 */

class User {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function authenticate($username, $password) {
        $sql = "SELECT * FROM users WHERE username = ? AND is_active = 1";
        $user = $this->db->fetchOne($sql, [$username]);
        
        if ($user && password_verify($password, $user['password'])) {
            $this->updateLastLogin($user['id']);
            unset($user['password']);
            return $user;
        }
        
        return false;
    }
    
    public function create($data) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['created_at'] = date(DATETIME_FORMAT);
        return $this->db->insert('users', $data);
    }
    
    public function update($id, $data) {
        $data['updated_at'] = date(DATETIME_FORMAT);
        return $this->db->update('users', $data, 'id = :id', ['id' => $id]);
    }
    
    public function getById($id) {
        $sql = "SELECT id, username, email, role, full_name, phone, is_active, created_at FROM users WHERE id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function getAll() {
        $sql = "SELECT id, username, email, role, full_name, phone, is_active, created_at FROM users ORDER BY created_at DESC";
        return $this->db->fetchAll($sql);
    }
    
    public function delete($id) {
        return $this->db->delete('users', 'id = ?', [$id]);
    }
    
    private function updateLastLogin($id) {
        $data = ['last_login' => date(DATETIME_FORMAT)];
        $this->db->update('users', $data, 'id = :id', ['id' => $id]);
    }
    
    public function usernameExists($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE username = ?";
        $params = [$username];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->db->fetchOne($sql, $params);
        return $result['count'] > 0;
    }
}
