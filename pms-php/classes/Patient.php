<?php
/**
 * PMS-PHP Patient Class
 * Handles patient data and operations
 */

class Patient {
    private $db;
    private $id;
    private $first_name;
    private $last_name;
    private $date_of_birth;
    private $gender;
    private $phone;
    private $email;
    private $address;
    private $medical_history;
    private $allergies;

    public function __construct($db = null) {
        $this->db = $db ?: $this->getDBConnection();
    }

    private function getDBConnection() {
        // Use PMS database connection function
        return getPMSDBConnection();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO patients (first_name, last_name, date_of_birth, gender, phone, email, address, medical_history, allergies)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['first_name'], $data['last_name'], $data['date_of_birth'], $data['gender'],
            $data['phone'], $data['email'], $data['address'], $data['medical_history'], $data['allergies']
        ]);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM patients WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE patients SET first_name = ?, last_name = ?, date_of_birth = ?, gender = ?, phone = ?, email = ?, address = ?, medical_history = ?, allergies = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?
        ");
        return $stmt->execute([
            $data['first_name'], $data['last_name'], $data['date_of_birth'], $data['gender'],
            $data['phone'], $data['email'], $data['address'], $data['medical_history'], $data['allergies'], $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM patients WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM patients ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
