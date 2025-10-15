<?php

class Staff {
    private $db;

    public function __construct() {
        $this->db = SORSDatabase::getInstance();
    }

    public function getAll() {
        $conn = $this->db->getConnection();
        $stmt = $conn->query("SELECT * FROM staff WHERE status = 'active'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
