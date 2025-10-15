<?php
/**
 * Report Model
 * Handles radiology report data operations
 */

class Report {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $conn = $this->db->getConnection();
        $stmt = $conn->query("SELECT * FROM reports ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM reports WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("INSERT INTO reports (title, description, patient_id, study_id, status, content, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        return $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['patient_id'],
            $data['study_id'] ?? null,
            $data['status'] ?? 'draft',
            $data['content'] ?? null,
            $data['created_by'] ?? null
        ]);
    }

    public function update($id, $data) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("UPDATE reports SET title = ?, description = ?, patient_id = ?, study_id = ?, status = ?, content = ? WHERE id = ?");
        return $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['patient_id'],
            $data['study_id'] ?? null,
            $data['status'] ?? 'draft',
            $data['content'] ?? null,
            $id
        ]);
    }

    public function delete($id) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("DELETE FROM reports WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function finalize($id, $userId = null) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("UPDATE reports SET status = 'final', finalized_by = ?, finalized_at = NOW() WHERE id = ?");
        return $stmt->execute([$userId, $id]);
    }
}
