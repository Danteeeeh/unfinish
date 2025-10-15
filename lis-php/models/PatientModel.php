<?php
/**
 * Patient Model
 * Handles patient data operations
 */

require_once __DIR__ . '/../config/database.php';

class PatientModel {
    private $conn;
    private $table = 'patients';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Get all patients with pagination
     */
    public function getAll($page = 1, $limit = RECORDS_PER_PAGE) {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT * FROM " . $this->table . " 
                  ORDER BY created_at DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Get patient by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    /**
     * Search patients
     */
    public function search($keyword) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE full_name LIKE :keyword 
                  OR patient_id LIKE :keyword 
                  OR email LIKE :keyword 
                  OR phone LIKE :keyword
                  ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $searchTerm = "%{$keyword}%";
        $stmt->bindParam(':keyword', $searchTerm);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Create new patient
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (patient_id, full_name, date_of_birth, gender, 
                   email, phone, address, emergency_contact, emergency_phone, created_at) 
                  VALUES 
                  (:patient_id, :full_name, :date_of_birth, :gender, 
                   :email, :phone, :address, :emergency_contact, :emergency_phone, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':patient_id', $data['patient_id']);
        $stmt->bindParam(':full_name', $data['full_name']);
        $stmt->bindParam(':date_of_birth', $data['date_of_birth']);
        $stmt->bindParam(':gender', $data['gender']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':emergency_contact', $data['emergency_contact']);
        $stmt->bindParam(':emergency_phone', $data['emergency_phone']);
        
        return $stmt->execute();
    }

    /**
     * Update patient
     */
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET full_name = :full_name, 
                      date_of_birth = :date_of_birth, 
                      gender = :gender, 
                      email = :email, 
                      phone = :phone, 
                      address = :address, 
                      emergency_contact = :emergency_contact, 
                      emergency_phone = :emergency_phone,
                      updated_at = NOW()
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':full_name', $data['full_name']);
        $stmt->bindParam(':date_of_birth', $data['date_of_birth']);
        $stmt->bindParam(':gender', $data['gender']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':emergency_contact', $data['emergency_contact']);
        $stmt->bindParam(':emergency_phone', $data['emergency_phone']);
        
        return $stmt->execute();
    }

    /**
     * Delete patient
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Get total count
     */
    public function getTotalCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result['total'];
    }

    /**
     * Generate unique patient ID
     */
    public function generatePatientId() {
        $prefix = 'PAT';
        $year = date('Y');
        
        $query = "SELECT patient_id FROM " . $this->table . " 
                  WHERE patient_id LIKE :pattern 
                  ORDER BY patient_id DESC LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $pattern = $prefix . $year . '%';
        $stmt->bindParam(':pattern', $pattern);
        $stmt->execute();
        
        $result = $stmt->fetch();
        
        if ($result) {
            $lastNumber = intval(substr($result['patient_id'], -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $year . $newNumber;
    }
}
