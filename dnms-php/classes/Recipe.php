<?php
/**
 * Recipe Management Class
 * Handles recipe creation, editing, and management
 */

class Recipe {
    private $db;

    public function __construct() {
        $this->db = getDNMSDBConnection();
    }

    /**
     * Create a new recipe
     */
    public function createRecipe($userId, $data) {
        try {
            $this->db->beginTransaction();

            // Insert recipe
            $stmt = $this->db->prepare("
                INSERT INTO dnms_recipes (user_id, name, description, servings, prep_time, cook_time, instructions, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            $stmt->execute([
                $userId,
                $data['name'],
                $data['description'],
                $data['servings'],
                $data['prep_time'],
                $data['cook_time'],
                $data['instructions']
            ]);

            $recipeId = $this->db->lastInsertId();

            // Insert ingredients
            if (isset($data['ingredients']) && is_array($data['ingredients'])) {
                foreach ($data['ingredients'] as $ingredient) {
                    $stmt = $this->db->prepare("
                        INSERT INTO dnms_recipe_ingredients (recipe_id, food_id, quantity, unit, notes)
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $recipeId,
                        $ingredient['food_id'],
                        $ingredient['quantity'],
                        $ingredient['unit'],
                        $ingredient['notes'] ?? ''
                    ]);
                }
            }

            $this->db->commit();
            return ['success' => true, 'recipe_id' => $recipeId];

        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get user's recipes
     */
    public function getUserRecipes($userId, $limit = 20, $offset = 0) {
        try {
            $stmt = $this->db->prepare("
                SELECT r.*, COUNT(ri.id) as ingredient_count
                FROM dnms_recipes r
                LEFT JOIN dnms_recipe_ingredients ri ON r.id = ri.recipe_id
                WHERE r.user_id = ?
                GROUP BY r.id
                ORDER BY r.created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$userId, $limit, $offset]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get recipe by ID
     */
    public function getRecipeById($recipeId, $userId = null) {
        try {
            $sql = "SELECT * FROM dnms_recipes WHERE id = ?";
            $params = [$recipeId];

            if ($userId) {
                $sql .= " AND user_id = ?";
                $params[] = $userId;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($recipe) {
                // Get ingredients
                $recipe['ingredients'] = $this->getRecipeIngredients($recipeId);
            }

            return $recipe;

        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get recipe ingredients
     */
    private function getRecipeIngredients($recipeId) {
        try {
            $stmt = $this->db->prepare("
                SELECT ri.*, f.name as food_name, f.calories_per_100g, f.protein_per_100g, f.carbs_per_100g, f.fat_per_100g
                FROM dnms_recipe_ingredients ri
                JOIN dnms_foods f ON ri.food_id = f.id
                WHERE ri.recipe_id = ?
                ORDER BY ri.id
            ");
            $stmt->execute([$recipeId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Update recipe
     */
    public function updateRecipe($recipeId, $userId, $data) {
        try {
            $this->db->beginTransaction();

            // Update recipe
            $stmt = $this->db->prepare("
                UPDATE dnms_recipes
                SET name = ?, description = ?, servings = ?, prep_time = ?, cook_time = ?, instructions = ?, updated_at = NOW()
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([
                $data['name'],
                $data['description'],
                $data['servings'],
                $data['prep_time'],
                $data['cook_time'],
                $data['instructions'],
                $recipeId,
                $userId
            ]);

            // Delete existing ingredients
            $stmt = $this->db->prepare("DELETE FROM dnms_recipe_ingredients WHERE recipe_id = ?");
            $stmt->execute([$recipeId]);

            // Insert new ingredients
            if (isset($data['ingredients']) && is_array($data['ingredients'])) {
                foreach ($data['ingredients'] as $ingredient) {
                    $stmt = $this->db->prepare("
                        INSERT INTO dnms_recipe_ingredients (recipe_id, food_id, quantity, unit, notes)
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $recipeId,
                        $ingredient['food_id'],
                        $ingredient['quantity'],
                        $ingredient['unit'],
                        $ingredient['notes'] ?? ''
                    ]);
                }
            }

            $this->db->commit();
            return ['success' => true];

        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Delete recipe
     */
    public function deleteRecipe($recipeId, $userId) {
        try {
            $this->db->beginTransaction();

            // Delete ingredients first
            $stmt = $this->db->prepare("DELETE FROM dnms_recipe_ingredients WHERE recipe_id = ?");
            $stmt->execute([$recipeId]);

            // Delete recipe
            $stmt = $this->db->prepare("DELETE FROM dnms_recipes WHERE id = ? AND user_id = ?");
            $stmt->execute([$recipeId, $userId]);

            $this->db->commit();
            return ['success' => true];

        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Calculate recipe nutrition
     */
    public function calculateRecipeNutrition($recipeId) {
        try {
            $ingredients = $this->getRecipeIngredients($recipeId);

            $totalNutrition = [
                'calories' => 0,
                'protein' => 0,
                'carbs' => 0,
                'fat' => 0,
                'fiber' => 0,
                'sugar' => 0
            ];

            foreach ($ingredients as $ingredient) {
                $quantity = $ingredient['quantity'];
                $foodNutrition = [
                    'calories' => ($ingredient['calories_per_100g'] ?? 0) * ($quantity / 100),
                    'protein' => ($ingredient['protein_per_100g'] ?? 0) * ($quantity / 100),
                    'carbs' => ($ingredient['carbs_per_100g'] ?? 0) * ($quantity / 100),
                    'fat' => ($ingredient['fat_per_100g'] ?? 0) * ($quantity / 100),
                ];

                $totalNutrition['calories'] += $foodNutrition['calories'];
                $totalNutrition['protein'] += $foodNutrition['protein'];
                $totalNutrition['carbs'] += $foodNutrition['carbs'];
                $totalNutrition['fat'] += $foodNutrition['fat'];
            }

            return $totalNutrition;

        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Search recipes
     */
    public function searchRecipes($searchTerm, $userId = null, $limit = 20) {
        try {
            $sql = "
                SELECT r.*, COUNT(ri.id) as ingredient_count
                FROM dnms_recipes r
                LEFT JOIN dnms_recipe_ingredients ri ON r.id = ri.recipe_id
                WHERE r.name LIKE ? OR r.description LIKE ?
            ";
            $params = ["%{$searchTerm}%", "%{$searchTerm}%"];

            if ($userId) {
                $sql .= " AND r.user_id = ?";
                $params[] = $userId;
            }

            $sql .= " GROUP BY r.id ORDER BY r.created_at DESC LIMIT ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([...$params, $limit]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            return [];
        }
    }
}
?>
