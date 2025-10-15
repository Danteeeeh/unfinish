<?php
define('ROOT_PATH', dirname(dirname(__DIR__)));
require_once ROOT_PATH . '/config/environment.php';

logout();
setFlash('success', 'You have been logged out successfully');
redirect(APP_URL . '/controllers/auth/login.php');
