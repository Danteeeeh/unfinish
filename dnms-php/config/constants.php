<?php
/**
 * Application Constants
 * DNMS-PHP - Diet & Nutrition Management System
 */

// Application Settings
define('APP_NAME', 'Diet & Nutrition Management System');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/dnms-php');

// Path Constants
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/assets/uploads/');
define('PROFILE_PIC_PATH', UPLOAD_PATH . 'profile_pics/');
define('FOOD_IMAGE_PATH', UPLOAD_PATH . 'food_images/');

// Session Settings
define('SESSION_TIMEOUT', 3600); // 1 hour
// Use the same session as main system - don't define custom session name

// Pagination
define('RECORDS_PER_PAGE', 20);

// User Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_NUTRITIONIST', 'nutritionist');
define('ROLE_USER', 'user');

// Activity Levels
define('ACTIVITY_LEVELS', [
    'sedentary' => 'Sedentary (little or no exercise)',
    'light' => 'Lightly Active (1-3 days/week)',
    'moderate' => 'Moderately Active (3-5 days/week)',
    'very_active' => 'Very Active (6-7 days/week)',
    'extra_active' => 'Extra Active (physical job + exercise)'
]);

// Activity Level Multipliers for BMR
define('ACTIVITY_MULTIPLIERS', [
    'sedentary' => 1.2,
    'light' => 1.375,
    'moderate' => 1.55,
    'very_active' => 1.725,
    'extra_active' => 1.9
]);

// Goals
define('GOALS', [
    'lose_weight' => 'Lose Weight',
    'maintain_weight' => 'Maintain Weight',
    'gain_weight' => 'Gain Weight',
    'build_muscle' => 'Build Muscle',
    'improve_health' => 'Improve Overall Health'
]);

// Meal Types
define('MEAL_TYPES', [
    'breakfast' => 'Breakfast',
    'morning_snack' => 'Morning Snack',
    'lunch' => 'Lunch',
    'afternoon_snack' => 'Afternoon Snack',
    'dinner' => 'Dinner',
    'evening_snack' => 'Evening Snack'
]);

// Nutrient Daily Values (based on 2000 calorie diet)
define('DAILY_VALUES', [
    'calories' => 2000,
    'protein' => 50,      // grams
    'carbs' => 300,       // grams
    'fat' => 65,          // grams
    'fiber' => 25,        // grams
    'sugar' => 50,        // grams
    'sodium' => 2300,     // mg
    'cholesterol' => 300, // mg
    'vitamin_a' => 900,   // mcg
    'vitamin_c' => 90,    // mg
    'calcium' => 1000,    // mg
    'iron' => 18          // mg
]);

// Food Categories
define('FOOD_CATEGORIES', [
    'fruits' => 'Fruits',
    'vegetables' => 'Vegetables',
    'grains' => 'Grains',
    'protein' => 'Protein Foods',
    'dairy' => 'Dairy',
    'fats_oils' => 'Fats & Oils',
    'beverages' => 'Beverages',
    'snacks' => 'Snacks',
    'sweets' => 'Sweets',
    'other' => 'Other'
]);

// Date Formats
define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'd/m/Y');
define('DISPLAY_DATETIME_FORMAT', 'd/m/Y h:i A');

// File Upload Settings
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);

// BMI Categories
define('BMI_CATEGORIES', [
    'underweight' => ['max' => 18.5, 'label' => 'Underweight', 'color' => '#3498db'],
    'normal' => ['min' => 18.5, 'max' => 24.9, 'label' => 'Normal Weight', 'color' => '#2ecc71'],
    'overweight' => ['min' => 25, 'max' => 29.9, 'label' => 'Overweight', 'color' => '#f39c12'],
    'obese' => ['min' => 30, 'label' => 'Obese', 'color' => '#e74c3c']
]);

// Macronutrient Ratios (percentage of total calories)
define('MACRO_RATIOS', [
    'balanced' => ['protein' => 30, 'carbs' => 40, 'fat' => 30],
    'low_carb' => ['protein' => 40, 'carbs' => 20, 'fat' => 40],
    'high_protein' => ['protein' => 40, 'carbs' => 30, 'fat' => 30],
    'low_fat' => ['protein' => 25, 'carbs' => 55, 'fat' => 20]
]);

// Water Intake (ml per kg of body weight)
define('WATER_INTAKE_PER_KG', 30);

// Calorie Adjustment for Weight Goals
define('CALORIE_ADJUSTMENTS', [
    'lose_weight' => -500,      // 500 calorie deficit
    'maintain_weight' => 0,
    'gain_weight' => 500,       // 500 calorie surplus
    'build_muscle' => 300       // 300 calorie surplus
]);
