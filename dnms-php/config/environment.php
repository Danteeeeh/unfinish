<?php
/**
 * Environment Configuration
 * DNMS-PHP - Diet & Nutrition Management System
 */

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Set error log path - use a fallback if ROOT_PATH is not defined yet
if (defined('ROOT_PATH')) {
    ini_set('error_log', ROOT_PATH . '/logs/error.log');
} else {
    ini_set('error_log', dirname(__DIR__) . '/logs/error.log');
}

// Timezone
date_default_timezone_set('Asia/Manila');

// Session Configuration - only set if session is not active
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
}

// PHP Settings
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');
ini_set('max_execution_time', '300');
ini_set('memory_limit', '256M');

// Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoloader for Classes
spl_autoload_register(function ($class) {
    $rootPath = defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__);
    $file = $rootPath . '/classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Include Required Files
$rootPath = defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__);
require_once $rootPath . '/config/database.php';
require_once $rootPath . '/config/constants.php';
require_once $rootPath . '/includes/auth_functions.php';

// Helper Functions
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function formatDate($date, $format = DISPLAY_DATE_FORMAT) {
    return date($format, strtotime($date));
}

function calculateBMI($weight, $height) {
    // weight in kg, height in cm
    $heightInMeters = $height / 100;
    return round($weight / ($heightInMeters * $heightInMeters), 1);
}

function getBMICategory($bmi) {
    foreach (BMI_CATEGORIES as $key => $category) {
        if (isset($category['max']) && $bmi < $category['max']) {
            return $key;
        }
        if (isset($category['min']) && !isset($category['max']) && $bmi >= $category['min']) {
            return $key;
        }
        if (isset($category['min']) && isset($category['max']) && $bmi >= $category['min'] && $bmi <= $category['max']) {
            return $key;
        }
    }
    return 'normal';
}

function calculateBMR($weight, $height, $age, $gender) {
    // Mifflin-St Jeor Equation
    // BMR = (10 × weight in kg) + (6.25 × height in cm) - (5 × age in years) + s
    // s = +5 for males, -161 for females
    
    $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age);
    $bmr += ($gender === 'male') ? 5 : -161;
    
    return round($bmr);
}

function calculateTDEE($bmr, $activityLevel) {
    $multiplier = ACTIVITY_MULTIPLIERS[$activityLevel] ?? 1.2;
    return round($bmr * $multiplier);
}

function calculateTargetCalories($tdee, $goal) {
    $adjustment = CALORIE_ADJUSTMENTS[$goal] ?? 0;
    return $tdee + $adjustment;
}

function calculateWaterIntake($weight) {
    // ml per day
    return round($weight * WATER_INTAKE_PER_KG);
}

function calculateMacros($calories, $ratio = 'balanced') {
    $ratios = MACRO_RATIOS[$ratio] ?? MACRO_RATIOS['balanced'];
    
    return [
        'protein' => round(($calories * $ratios['protein'] / 100) / 4), // 4 cal per gram
        'carbs' => round(($calories * $ratios['carbs'] / 100) / 4),     // 4 cal per gram
        'fat' => round(($calories * $ratios['fat'] / 100) / 9)          // 9 cal per gram
    ];
}
