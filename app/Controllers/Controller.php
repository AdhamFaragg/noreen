<?php
/**
 * Base Controller Class
 * Provides common functionality for all controllers
 */
class Controller {
    protected $data = [];
    protected $view = null;

    /**
     * Render a view with data and layout wrapper
     */
    protected function render($viewName, $data = []) {
        $this->data = array_merge($this->data, $data);
        $viewPath = APP_PATH . '/Views/' . $viewName . '.php';

        if (!file_exists($viewPath)) {
            die("View not found: {$viewName}");
        }

        extract($this->data);
        ob_start();
        include $viewPath;
        $content = ob_get_clean();
        
        // Wrap with layout
        return $this->wrapWithLayout($content);
    }

    /**
     * Wrap content with layout
     */
    private function wrapWithLayout($content) {
        ob_start();
        $layoutPath = APP_PATH . '/Views/layout.php';
        include $layoutPath;
        return ob_get_clean();
    }

    /**
     * Set data for view
     */
    protected function with($key, $value) {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Redirect to a URL
     */
    protected function redirect($url) {
        header("Location: " . $url);
        exit();
    }

    /**
     * JSON response
     */
    protected function json($data, $statusCode = 200) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit();
    }

    /**
     * Check if user is logged in
     */
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Check if user is admin
     */
    protected function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    /**
     * Check if user is staff
     */
    protected function isStaff() {
        return isset($_SESSION['role']) && in_array($_SESSION['role'], ['staff', 'admin']);
    }

    /**
     * Require login
     */
    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            $this->redirect(BASE_URL . 'login');
        }
    }

    /**
     * Require admin
     */
    protected function requireAdmin() {
        if (!$this->isAdmin()) {
            $this->redirect(BASE_URL);
        }
    }

    /**
     * Get current user ID
     */
    protected function userId() {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get current user role
     */
    protected function userRole() {
        return $_SESSION['role'] ?? null;
    }
}
?>
