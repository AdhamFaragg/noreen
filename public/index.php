<?php
/**
 * Front Controller / Router
 * Central entry point for all requests
 * 
 * Usage: index.php?page=home
 *        index.php?page=products&action=index
 *        index.php?page=auth&action=login
 */

require_once dirname(__DIR__) . '/app/bootstrap.php';

/**
 * Simple Router
 */
class Router {
    private $page;
    private $action;
    private $controller;

    public function __construct() {
        $this->page = $_GET['page'] ?? 'home';
        $this->action = $_GET['action'] ?? 'index';
    }

    public function dispatch() {
        // Map page to controller
        $controller = $this->getController();

        if (!$controller) {
            $this->notFound();
            return;
        }

        try {
            // Instantiate controller
            $controllerClass = $controller . 'Controller';
            $controllerObj = new $controllerClass();

            // Call action method
            if (!method_exists($controllerObj, $this->action)) {
                $this->notFound();
                return;
            }

            $method = $this->action;
            $output = $controllerObj->$method();

            // If controller returns output, echo it
            if ($output) {
                echo $output;
            }
        } catch (Exception $e) {
            error_log('Router Error: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            $this->error('An error occurred: ' . $e->getMessage());
        }
    }

    private function getController() {
        $pageMap = [
            'home' => 'Home',
            'products' => 'Product',
            'product' => 'Product',
            'cart' => 'Cart',
            'auth' => 'Auth',
            'login' => 'Auth',
            'register' => 'Auth',
            'logout' => 'Auth',
            'admin' => 'Admin',
        ];

        $page = strtolower($this->page);
        return $pageMap[$page] ?? null;
    }

    private function notFound() {
        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1>';
    }

    private function error($message) {
        http_response_code(500);
        echo '<h1>Error: ' . htmlspecialchars($message) . '</h1>';
    }
}

// Create and dispatch router
$router = new Router();
$router->dispatch();
?>
