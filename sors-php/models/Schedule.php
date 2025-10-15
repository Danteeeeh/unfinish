<?php

class Schedule {
    private $db;

    public function __construct() {
        $this->db = SORSDatabase::getInstance();
    }

    public function getSchedule($date) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM sors_surgeries WHERE DATE(scheduled_date) = ? ORDER BY scheduled_date");
        $stmt->execute([$date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
