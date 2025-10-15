<?php
/**
 * Patient Model
 * Handles patient data operations
 */

class Patient {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $conn = $this->db->getConnection();
        $stmt = $conn->query("SELECT * FROM patients ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("INSERT INTO patients (patient_id, full_name, date_of_birth, gender, phone, email, address, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        return $stmt->execute([
            $data['patient_id'],
            $data['full_name'],
            $data['date_of_birth'],
            $data['gender'],
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['status'] ?? 'active'
        ]);
    }

    public function update($id, $data) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("UPDATE patients SET patient_id = ?, full_name = ?, date_of_birth = ?, gender = ?, phone = ?, email = ?, address = ?, status = ? WHERE id = ?");
        return $stmt->execute([
            $data['patient_id'] ?? null,
            $data['full_name'],
            $data['date_of_birth'],
            $data['gender'],
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['status'] ?? 'active',
            $id
        ]);
    }

    public function delete($id) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("DELETE FROM patients WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function search($query) {
        $conn = $this->db->getConnection();
        $searchTerm = "%{$query}%";
        $stmt = $conn->prepare("SELECT * FROM patients WHERE full_name LIKE :searchTerm OR phone LIKE :searchTerm OR email LIKE :searchTerm ORDER BY full_name");
        $stmt->bindParam(':searchTerm', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
