<?php
/**
 * Study Model
 * Handles radiology study data operations
 */

class Study {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $conn = $this->db->getConnection();
        $stmt = $conn->query("SELECT * FROM ris_exams ORDER BY exam_date DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM ris_exams WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("INSERT INTO ris_exams (patient_id, exam_type, exam_date, modality, status, urgency, notes, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        return $stmt->execute([
            $data['patient_id'],
            $data['exam_type'],
            $data['exam_date'],
            $data['modality'] ?? null,
            $data['status'] ?? 'scheduled',
            $data['urgency'] ?? 'routine',
            $data['notes'] ?? null
        ]);
    }

    public function update($id, $data) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("UPDATE ris_exams SET patient_id = ?, exam_type = ?, exam_date = ?, modality = ?, status = ?, urgency = ?, notes = ? WHERE id = ?");
        return $stmt->execute([
            $data['patient_id'],
            $data['exam_type'],
            $data['exam_date'],
            $data['modality'] ?? null,
            $data['status'] ?? 'scheduled',
            $data['urgency'] ?? 'routine',
            $data['notes'] ?? null,
            $id
        ]);
    }

    public function delete($id) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("DELETE FROM ris_exams WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getRecent($limit = 5) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("
            SELECT
                re.*,
                p.full_name as patient_name,
                p.patient_id as patient_code
            FROM ris_exams re
            LEFT JOIN patients p ON re.patient_id = p.id
            ORDER BY re.exam_date DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Transform the data to match expected format
        $studies = [];
        foreach ($results as $result) {
            $studies[] = [
                'patient_name' => $result['patient_name'] ?? 'Unknown Patient',
                'patient_code' => $result['patient_code'] ?? 'N/A',
                'study_type' => ucfirst($result['exam_type'] ?? 'General'),
                'study_date' => $result['exam_date'] ?? 'No Date',
                'exam_id' => $result['exam_id'] ?? 'N/A',
                'status' => $result['status'] ?? 'unknown'
            ];
        }

        return $studies;
    }

    public function getWorklist() {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("
            SELECT
                re.*,
                p.full_name as patient_name,
                p.patient_id as patient_code
            FROM ris_exams re
            LEFT JOIN patients p ON re.patient_id = p.id
            WHERE re.status IN ('scheduled', 'in_progress')
            ORDER BY re.exam_date ASC
        ");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Transform the data to match expected format
        $studies = [];
        foreach ($results as $result) {
            $studies[] = [
                'patient_name' => $result['patient_name'] ?? 'Unknown Patient',
                'patient_code' => $result['patient_code'] ?? 'N/A',
                'study_type' => ucfirst($result['exam_type'] ?? 'General'),
                'study_date' => $result['exam_date'] ?? 'No Date',
                'exam_id' => $result['exam_id'] ?? 'N/A',
                'status' => $result['status'] ?? 'unknown',
                'modality' => $result['modality'] ?? 'N/A',
                'urgency' => $result['urgency'] ?? 'routine'
            ];
        }

        return $studies;
    }
}
