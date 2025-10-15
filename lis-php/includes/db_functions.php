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
