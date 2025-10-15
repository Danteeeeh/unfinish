<?php
/**
 * Medicine Class
 * Handles medicine operations
 */

class Medicine {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function create($data) {
        $data['created_at'] = date(DATETIME_FORMAT);
        return $this->db->insert('medicines', $data);
    }
    
    public function update($id, $data) {
        $data['updated_at'] = date(DATETIME_FORMAT);
        return $this->db->update('medicines', $data, 'id = :id', ['id' => $id]);
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM medicines WHERE id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function getAll($page = 1, $perPage = RECORDS_PER_PAGE) {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM medicines ORDER BY name ASC LIMIT ? OFFSET ?";
        return $this->db->fetchAll($sql, [$perPage, $offset]);
    }
    
    public function search($keyword) {
        $sql = "SELECT * FROM medicines 
                WHERE name LIKE ? OR generic_name LIKE ? OR barcode LIKE ? 
                ORDER BY name ASC";
        $searchTerm = "%{$keyword}%";
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm]);
    }
    
    public function delete($id) {
        return $this->db->delete('medicines', 'id = ?', [$id]);
    }
    
    public function getTotalCount() {
        $sql = "SELECT COUNT(*) as total FROM medicines";
        $result = $this->db->fetchOne($sql);
        return $result['total'];
    }
    
    public function getLowStock() {
        $sql = "SELECT * FROM medicines WHERE quantity <= ? ORDER BY quantity ASC";
        return $this->db->fetchAll($sql, [LOW_STOCK_THRESHOLD]);
    }
    
    public function getExpiringSoon() {
        $alertDate = date('Y-m-d', strtotime('+' . EXPIRY_ALERT_DAYS . ' days'));
        $sql = "SELECT * FROM medicines WHERE expiry_date <= ? AND expiry_date >= CURDATE() ORDER BY expiry_date ASC";
        return $this->db->fetchAll($sql, [$alertDate]);
    }
    
    public function getExpired() {
        $sql = "SELECT * FROM medicines WHERE expiry_date < CURDATE() ORDER BY expiry_date DESC";
        return $this->db->fetchAll($sql);
    }
    
    public function updateStock($id, $quantity, $operation = 'add') {
        $medicine = $this->getById($id);
        if (!$medicine) return false;
        
        $newQuantity = $operation === 'add' 
            ? $medicine['quantity'] + $quantity 
            : $medicine['quantity'] - $quantity;
        
        $data = ['quantity' => max(0, $newQuantity), 'updated_at' => date(DATETIME_FORMAT)];
        return $this->db->update('medicines', $data, 'id = :id', ['id' => $id]);
    }
}
