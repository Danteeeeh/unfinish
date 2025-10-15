<?php
/**
 * Test Model
 * Handles test processing logic and operations
 * Note: This model works with lis_results (test results) not lis_tests (test definitions)
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';

class TestModel {
    private $conn;
    private $table = 'lis_results'; // Changed from 'lis_tests' to 'lis_results'

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Get all test results with pagination
     */
    public function getAll($page = 1, $limit = RECORDS_PER_PAGE) {
        $offset = ($page - 1) * $limit;

        $query = "SELECT r.*, t.test_name, t.category, p.full_name, p.patient_id
                  FROM " . $this->table . " r
                  LEFT JOIN lis_tests t ON r.test_id = t.id
                  LEFT JOIN patients p ON r.patient_id = p.id
                  ORDER BY r.collection_date DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get test result by ID
     */
    public function getById($id) {
        $query = "SELECT r.*, t.test_name, t.category, t.normal_range, t.units,
                         p.full_name, p.patient_id, p.date_of_birth
                  FROM " . $this->table . " r
                  LEFT JOIN lis_tests t ON r.test_id = t.id
                  LEFT JOIN patients p ON r.patient_id = p.id
                  WHERE r.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Get test results by patient ID
     */
    public function getByPatientId($patientId) {
        $query = "SELECT r.*, t.test_name, t.category
                  FROM " . $this->table . " r
                  LEFT JOIN lis_tests t ON r.test_id = t.id
                  WHERE r.patient_id = :patient_id
                  ORDER BY r.collection_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patientId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get test results by status
     */
    public function getByStatus($status) {
        $query = "SELECT r.*, t.test_name, t.category, p.full_name, p.patient_id
                  FROM " . $this->table . " r
                  LEFT JOIN lis_tests t ON r.test_id = t.id
                  LEFT JOIN patients p ON r.patient_id = p.id
                  WHERE r.status = :status
                  ORDER BY r.collection_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get pending test results
     */
    public function getPending() {
        return $this->getByStatus('pending');
    }

    /**
     * Create new test result
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . "
                  (patient_id, test_id, order_number, collection_date, status, notes, created_at)
                  VALUES
                  (:patient_id, :test_id, :order_number, :collection_date, :status, :notes, NOW())";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':patient_id', $data['patient_id'], PDO::PARAM_INT);
        $stmt->bindParam(':test_id', $data['test_id'], PDO::PARAM_INT);
        $stmt->bindParam(':order_number', $data['order_number']);
        $stmt->bindParam(':collection_date', $data['collection_date']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':notes', $data['notes']);

        return $stmt->execute();
    }

    /**
     * Update test result
     */
    public function updateResults($id, $results, $performedBy, $status = 'completed') {
        $query = "UPDATE " . $this->table . "
                  SET result_value = :results,
                      result_text = :results,
                      performed_by = :performed_by,
                      result_date = NOW(),
                      status = :status,
                      updated_at = NOW()
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':results', $results);
        $stmt->bindParam(':performed_by', $performedBy);
        $stmt->bindParam(':status', $status);

        return $stmt->execute();
    }

    /**
     * Verify test result
     */
    public function verify($id, $verifiedBy, $verificationNotes = '') {
        $query = "UPDATE " . $this->table . "
                  SET verified_by = :verified_by,
                      verification_date = NOW(),
                      verification_notes = :verification_notes,
                      status = :status,
                      updated_at = NOW()
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':verified_by', $verifiedBy);
        $stmt->bindParam(':verification_notes', $verificationNotes);
        $status = 'verified';
        $stmt->bindParam(':status', $status);

        return $stmt->execute();
    }

    /**
     * Update test result status
     */
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table . "
                  SET status = :status,
                      updated_at = NOW()
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status);

        return $stmt->execute();
    }

    /**
     * Delete test result
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Generate unique order number
     */
    public function generateOrderNumber() {
        $prefix = 'LAB';
        $year = date('Y');
        $month = date('m');

        $query = "SELECT order_number FROM " . $this->table . "
                  WHERE order_number LIKE :pattern
                  ORDER BY order_number DESC LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $pattern = $prefix . $year . $month . '%';
        $stmt->bindParam(':pattern', $pattern);
        $stmt->execute();

        $result = $stmt->fetch();

        if ($result) {
            $lastNumber = intval(substr($result['order_number'], -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $year . $month . $newNumber;
    }
}
