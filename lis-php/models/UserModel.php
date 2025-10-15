<?php
/**
 * User Model
 * Handles authentication and user permissions
 */

require_once __DIR__ . '/../config/database.php';

class UserModel {
    private $conn;
    private $table = 'users';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Get all users
     */
    public function getAll() {
        $query = "SELECT id, username, email, role, full_name, is_active, created_at 
                  FROM " . $this->table . " 
                  ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Get user by ID
     */
    public function getById($id) {
        $query = "SELECT id, username, email, role, full_name, is_active, created_at 
                  FROM " . $this->table . " 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    /**
     * Get user by username
     */
    public function getByUsername($username) {
        $query = "SELECT * FROM " . $this->table . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    /**
     * Authenticate user
     */
    public function authenticate($username, $password) {
        $user = $this->getByUsername($username);
        
        if ($user && password_verify($password, $user['password'])) {
            if ($user['is_active']) {
                // Update last login
                $this->updateLastLogin($user['id']);
                
                // Remove password from returned data
                unset($user['password']);
                return $user;
            }
        }
        
        return false;
    }

    /**
     * Create new user
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (username, password, email, role, full_name, is_active, created_at) 
                  VALUES 
                  (:username, :password, :email, :role, :full_name, :is_active, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':role', $data['role']);
        $stmt->bindParam(':full_name', $data['full_name']);
        $stmt->bindParam(':is_active', $data['is_active'], PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Update user
     */
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET email = :email, 
                      role = :role, 
                      full_name = :full_name, 
                      is_active = :is_active,
                      updated_at = NOW()
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':role', $data['role']);
        $stmt->bindParam(':full_name', $data['full_name']);
        $stmt->bindParam(':is_active', $data['is_active'], PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Update password
     */
    public function updatePassword($id, $newPassword) {
        $query = "UPDATE " . $this->table . " 
                  SET password = :password,
                      updated_at = NOW()
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':password', $hashedPassword);
        
        return $stmt->execute();
    }

    /**
     * Update last login timestamp
     */
    private function updateLastLogin($id) {
        $query = "UPDATE " . $this->table . " 
                  SET last_login = NOW() 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Delete user
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Check if username exists
     */
    public function usernameExists($username, $excludeId = null) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                  WHERE username = :username";
        
        if ($excludeId) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        
        if ($excludeId) {
            $stmt->bindParam(':exclude_id', $excludeId, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result['count'] > 0;
    }
}
