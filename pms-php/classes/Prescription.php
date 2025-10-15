<?php
/**
 * PMS-PHP Prescription Class
 * Handles prescription data and operations
 */

class Prescription {
    private $db;

    public function __construct($db = null) {
        $this->db = $db ?: $this->getDBConnection();
    }

    private function getDBConnection() {
        // Use PMS database connection function
        return getPMSDBConnection();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO prescriptions (patient_id, doctor_name, prescription_date, medications, dosage_instructions, notes, status, refill_count, next_refill_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['patient_id'], $data['doctor_name'], $data['prescription_date'], $data['medications'],
            $data['dosage_instructions'], $data['notes'], $data['status'], $data['refill_count'], $data['next_refill_date']
        ]);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM prescriptions WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE prescriptions SET patient_id = ?, doctor_name = ?, prescription_date = ?, medications = ?, dosage_instructions = ?, notes = ?, status = ?, refill_count = ?, next_refill_date = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?
        ");
        return $stmt->execute([
            $data['patient_id'], $data['doctor_name'], $data['prescription_date'], $data['medications'],
            $data['dosage_instructions'], $data['notes'], $data['status'], $data['refill_count'], $data['next_refill_date'], $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM prescriptions WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getByPatientId($patient_id) {
        $stmt = $this->db->prepare("SELECT * FROM prescriptions WHERE patient_id = ? ORDER BY prescription_date DESC");
        $stmt->execute([$patient_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM prescriptions ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkDrugInteractions($medications) {
        // TODO: Implement drug interaction checking when pms_drug_interactions table is created
        return [];
    }
}
?>
