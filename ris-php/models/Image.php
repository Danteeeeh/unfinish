<?php
/**
 * Image Model
 * Handles DICOM image data operations
 */

class Image {
    private $db;
    private $table = 'images';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Get all images for a study
     */
    public function getByStudyId($studyId) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE study_id = :study_id 
                ORDER BY series_number, instance_number";
        
        return $this->db->fetchAll($sql, ['study_id' => $studyId]);
    }

    /**
     * Get image by ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }

    /**
     * Get image by SOP Instance UID
     */
    public function getBySOPInstanceUID($sopInstanceUID) {
        $sql = "SELECT * FROM {$this->table} WHERE sop_instance_uid = :sop_instance_uid";
        return $this->db->fetchOne($sql, ['sop_instance_uid' => $sopInstanceUID]);
    }

    /**
     * Create new image record
     */
    public function create($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update image
     */
    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return $this->db->update(
            $this->table,
            $data,
            'id = :id',
            ['id' => $id]
        );
    }

    /**
     * Delete image
     */
    public function delete($id) {
        return $this->db->delete($this->table, 'id = :id', ['id' => $id]);
    }

    /**
     * Delete all images for a study
     */
    public function deleteByStudyId($studyId) {
        return $this->db->delete($this->table, 'study_id = :study_id', ['study_id' => $studyId]);
    }

    /**
     * Get series list for a study
     */
    public function getSeriesList($studyId) {
        $sql = "SELECT DISTINCT 
                    series_number,
                    series_description,
                    series_instance_uid,
                    modality,
                    COUNT(*) as image_count
                FROM {$this->table}
                WHERE study_id = :study_id
                GROUP BY series_number, series_description, series_instance_uid, modality
                ORDER BY series_number";
        
        return $this->db->fetchAll($sql, ['study_id' => $studyId]);
    }

    /**
     * Get images by series
     */
    public function getBySeries($studyId, $seriesNumber) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE study_id = :study_id AND series_number = :series_number 
                ORDER BY instance_number";
        
        return $this->db->fetchAll($sql, [
            'study_id' => $studyId,
            'series_number' => $seriesNumber
        ]);
    }

    /**
     * Get image count for a study
     */
    public function getCountByStudyId($studyId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE study_id = :study_id";
        $result = $this->db->fetchOne($sql, ['study_id' => $studyId]);
        return $result['count'];
    }

    /**
     * Get total storage size for a study
     */
    public function getTotalSizeByStudyId($studyId) {
        $sql = "SELECT SUM(file_size) as total_size FROM {$this->table} WHERE study_id = :study_id";
        $result = $this->db->fetchOne($sql, ['study_id' => $studyId]);
        return $result['total_size'] ?? 0;
    }

    /**
     * Generate SOP Instance UID
     */
    public function generateSOPInstanceUID() {
        $root = '1.2.840.99999'; // Replace with your organization's OID
        $timestamp = time();
        $random = mt_rand(10000, 99999);
        
        return "{$root}.{$timestamp}.{$random}";
    }
}
