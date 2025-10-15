<?php
/**
 * User Model
 * Handles user authentication and management
 */

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $conn = $this->db->getConnection();
        $stmt = $conn->query("SELECT id, user_id, email, full_name, role, created_at FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT id, user_id, email, full_name, role, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("INSERT INTO users (user_id, email, full_name, role, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        return $stmt->execute([
            $data['user_id'],
            $data['email'],
            $data['full_name'],
            $data['role'] ?? 'user',
            $data['status'] ?? 'active'
        ]);
    }

    public function update($id, $data) {
        $conn = $this->db->getConnection();
        $updateFields = [];
        $params = [];

        if (isset($data['user_id'])) {
            $updateFields[] = 'user_id = ?';
            $params[] = $data['user_id'];
        }
        if (isset($data['email'])) {
            $updateFields[] = 'email = ?';
            $params[] = $data['email'];
        }
        if (isset($data['full_name'])) {
            $updateFields[] = 'full_name = ?';
            $params[] = $data['full_name'];
        }
        if (isset($data['role'])) {
            $updateFields[] = 'role = ?';
            $params[] = $data['role'];
        }
        if (isset($data['status'])) {
            $updateFields[] = 'status = ?';
            $params[] = $data['status'];
        }

        if (!empty($updateFields)) {
            $params[] = $id;
            $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $stmt = $conn->prepare($sql);
            return $stmt->execute($params);
        }

        return false;
    }

    public function delete($id) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function findByUserId($userId) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByEmail($email) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
