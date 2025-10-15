<?php
/**
 * Sample Model
 * Handles sample tracking and storage operations
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';

class SampleModel {
    private $conn;
    private $table = 'lis_results'; // Changed from 'samples' to 'lis_results'

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Get all samples with pagination
     */
    public function getAll($page = 1, $limit = RECORDS_PER_PAGE) {
        $offset = ($page - 1) * $limit;

        $query = "SELECT s.*, p.full_name, p.patient_id
                  FROM " . $this->table . " s
                  LEFT JOIN patients p ON s.patient_id = p.id
                  ORDER BY s.collection_date DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get sample by ID
     */
    public function getById($id) {
        $query = "SELECT s.*, p.full_name, p.patient_id
                  FROM " . $this->table . " s
                  LEFT JOIN patients p ON s.patient_id = p.id
                  WHERE s.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Get samples by patient ID
     */
    public function getByPatientId($patientId) {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE patient_id = :patient_id
                  ORDER BY collection_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patientId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get samples by status
     */
    public function getByStatus($status) {
        $query = "SELECT s.*, p.full_name, p.patient_id
                  FROM " . $this->table . " s
                  LEFT JOIN patients p ON s.patient_id = p.id
                  WHERE s.status = :status
                  ORDER BY s.collection_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Search samples
     */
    public function search($keyword) {
        $query = "SELECT s.*, p.full_name, p.patient_id
                  FROM " . $this->table . " s
                  LEFT JOIN patients p ON s.patient_id = p.id
                  WHERE s.order_number LIKE :keyword
                  OR s.status LIKE :keyword
                  OR p.full_name LIKE :keyword
                  OR p.patient_id LIKE :keyword
                  ORDER BY s.collection_date DESC";

        $stmt = $this->conn->prepare($query);
        $searchTerm = "%{$keyword}%";
        $stmt->bindParam(':keyword', $searchTerm);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Create new sample
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . "
                  (order_number, patient_id, status, collection_date,
                   notes, created_at)
                  VALUES
                  (:order_number, :patient_id, :status, :collection_date,
                   :notes, NOW())";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':order_number', $data['order_number']);
        $stmt->bindParam(':patient_id', $data['patient_id'], PDO::PARAM_INT);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':collection_date', $data['collection_date']);
        $stmt->bindParam(':notes', $data['notes']);

        return $stmt->execute();
    }

    /**
     * Update sample status
     */
    public function updateStatus($id, $status, $notes = '') {
        $query = "UPDATE " . $this->table . "
                  SET status = :status,
                      notes = :notes,
                      updated_at = NOW()
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':notes', $notes);

        return $stmt->execute();
    }

    /**
     * Update sample
     */
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . "
                  SET status = :status,
                      collection_date = :collection_date,
                      notes = :notes,
                      updated_at = NOW()
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':collection_date', $data['collection_date']);
        $stmt->bindParam(':notes', $data['notes']);

        return $stmt->execute();
    }

    /**
     * Delete sample
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
