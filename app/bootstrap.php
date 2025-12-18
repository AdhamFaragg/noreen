<?php
/**
 * Bootstrap and Autoloader
 * Initializes app, sets up sessions, constants, and auto-loads classes
 */

// Define paths
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');

// Define base URL
define('BASE_URL', 'http://localhost:8080/');

// Define upload directory
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/noreen/assets/images/products/');
define('UPLOAD_URL', BASE_URL . 'assets/images/products/');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Simple PSR-4 style autoloader
 */
spl_autoload_register(function ($className) {
    // Model classes
    $modelPath = APP_PATH . '/Models/' . $className . '.php';
    if (file_exists($modelPath)) {
        require_once $modelPath;
        return;
    }

    // Controller classes
    $controllerPath = APP_PATH . '/Controllers/' . $className . '.php';
    if (file_exists($controllerPath)) {
        require_once $controllerPath;
        return;
    }
});

// Load base classes
require_once APP_PATH . '/Models/DB.php';
require_once APP_PATH . '/Models/Model.php';
require_once APP_PATH . '/Controllers/Controller.php';

// Load all model classes
$modelFiles = glob(APP_PATH . '/Models/*.php');
foreach ($modelFiles as $file) {
    if ($file !== APP_PATH . '/Models/DB.php' && $file !== APP_PATH . '/Models/Model.php') {
        require_once $file;
    }
}

// Load all controller classes
$controllerFiles = glob(APP_PATH . '/Controllers/*.php');
foreach ($controllerFiles as $file) {
    if ($file !== APP_PATH . '/Controllers/Controller.php') {
        require_once $file;
    }
}
?>
