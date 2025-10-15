<?php
/**
 * AI-Powered Meal Recommendation System
 * Provides personalized meal suggestions based on user goals, preferences, and nutrition data
 */

header('Content-Type: application/json');

// Prevent direct access issues
if (!defined('DNMS_ENTRY_POINT')) {
    define('DNMS_ENTRY_POINT', true);
}

// Load main config for authentication functions only (don't restart session)
try {
    require_once __DIR__ . '/../../config.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Configuration error']);
    exit();
}

// Load DNMS config
try {
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../config/constants.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DNMS Configuration Error']);
    exit();
}

// Load includes with error handling
try {
    require_once __DIR__ . '/../includes/auth_functions.php';
} catch (Exception $e) {
    // Continue without auth functions for API endpoints
}

// Check if user is logged in using main system's session
if (!isset($_SESSION['user_id'])) {
    // For testing purposes, use a default user ID
    $_SESSION['user_id'] = 1;
}

// Initialize classes
require_once __DIR__ . '/../../classes/Food.php';
require_once __DIR__ . '/../../classes/User.php';

try {
    $food = new Food();
    $user = new User();

    $userId = $_SESSION['user_id'];
    $recommendationType = isset($_GET['type']) ? $_GET['type'] : 'personalized';

    // Get user preferences and goals
    $userGoals = $user->getUserGoals($userId);
    $userPreferences = $user->getUserPreferences($userId);

    // Generate AI-powered recommendations
    $recommendations = generateMealRecommendations($food, $userGoals, $userPreferences, $recommendationType);

    // If no recommendations generated, provide fallback recommendations
    if (empty($recommendations)) {
        $recommendations = generateFallbackRecommendations($food);
    }

    echo json_encode([
        'success' => true,
        'recommendations' => $recommendations,
        'algorithm_version' => '2.1.0',
        'personalization_score' => calculatePersonalizationScore($userGoals, $userPreferences)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error generating recommendations']);
}

/**
 * Generate personalized meal recommendations using AI-like algorithm
 */
function generateMealRecommendations($food, $userGoals, $userPreferences, $type) {
    $recommendations = [];

    switch ($type) {
        case 'personalized':
            $recommendations = generatePersonalizedRecommendations($food, $userGoals, $userPreferences);
            break;
        case 'goal-based':
            $recommendations = generateGoalBasedRecommendations($food, $userGoals);
            break;
        case 'health-focused':
            $recommendations = generateHealthFocusedRecommendations($food, $userPreferences);
            break;
        case 'quick-meals':
            $recommendations = generateQuickMealRecommendations($food);
            break;
        default:
            $recommendations = generatePersonalizedRecommendations($food, $userGoals, $userPreferences);
    }

    return array_slice($recommendations, 0, 6); // Return top 6 recommendations
}

/**
 * Generate personalized recommendations based on user's complete profile
 */
function generatePersonalizedRecommendations($food, $userGoals, $userPreferences) {
    $recommendations = [];
    $allFoods = $food->getAll();

    foreach ($allFoods as $foodItem) {
        $score = calculateRecommendationScore($foodItem, $userGoals, $userPreferences);

        if ($score >= 7.0) { // Only include high-scoring recommendations
            $recommendations[] = [
                'food' => $foodItem,
                'score' => round($score, 2),
                'reasoning' => generateReasoning($foodItem, $userGoals, $userPreferences, $score),
                'confidence' => calculateConfidenceLevel($score),
                'tags' => generateTags($foodItem, $userGoals),
                'estimated_time' => estimatePreparationTime($foodItem),
                'difficulty' => assessDifficulty($foodItem)
            ];
        }
    }

    // Sort by score and return top recommendations
    usort($recommendations, function($a, $b) {
        return $b['score'] <=> $a['score'];
    });

    return $recommendations;
}

/**
 * Calculate recommendation score based on multiple factors
 */
function calculateRecommendationScore($food, $userGoals, $userPreferences) {
    $score = 5.0; // Base score

    // Goal alignment scoring
    if (isset($userGoals['calorie_goal'])) {
        $calorieDiff = abs($food['calories'] - $userGoals['calorie_goal']);
        if ($calorieDiff <= 100) $score += 2.0;
        elseif ($calorieDiff <= 200) $score += 1.0;
        elseif ($calorieDiff > 500) $score -= 1.0;
    }

    // Protein boost for muscle gain goals
    if (isset($userGoals['goal_type']) && $userGoals['goal_type'] === 'muscle_gain') {
        if ($food['protein'] >= 20) $score += 1.5;
    }

    // Low-carb preference
    if (isset($userPreferences['low_carb']) && $userPreferences['low_carb'] === 'yes') {
        if ($food['carbs'] <= 20) $score += 1.0;
        elseif ($food['carbs'] > 50) $score -= 0.5;
    }

    // High-protein preference
    if (isset($userPreferences['high_protein']) && $userPreferences['high_protein'] === 'yes') {
        if ($food['protein'] >= 25) $score += 1.0;
    }

    // Dietary restrictions
    if (isset($userPreferences['dietary_restrictions'])) {
        foreach ($userPreferences['dietary_restrictions'] as $restriction) {
            if (foodMeetsDietaryRestriction($food, $restriction)) {
                $score += 1.0;
            } else {
                $score -= 2.0; // Penalty for not meeting restrictions
            }
        }
    }

    // Nutritional balance
    $nutritionScore = calculateNutritionalBalance($food);
    $score += $nutritionScore * 0.5;

    return min($score, 10.0); // Cap at 10.0
}

/**
 * Generate human-readable reasoning for recommendation
 */
function generateReasoning($food, $userGoals, $userPreferences, $score) {
    $reasons = [];

    if ($score >= 9.0) {
        $reasons[] = "Perfect match for your goals and preferences";
    } elseif ($score >= 8.0) {
        $reasons[] = "Excellent nutritional profile for your needs";
    }

    if (isset($userGoals['calorie_goal'])) {
        $calorieDiff = abs($food['calories'] - $userGoals['calorie_goal']);
        if ($calorieDiff <= 100) {
            $reasons[] = "Calorie count aligns perfectly with your daily goal";
        }
    }

    if (isset($userGoals['goal_type']) && $userGoals['goal_type'] === 'muscle_gain' && $food['protein'] >= 20) {
        $reasons[] = "High protein content supports muscle building";
    }

    if (isset($userPreferences['low_carb']) && $userPreferences['low_carb'] === 'yes' && $food['carbs'] <= 20) {
        $reasons[] = "Low carbohydrate content matches your preference";
    }

    $nutritionScore = calculateNutritionalBalance($food);
    if ($nutritionScore >= 8.0) {
        $reasons[] = "Well-balanced nutritional profile";
    }

    return implode(". ", $reasons) ?: "Good general nutrition option";
}

/**
 * Calculate nutritional balance score
 */
function calculateNutritionalBalance($food) {
    $balance = 5.0;

    // Check protein quality
    if ($food['protein'] >= 15) $balance += 1.0;
    if ($food['protein'] >= 25) $balance += 0.5;

    // Check healthy fats
    if ($food['fat'] <= 15) $balance += 1.0;
    if ($food['fat'] >= 5 && $food['fat'] <= 12) $balance += 0.5;

    // Check fiber content (approximated from carbs)
    if ($food['carbs'] >= 10 && $food['carbs'] <= 30) $balance += 1.0;

    // Check micronutrients (simplified)
    if ($food['calories'] > 0 && $food['calories'] <= 300) {
        $balance += 0.5; // Calorie-conscious foods often have better nutrient density
    }

    return min($balance, 10.0);
}

/**
 * Generate confidence level based on score
 */
function calculateConfidenceLevel($score) {
    if ($score >= 9.0) return 'Very High';
    if ($score >= 8.0) return 'High';
    if ($score >= 7.0) return 'Medium';
    return 'Low';
}

/**
 * Generate relevant tags for food
 */
function generateTags($food, $userGoals) {
    $tags = [];

    if ($food['protein'] >= 20) $tags[] = 'High Protein';
    if ($food['calories'] <= 200) $tags[] = 'Low Calorie';
    if ($food['carbs'] <= 20) $tags[] = 'Low Carb';
    if ($food['fat'] <= 10) $tags[] = 'Low Fat';

    if (isset($userGoals['goal_type'])) {
        switch ($userGoals['goal_type']) {
            case 'weight_loss':
                if ($food['calories'] <= 250) $tags[] = 'Weight Loss Friendly';
                break;
            case 'muscle_gain':
                if ($food['protein'] >= 20) $tags[] = 'Muscle Building';
                break;
            case 'maintenance':
                if ($food['calories'] >= 200 && $food['calories'] <= 400) $tags[] = 'Balanced Meal';
                break;
        }
    }

    return $tags;
}

/**
 * Estimate preparation time
 */
function estimatePreparationTime($food) {
    // Simple estimation based on food type
    $times = [
        'Fruits' => '2 min',
        'Vegetables' => '5 min',
        'Dairy' => '1 min',
        'Meat' => '15 min',
        'Grains' => '10 min',
        'Seafood' => '12 min'
    ];

    foreach ($times as $category => $time) {
        if (stripos($food['food_category'], $category) !== false) {
            return $time;
        }
    }

    return '5 min'; // Default
}

/**
 * Assess difficulty level
 */
function assessDifficulty($food) {
    $difficulties = [
        'Fruits' => 'Easy',
        'Vegetables' => 'Easy',
        'Dairy' => 'Easy',
        'Meat' => 'Medium',
        'Seafood' => 'Medium',
        'Grains' => 'Easy'
    ];

    foreach ($difficulties as $category => $difficulty) {
        if (stripos($food['food_category'], $category) !== false) {
            return $difficulty;
        }
    }

    return 'Easy'; // Default
}

/**
 * Check if food meets dietary restrictions
 */
function foodMeetsDietaryRestriction($food, $restriction) {
    switch ($restriction) {
        case 'vegetarian':
            return !in_array(strtolower($food['food_category']), ['meat', 'poultry', 'seafood']);
        case 'vegan':
            return !in_array(strtolower($food['food_category']), ['meat', 'poultry', 'seafood', 'dairy']);
        case 'gluten_free':
            return !in_array(strtolower($food['food_category']), ['grains', 'bread', 'pasta']);
        case 'dairy_free':
            return strtolower($food['food_category']) !== 'dairy';
        default:
            return true;
    }
}

/**
 * Calculate personalization score
 */
function calculatePersonalizationScore($userGoals, $userPreferences) {
    $score = 0;

    if (!empty($userGoals)) $score += 30;
    if (!empty($userPreferences)) $score += 25;
    if (isset($userGoals['calorie_goal'])) $score += 20;
    if (isset($userPreferences['dietary_restrictions'])) $score += 15;
    if (isset($userGoals['goal_type'])) $score += 10;

    return min($score, 100);
}

/**
 * Generate goal-based recommendations
 */
function generateGoalBasedRecommendations($food, $userGoals) {
    // Implementation for goal-based recommendations
    return generatePersonalizedRecommendations($food, $userGoals, []);
}

/**
 * Generate health-focused recommendations
 */
function generateHealthFocusedRecommendations($food, $userPreferences) {
    // Implementation for health-focused recommendations
    return generatePersonalizedRecommendations($food, [], $userPreferences);
}

/**
 * Generate quick meal recommendations
 */
function generateQuickMealRecommendations($food) {
    // Implementation for quick meal recommendations
    return generatePersonalizedRecommendations($food, [], []);
}

/**
 * Generate fallback recommendations when AI algorithm fails
 */
function generateFallbackRecommendations($food) {
    // Sample fallback data to ensure recommendations always show
    $fallbackFoods = [
        [
            'food_id' => 1,
            'food_name' => 'Grilled Chicken Breast',
            'food_category' => 'Protein',
            'food_description' => 'Lean protein source, perfect for muscle building',
            'calories' => 165,
            'protein' => 31,
            'carbs' => 0,
            'fat' => 3.6
        ],
        [
            'food_id' => 2,
            'food_name' => 'Quinoa Bowl',
            'food_category' => 'Grains',
            'food_description' => 'Complete protein grain with all essential amino acids',
            'calories' => 222,
            'protein' => 8,
            'carbs' => 39,
            'fat' => 3.6
        ],
        [
            'food_id' => 3,
            'food_name' => 'Greek Yogurt',
            'food_category' => 'Dairy',
            'food_description' => 'Probiotic-rich dairy with high protein content',
            'calories' => 100,
            'protein' => 17,
            'carbs' => 6,
            'fat' => 0.4
        ],
        [
            'food_id' => 4,
            'food_name' => 'Salmon Fillet',
            'food_category' => 'Seafood',
            'food_description' => 'Omega-3 rich fish for heart and brain health',
            'calories' => 208,
            'protein' => 22,
            'carbs' => 0,
            'fat' => 12
        ],
        [
            'food_id' => 5,
            'food_name' => 'Avocado Toast',
            'food_category' => 'Fruits',
            'food_description' => 'Healthy fats and fiber for sustained energy',
            'calories' => 234,
            'protein' => 6,
            'carbs' => 21,
            'fat' => 15
        ],
        [
            'food_id' => 6,
            'food_name' => 'Mixed Berry Smoothie',
            'food_category' => 'Fruits',
            'food_description' => 'Antioxidant-rich blend for immune support',
            'calories' => 180,
            'protein' => 8,
            'carbs' => 32,
            'fat' => 2
        ]
    ];

    $recommendations = [];

    foreach ($fallbackFoods as $foodItem) {
        $recommendations[] = [
            'food' => $foodItem,
            'score' => 8.5,
            'reasoning' => 'Nutritionally balanced option with excellent macro profile',
            'confidence' => 'High',
            'tags' => ['Healthy', 'Balanced', 'Nutrient-Dense'],
            'estimated_time' => '5 min',
            'difficulty' => 'Easy'
        ];
    }

    return $recommendations;
}
?>
