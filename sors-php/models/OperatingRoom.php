<?php

class OperatingRoom {
    private $db;

    public function __construct() {
        $this->db = SORSDatabase::getInstance();
    }

    public function getStatus() {
        $conn = $this->db->getConnection();
        $stmt = $conn->query("SELECT * FROM sors_operating_rooms");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
