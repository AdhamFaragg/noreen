<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'clothing_store');

// Create database connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to UTF-8
mysqli_set_charset($conn, "utf8");

// Define base URL (change this according to your setup)
define('BASE_URL', 'http://localhost/noreen/');

// Define upload directory
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/noreen/assets/images/products/');
define('UPLOAD_URL', BASE_URL . 'assets/images/products/');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
