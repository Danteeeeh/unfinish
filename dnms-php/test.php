<?php
echo "Testing DNMS-PHP System<br><br>";

// Test 1: Check if files exist
$files_to_check = [
    'config/database.php',
    'config/constants.php',
    'classes/User.php',
    'classes/Food.php',
    'classes/Meal.php',
    'controllers/user/dashboard.php',
    'views/user/dashboard_view.php'
];

echo "File Existence Check:<br>";
foreach ($files_to_check as $file) {
    $path = __DIR__ . '/' . $file;
    echo ($file . ': ' . (file_exists($path) ? '✓ EXISTS' : '✗ MISSING') . '<br>');
}

echo "<br>";

// Test 2: Try to load database configuration
try {
    require_once __DIR__ . '/config/database.php';
    require_once __DIR__ . '/config/constants.php';
    echo "✓ Configuration files loaded successfully<br>";
} catch (Exception $e) {
    echo "✗ Configuration loading failed: " . $e->getMessage() . '<br>';
}

echo "<br>";

// Test 3: Try database connection
try {
    $db = getDBConnection();
    echo "✓ Database connection successful<br>";
    $db = null;
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . '<br>';
}

echo "<br>Test completed.";
?>
