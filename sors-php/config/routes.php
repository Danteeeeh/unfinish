<?php
/**
 * URL Routing Configuration
 * Maps URLs to controllers and actions
 */

$routes = [
    // Dashboard Route
    'dashboard' => ['controller' => 'DashboardController', 'action' => 'index'],
    
    // Surgery Routes
    'surgeries/list' => ['controller' => 'SurgeryController', 'action' => 'list'],
    'surgeries/add' => ['controller' => 'SurgeryController', 'action' => 'add'],
    'surgeries/edit' => ['controller' => 'SurgeryController', 'action' => 'edit'],
    'surgeries/delete' => ['controller' => 'SurgeryController', 'action' => 'delete'],
    'surgeries/view' => ['controller' => 'SurgeryController', 'action' => 'view'],
    'surgeries/schedule' => ['controller' => 'SurgeryController', 'action' => 'schedule'],
    'surgeries/calendar' => ['controller' => 'SurgeryController', 'action' => 'calendar'],
    
    // Room Routes
    'rooms/list' => ['controller' => 'RoomController', 'action' => 'list'],
    'rooms/add' => ['controller' => 'RoomController', 'action' => 'add'],
    'rooms/edit' => ['controller' => 'RoomController', 'action' => 'edit'],
    'rooms/delete' => ['controller' => 'RoomController', 'action' => 'delete'],
    'rooms/view' => ['controller' => 'RoomController', 'action' => 'view'],
    'rooms/availability' => ['controller' => 'RoomController', 'action' => 'availability'],
    
    // Staff Routes
    'staff/list' => ['controller' => 'StaffController', 'action' => 'list'],
    'staff/add' => ['controller' => 'StaffController', 'action' => 'add'],
    'staff/edit' => ['controller' => 'StaffController', 'action' => 'edit'],
    'staff/delete' => ['controller' => 'StaffController', 'action' => 'delete'],
    'staff/view' => ['controller' => 'StaffController', 'action' => 'view'],
    'staff/schedule' => ['controller' => 'StaffController', 'action' => 'schedule'],
    
    // Report Routes
    'reports/list' => ['controller' => 'ReportController', 'action' => 'list'],
    'reports/generate' => ['controller' => 'ReportController', 'action' => 'generate'],
    'reports/view' => ['controller' => 'ReportController', 'action' => 'view'],
    'reports/download' => ['controller' => 'ReportController', 'action' => 'download'],

    // Default Route
    '' => ['controller' => 'DashboardController', 'action' => 'index'],
];

return $routes;
