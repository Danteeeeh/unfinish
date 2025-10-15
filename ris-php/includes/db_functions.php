<?php
/**
 * Database Utility Functions
 * Helper functions for database operations
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Execute a query and return results
 */
function executeQuery($query, $params = []) {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        
        return $stmt;
    } catch (PDOException $e) {
        error_log("Query Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get single record
 */
function getSingleRecord($query, $params = []) {
    $stmt = executeQuery($query, $params);
    
    if ($stmt) {
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    return false;
}

/**
 * Get multiple records
 */
function getMultipleRecords($query, $params = []) {
    $stmt = executeQuery($query, $params);
    
    if ($stmt) {
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    return [];
}

/**
 * Insert record
 */
function insertRecord($table, $data) {
    $columns = implode(', ', array_keys($data));
    $placeholders = ':' . implode(', :', array_keys($data));
    
    $query = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
    
    $stmt = executeQuery($query, $data);
    
    return $stmt !== false;
}

/**
 * Update record
 */
function updateRecord($table, $data, $where, $whereParams = []) {
    $setParts = [];
    foreach (array_keys($data) as $key) {
        $setParts[] = "{$key} = :{$key}";
    }
    $setClause = implode(', ', $setParts);
    
    $query = "UPDATE {$table} SET {$setClause} WHERE {$where}";
    
    $params = array_merge($data, $whereParams);
    $stmt = executeQuery($query, $params);
    
    return $stmt !== false;
}

/**
 * Delete record
 */
function deleteRecord($table, $where, $whereParams = []) {
    $query = "DELETE FROM {$table} WHERE {$where}";
    
    $stmt = executeQuery($query, $whereParams);
    
    return $stmt !== false;
}

/**
 * Get last insert ID
 */
function getLastInsertId() {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        return $conn->lastInsertId();
    } catch (PDOException $e) {
        error_log("Error getting last insert ID: " . $e->getMessage());
        return false;
    }
}

/**
 * Begin transaction
 */
function beginTransaction() {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        return $conn->beginTransaction();
    } catch (PDOException $e) {
        error_log("Error beginning transaction: " . $e->getMessage());
        return false;
    }
}

/**
 * Commit transaction
 */
function commitTransaction() {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        return $conn->commit();
    } catch (PDOException $e) {
        error_log("Error committing transaction: " . $e->getMessage());
        return false;
    }
}

/**
 * Rollback transaction
 */
function rollbackTransaction() {
    try {
        $database = new Database();
        $conn = $database->getConnection();

        return $conn->rollBack();
    } catch (PDOException $e) {
        error_log("Error rolling back transaction: " . $e->getMessage());
        return false;
    }
}

/**
 * Get total patient count
 */
function getPatientCount() {
    try {
        $query = "SELECT COUNT(*) as count FROM patients";
        $result = getSingleRecord($query);

        return $result ? (int)$result['count'] : 0;
    } catch (Exception $e) {
        error_log("Error getting patient count: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get recent patients
 */
function getRecentPatients($limit = 5) {
    try {
        $query = "SELECT id, name, phone, registration_date FROM patients ORDER BY registration_date DESC LIMIT :limit";
        $stmt = executeQuery($query, ['limit' => $limit]);

        if ($stmt) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return [];
    } catch (Exception $e) {
        error_log("Error getting recent patients: " . $e->getMessage());
        return [];
    }
}

/**
 * Get today's patient registrations
 */
function getTodayPatientRegistrations() {
    try {
        $today = date('Y-m-d');
        $query = "SELECT COUNT(*) as count FROM patients WHERE DATE(registration_date) = :today";
        $result = getSingleRecord($query, ['today' => $today]);

        return $result ? (int)$result['count'] : 0;
    } catch (Exception $e) {
        error_log("Error getting today's registrations: " . $e->getMessage());
        return 0;
    }
}

/**
 * Search patients by query
 */
function searchPatients($query, $limit = 20) {
    try {
        $searchTerm = "%{$query}%";
        $sql = "SELECT id, name, phone, email, address, registration_date
                FROM patients
                WHERE name LIKE :searchTerm
                   OR phone LIKE :searchTerm
                   OR email LIKE :searchTerm
                   OR id LIKE :searchTerm
                ORDER BY name ASC LIMIT :limit";

        $stmt = executeQuery($sql, [
            'searchTerm' => $searchTerm,
            'limit' => $limit
        ]);

        if ($stmt) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return [];
    } catch (Exception $e) {
        error_log("Error searching patients: " . $e->getMessage());
        return [];
    }
}

/**
 * Get patient by ID
 */
function getPatientById($id) {
    try {
        $query = "SELECT * FROM patients WHERE id = :id";
        return getSingleRecord($query, ['id' => $id]);
    } catch (Exception $e) {
        error_log("Error getting patient by ID: " . $e->getMessage());
        return false;
    }
}

/**
 * Get study count for patient
 */
function getPatientStudyCount($patientId) {
    try {
        $query = "SELECT COUNT(*) as count FROM ris_exams WHERE patient_id = :patientId";
        $result = getSingleRecord($query, ['patientId' => $patientId]);

        return $result ? (int)$result['count'] : 0;
    } catch (Exception $e) {
        error_log("Error getting patient study count: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get patient's recent studies
 */
function getPatientRecentStudies($patientId, $limit = 3) {
    try {
        $query = "SELECT id, study_type, study_date, status FROM ris_exams
                  WHERE patient_id = :patientId ORDER BY study_date DESC LIMIT :limit";
        $stmt = executeQuery($query, ['patientId' => $patientId, 'limit' => $limit]);

        if ($stmt) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return [];
    } catch (Exception $e) {
        error_log("Error getting patient recent studies: " . $e->getMessage());
        return [];
    }
}
