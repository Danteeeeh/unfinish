<?php
/**
 * URL Routing Configuration
 * Maps URLs to controllers and actions
 */

$routes = [
    // Dashboard Route
    'dashboard' => ['controller' => 'DashboardController', 'action' => 'index'],
    
    // Patient Routes
    'patients/list' => ['controller' => 'PatientController', 'action' => 'list'],
    'patients/add' => ['controller' => 'PatientController', 'action' => 'add'],
    'patients/edit' => ['controller' => 'PatientController', 'action' => 'edit'],
    'patients/delete' => ['controller' => 'PatientController', 'action' => 'delete'],
    'patients/view' => ['controller' => 'PatientController', 'action' => 'view'],
    
    // Sample Routes
    'samples/list' => ['controller' => 'SampleController', 'action' => 'list'],
    'samples/add' => ['controller' => 'SampleController', 'action' => 'add'],
    'samples/status' => ['controller' => 'SampleController', 'action' => 'status'],
    'samples/search' => ['controller' => 'SampleController', 'action' => 'search'],
    'samples/update' => ['controller' => 'SampleController', 'action' => 'update'],
    
    // Test Routes
    'tests/order' => ['controller' => 'TestController', 'action' => 'order'],
    'tests/results' => ['controller' => 'TestController', 'action' => 'results'],
    'tests/pending' => ['controller' => 'TestController', 'action' => 'pending'],
    'tests/update' => ['controller' => 'TestController', 'action' => 'update'],
    'tests/verify' => ['controller' => 'TestController', 'action' => 'verify'],
    
    // Report Routes
    'reports/generate' => ['controller' => 'ReportController', 'action' => 'generate'],
    'reports/view' => ['controller' => 'ReportController', 'action' => 'view'],
    'reports/download' => ['controller' => 'ReportController', 'action' => 'download'],
    'reports/list' => ['controller' => 'ReportController', 'action' => 'list'],

    // Admin Routes
    'admin' => ['controller' => 'AdminController', 'action' => 'index'],
    'admin/users' => ['controller' => 'AdminController', 'action' => 'users'],
    'admin/settings' => ['controller' => 'AdminController', 'action' => 'settings'],
    'admin/future' => ['controller' => 'AdminController', 'action' => 'future'],

    // Default Route
    '' => ['controller' => 'DashboardController', 'action' => 'index'],
];

return $routes;
