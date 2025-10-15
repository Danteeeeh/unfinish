<?php
/**
 * Shopping List Management Class
 * Handles shopping list creation, management, and tracking
 */

class ShoppingList {
    private $db;

    public function __construct() {
        $this->db = getDNMSDBConnection();
    }

    /**
     * Create a new shopping list
     */
    public function createShoppingList($userId, $data) {
        try {
            $this->db->beginTransaction();

            // Insert shopping list
            $stmt = $this->db->prepare("
                INSERT INTO dnms_shopping_lists (user_id, name, description, created_at, updated_at)
                VALUES (?, ?, ?, NOW(), NOW())
            ");
            $stmt->execute([
                $userId,
                $data['name'],
                $data['description'] ?? ''
            ]);

            $listId = $this->db->lastInsertId();

            // Insert items
            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    $stmt = $this->db->prepare("
                        INSERT INTO dnms_shopping_list_items (list_id, food_id, quantity, unit, notes, completed)
                        VALUES (?, ?, ?, ?, ?, 0)
                    ");
                    $stmt->execute([
                        $listId,
                        $item['food_id'] ?? null,
                        $item['quantity'],
                        $item['unit'],
                        $item['notes'] ?? ''
                    ]);
                }
            }

            $this->db->commit();
            return ['success' => true, 'list_id' => $listId];

        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get user's shopping lists
     */
    public function getUserShoppingLists($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT sl.*, COUNT(sli.id) as item_count,
                       SUM(CASE WHEN sli.completed = 1 THEN 1 ELSE 0 END) as completed_items
                FROM dnms_shopping_lists sl
                LEFT JOIN dnms_shopping_list_items sli ON sl.id = sli.list_id
                WHERE sl.user_id = ?
                GROUP BY sl.id
                ORDER BY sl.created_at DESC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get shopping list by ID
     */
    public function getShoppingListById($listId, $userId = null) {
        try {
            $sql = "SELECT * FROM dnms_shopping_lists WHERE id = ?";
            $params = [$listId];

            if ($userId) {
                $sql .= " AND user_id = ?";
                $params[] = $userId;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $list = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($list) {
                // Get items
                $list['items'] = $this->getShoppingListItems($listId);
            }

            return $list;

        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get shopping list items
     */
    private function getShoppingListItems($listId) {
        try {
            $stmt = $this->db->prepare("
                SELECT sli.*, f.name as food_name, f.calories_per_100g, f.protein_per_100g, f.carbs_per_100g, f.fat_per_100g
                FROM dnms_shopping_list_items sli
                LEFT JOIN dnms_foods f ON sli.food_id = f.id
                WHERE sli.list_id = ?
                ORDER BY sli.completed, sli.id
            ");
            $stmt->execute([$listId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Toggle item completion status
     */
    public function toggleItemStatus($itemId, $userId) {
        try {
            // First check if user owns the item
            $stmt = $this->db->prepare("
                SELECT sli.id FROM dnms_shopping_list_items sli
                JOIN dnms_shopping_lists sl ON sli.list_id = sl.id
                WHERE sli.id = ? AND sl.user_id = ?
            ");
            $stmt->execute([$itemId, $userId]);

            if (!$stmt->fetch()) {
                return ['success' => false, 'error' => 'Item not found or access denied'];
            }

            // Toggle status
            $stmt = $this->db->prepare("
                UPDATE dnms_shopping_list_items
                SET completed = !completed, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$itemId]);

            return ['success' => true];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Add item to shopping list
     */
    public function addItemToList($listId, $userId, $data) {
        try {
            // Check if user owns the list
            $stmt = $this->db->prepare("SELECT id FROM dnms_shopping_lists WHERE id = ? AND user_id = ?");
            $stmt->execute([$listId, $userId]);

            if (!$stmt->fetch()) {
                return ['success' => false, 'error' => 'List not found or access denied'];
            }

            $stmt = $this->db->prepare("
                INSERT INTO dnms_shopping_list_items (list_id, food_id, quantity, unit, notes, completed, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, 0, NOW(), NOW())
            ");
            $stmt->execute([
                $listId,
                $data['food_id'] ?? null,
                $data['quantity'],
                $data['unit'],
                $data['notes'] ?? ''
            ]);

            return ['success' => true, 'item_id' => $this->db->lastInsertId()];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Remove item from shopping list
     */
    public function removeItemFromList($itemId, $userId) {
        try {
            // Check if user owns the item
            $stmt = $this->db->prepare("
                SELECT sli.id FROM dnms_shopping_list_items sli
                JOIN dnms_shopping_lists sl ON sli.list_id = sl.id
                WHERE sli.id = ? AND sl.user_id = ?
            ");
            $stmt->execute([$itemId, $userId]);

            if (!$stmt->fetch()) {
                return ['success' => false, 'error' => 'Item not found or access denied'];
            }

            $stmt = $this->db->prepare("DELETE FROM dnms_shopping_list_items WHERE id = ?");
            $stmt->execute([$itemId]);

            return ['success' => true];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Delete shopping list
     */
    public function deleteShoppingList($listId, $userId) {
        try {
            $this->db->beginTransaction();

            // Delete items first
            $stmt = $this->db->prepare("DELETE FROM dnms_shopping_list_items WHERE list_id = ?");
            $stmt->execute([$listId]);

            // Delete list
            $stmt = $this->db->prepare("DELETE FROM dnms_shopping_lists WHERE id = ? AND user_id = ?");
            $stmt->execute([$listId, $userId]);

            $this->db->commit();
            return ['success' => true];

        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get suggested shopping list based on meal plan
     */
    public function getSuggestedShoppingList($userId, $days = 7) {
        try {
            // This would analyze meal plans and suggest ingredients
            // For now, return a basic structure
            $stmt = $this->db->prepare("
                SELECT f.name, SUM(mf.quantity) as total_quantity, f.unit
                FROM dnms_meal_foods mf
                JOIN dnms_foods f ON mf.food_id = f.id
                JOIN dnms_meals m ON mf.meal_id = m.id
                WHERE m.user_id = ? AND m.meal_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                GROUP BY f.id, f.name, f.unit
                ORDER BY total_quantity DESC
            ");
            $stmt->execute([$userId, $days]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            return [];
        }
    }
}
?>
