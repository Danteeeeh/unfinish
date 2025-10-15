<?php
/**
 * User Model
 * Handles user authentication and management
 */

class User {
    private $db;
    private $table = 'users';

    public function __construct() {
        $this->db = SORSDatabase::getInstance();
    }

    public function authenticate($username, $password) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ? AND status = 'active'");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $this->updateLastLogin($user['id']);
            unset($user['password']);
            return $user;
        }
        
        return false;
    }

    public function create($data) {
        $conn = $this->db->getConnection();
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
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

    public function getById($id) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT id, user_id, email, role, full_name, phone, status, created_at FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $conn = $this->db->getConnection();
        $stmt = $conn->query("SELECT id, user_id, email, role, full_name, phone, status, created_at FROM {$this->table} ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }

    private function updateLastLogin($id) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("UPDATE {$this->table} SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$id]);
    }
}
