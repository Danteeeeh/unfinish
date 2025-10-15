<?php

class Modality {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $conn = $this->db->getConnection();
        $stmt = $conn->query("SELECT * FROM ris_equipment ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
