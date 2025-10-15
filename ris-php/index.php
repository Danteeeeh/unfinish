<?php
/**
 * Main Entry Point
 * Routes requests to appropriate controllers
 * RIS-PHP - Radiology Information System
 */

// Load main config FIRST for authentication
require_once __DIR__ . '/../config.php';

// Load configuration
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';

// Load includes (after main config to avoid conflicts)
require_once __DIR__ . '/includes/db_functions.php';
require_once __DIR__ . '/includes/utils.php';

// Note: auth_functions.php is not loaded here to avoid conflicts with main config.php

requireLogin();

// Get route from URL
$route = isset($_GET['route']) ? $_GET['route'] : '';

// Load routes configuration
$routes = require_once __DIR__ . '/config/routes.php';

// Handle route not found
if (!isset($routes[$route])) {
    header("HTTP/1.0 404 Not Found");
    include __DIR__ . '/views/errors/404.php';
    exit;
}

// Find matching route
$routeConfig = $routes[$route];
$controllerName = $routeConfig['controller'];
$action = $routeConfig['action'];

// Load and instantiate controller
$controllerFile = __DIR__ . '/controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        
        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            die("Action '{$action}' not found in controller '{$controllerName}'.");
        }
    } else {
        die("Controller class '{$controllerName}' not found.");
    }
} else {
    die("Controller file '{$controllerName}.php' not found.");
}
