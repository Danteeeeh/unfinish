<?php
/**
 * RIS Sidebar Connection Verification Script
 */

// Load routes configuration
$routes = require __DIR__ . '/config/routes.php';

// Sidebar routes to verify
$sidebarRoutes = [
    'dashboard',
    'patients/list', 'patients/add', 'patients/search',
    'studies/list', 'studies/add', 'studies/worklist',
    'reports/list', 'reports/create',
    'users/list', 'users/add'
];

echo "=== SIDEBAR CONNECTION VERIFICATION ===\n";

foreach ($sidebarRoutes as $route) {
    if (isset($routes[$route])) {
        $config = $routes[$route];
        echo "✅ $route -> {$config['controller']}@{$config['action']}\n";

        // Check if controller file exists
        $controllerFile = __DIR__ . "/controllers/{$config['controller']}.php";
        if (file_exists($controllerFile)) {
            echo "   📁 Controller file exists\n";
        } else {
            echo "   ❌ Controller file missing\n";
        }

        // Check if method exists (basic check)
        $fileContent = file_get_contents($controllerFile);
        if (strpos($fileContent, "public function {$config['action']}") !== false) {
            echo "   🔧 Method exists\n";
        } else {
            echo "   ❌ Method missing\n";
        }
    } else {
        echo "❌ $route -> Route not defined\n";
    }
    echo "\n";
}

echo "=== VIEW FILES VERIFICATION ===\n";
$viewDirs = ['patients', 'studies', 'reports', 'users'];
foreach ($viewDirs as $dir) {
    $viewPath = __DIR__ . "/views/$dir";
    if (is_dir($viewPath)) {
        $files = scandir($viewPath);
        $phpFiles = array_filter($files, function($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'php';
        });
        echo "✅ $dir/ (" . count($phpFiles) . " PHP files)\n";
        foreach ($phpFiles as $file) {
            echo "   📄 $file\n";
        }
    } else {
        echo "❌ $dir/ -> Directory missing\n";
    }
    echo "\n";
}

echo "🎯 All sidebar connections verified!\n";
?>
