<?php
/**
 * LIS Test Model
 * Handles test definitions and available tests
 */

require_once __DIR__ . '/../config/database.php';

class LisTestModel {
    private $conn;
    private $table = 'lis_tests';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Get all active tests
     */
    public function getActiveTests() {
        $query = "SELECT * FROM " . $this->table . " WHERE status = 'active' ORDER BY test_name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get test by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id AND status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Get tests by category
     */
    public function getByCategory($category) {
        $query = "SELECT * FROM " . $this->table . " WHERE category = :category AND status = 'active' ORDER BY test_name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category', $category);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Search tests
     */
    public function search($keyword) {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE (test_name LIKE :keyword OR test_code LIKE :keyword)
                  AND status = 'active'
                  ORDER BY test_name";

        $stmt = $this->conn->prepare($query);
        $searchTerm = "%{$keyword}%";
        $stmt->bindParam(':keyword', $searchTerm);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get test by code
     */
    public function getByCode($code) {
        $query = "SELECT * FROM " . $this->table . " WHERE test_code = :code AND status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->execute();

        return $stmt->fetch();
    }
}
