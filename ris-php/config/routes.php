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
    'patients/search' => ['controller' => 'PatientController', 'action' => 'search'],
    
    // Study Routes
    'studies/list' => ['controller' => 'StudyController', 'action' => 'list'],
    'studies/add' => ['controller' => 'StudyController', 'action' => 'add'],
    'studies/edit' => ['controller' => 'StudyController', 'action' => 'edit'],
    'studies/delete' => ['controller' => 'StudyController', 'action' => 'delete'],
    'studies/view' => ['controller' => 'StudyController', 'action' => 'view'],
    'studies/images' => ['controller' => 'StudyController', 'action' => 'images'],
    'studies/upload' => ['controller' => 'StudyController', 'action' => 'uploadImages'],
    'studies/worklist' => ['controller' => 'StudyController', 'action' => 'worklist'],
    
    // Report Routes
    'reports/list' => ['controller' => 'ReportController', 'action' => 'list'],
    'reports/create' => ['controller' => 'ReportController', 'action' => 'create'],
    'reports/edit' => ['controller' => 'ReportController', 'action' => 'edit'],
    'reports/view' => ['controller' => 'ReportController', 'action' => 'view'],
    'reports/delete' => ['controller' => 'ReportController', 'action' => 'delete'],
    'reports/pdf' => ['controller' => 'ReportController', 'action' => 'generatePDF'],
    'reports/finalize' => ['controller' => 'ReportController', 'action' => 'finalize'],
    
    // User Management Routes
    'users/list' => ['controller' => 'UserController', 'action' => 'list'],
    'users/add' => ['controller' => 'UserController', 'action' => 'add'],
    'users/edit' => ['controller' => 'UserController', 'action' => 'edit'],
    'users/delete' => ['controller' => 'UserController', 'action' => 'delete'],

    // Default Route
    '' => ['controller' => 'DashboardController', 'action' => 'index'],
];

return $routes;
