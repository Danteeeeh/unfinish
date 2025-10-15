<?php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/environment.php';

class Food {
    private $db;

    public function __construct() {
        try {
            $this->db = getDNMSDBConnection();
        } catch (Exception $e) {
            $this->db = null;
        }
    }

    public function getAll() {
        if (!$this->db) return [];

        try {
            return $this->db->query("SELECT * FROM dnms_foods")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function searchFoods($searchTerm = '', $category = '', $limit = 50, $page = 1) {
        if (!$this->db) return [];

        try {
            $offset = ($page - 1) * $limit;
            $searchTerm = trim($searchTerm);
            $category = trim($category);

            $whereConditions = [];
            $params = [];

            if (!empty($searchTerm)) {
                $whereConditions[] = "(name LIKE :searchTerm OR category LIKE :searchTerm)";
                $params[':searchTerm'] = "%{$searchTerm}%";
            }

            if (!empty($category)) {
                $whereConditions[] = "category = :category";
                $params[':category'] = $category;
            }

            $whereClause = empty($whereConditions) ? '' : 'WHERE ' . implode(' AND ', $whereConditions);

            $query = "SELECT * FROM dnms_foods {$whereClause} ORDER BY name LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function getFoods($limit = 50, $page = 1) {
        if (!$this->db) return [];

        try {
            $offset = ($page - 1) * $limit;
            $stmt = $this->db->prepare("SELECT * FROM dnms_foods ORDER BY name LIMIT :limit OFFSET :offset");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function getSearchCount($searchTerm = '', $category = '') {
        if (!$this->db) return 0;

        try {
            $searchTerm = trim($searchTerm);
            $category = trim($category);

            $whereConditions = [];
            $params = [];

            if (!empty($searchTerm)) {
                $whereConditions[] = "(name LIKE :searchTerm OR category LIKE :searchTerm)";
                $params[':searchTerm'] = "%{$searchTerm}%";
            }

            if (!empty($category)) {
                $whereConditions[] = "category = :category";
                $params[':category'] = $category;
            }

            $whereClause = empty($whereConditions) ? '' : 'WHERE ' . implode(' AND ', $whereConditions);

            $query = "SELECT COUNT(*) as count FROM dnms_foods {$whereClause}";

            $stmt = $this->db->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();

            return $stmt->fetch()['count'];
        } catch (Exception $e) {
            return 0;
        }
    }

    public function getTotalCount() {
        if (!$this->db) return 0;

        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM dnms_foods");
            return $stmt->fetch()['count'];
        } catch (Exception $e) {
            return 0;
        }
    }

    public function getFoodById($id) {
        if (!$this->db) return null;

        try {
            // Ensure ID is integer
            $id = (int)$id;
            
            $stmt = $this->db->prepare("SELECT * FROM dnms_foods WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }

    public function getFoodsByCategory($category, $limit = 20) {
        if (!$this->db) return [];

        try {
            $stmt = $this->db->prepare("SELECT * FROM dnms_foods WHERE category = :category ORDER BY name LIMIT :limit");
            $stmt->bindParam(':category', $category, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public function getCategories() {
        if (!$this->db) return [];

        try {
            $stmt = $this->db->query("SELECT DISTINCT category FROM dnms_foods ORDER BY category");
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            return [];
        }
    }

    public function addFood($foodData) {
        if (!$this->db) return false;

        try {
            $stmt = $this->db->prepare("
                INSERT INTO dnms_foods (name, brand, category, serving_size, serving_unit, calories, protein, carbohydrates, fat, fiber, sugar, sodium, potassium, calcium, iron, vitamins, barcode, verified, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            return $stmt->execute([
                $foodData['name'],
                $foodData['brand'] ?? null,
                $foodData['category'] ?? null,
                $foodData['serving_size'],
                $foodData['serving_unit'] ?? 'g',
                $foodData['calories'],
                $foodData['protein'] ?? 0,
                $foodData['carbohydrates'] ?? 0,
                $foodData['fat'] ?? 0,
                $foodData['fiber'] ?? 0,
                $foodData['sugar'] ?? 0,
                $foodData['sodium'] ?? 0,
                $foodData['potassium'] ?? 0,
                $foodData['calcium'] ?? 0,
                $foodData['iron'] ?? 0,
                $foodData['vitamins'] ?? null,
                $foodData['barcode'] ?? null,
                $foodData['verified'] ?? 0,
                $foodData['created_by'] ?? null
            ]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateFood($id, $foodData) {
        if (!$this->db) return false;

        try {
            $fields = [];
            $values = [];

            foreach ($foodData as $field => $value) {
                if ($field !== 'id') {
                    $fields[] = "{$field} = ?";
                    $values[] = $value;
                }
            }

            $values[] = $id;

            $query = "UPDATE dnms_foods SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $this->db->prepare($query);

            return $stmt->execute($values);
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteFood($id) {
        if (!$this->db) return false;

        try {
            $stmt = $this->db->prepare("DELETE FROM dnms_foods WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            return false;
        }
    }
}
