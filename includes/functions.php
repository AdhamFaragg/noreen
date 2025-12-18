<?php
// Security Functions
function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_real_escape_string($conn, $data);
    return $data;
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

// Session Functions
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function is_staff() {
    return isset($_SESSION['role']) && ($_SESSION['role'] === 'staff' || $_SESSION['role'] === 'admin');
}

function is_customer() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'customer';
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function require_login() {
    if (!is_logged_in()) {
        redirect(BASE_URL . 'login.php');
    }
}

function require_admin() {
    if (!is_admin()) {
        redirect(BASE_URL . 'index.php');
    }
}

function require_staff() {
    if (!is_staff()) {
        redirect(BASE_URL . 'index.php');
    }
}

// Alert Messages
function set_message($message, $type = 'info') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

function display_message() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $type = $_SESSION['message_type'] ?? 'info';
        
        $alert_class = 'alert-info';
        switch ($type) {
            case 'success':
                $alert_class = 'alert-success';
                break;
            case 'error':
                $alert_class = 'alert-danger';
                break;
            case 'warning':
                $alert_class = 'alert-warning';
                break;
        }
        
        echo '<div class="alert ' . $alert_class . ' alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($message);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
        
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
}

// Image Upload Function
function upload_image($file, $prefix = 'img_') {
    $target_dir = UPLOAD_DIR;
    
    // Create directory if not exists
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = $prefix . time() . '_' . uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Check if file is an actual image
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return ['success' => false, 'message' => 'File is not an image.'];
    }
    
    // Check file size (5MB max)
    if ($file["size"] > 5000000) {
        return ['success' => false, 'message' => 'File is too large. Max 5MB.'];
    }
    
    // Allow certain file formats
    $allowed_extensions = ["jpg", "jpeg", "png", "gif", "webp"];
    if (!in_array($file_extension, $allowed_extensions)) {
        return ['success' => false, 'message' => 'Only JPG, JPEG, PNG, GIF & WEBP files are allowed.'];
    }
    
    // Upload file
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ['success' => true, 'filename' => $new_filename];
    } else {
        return ['success' => false, 'message' => 'Error uploading file.'];
    }
}

// Delete Image Function
function delete_image($filename) {
    if (!empty($filename)) {
        $file_path = UPLOAD_DIR . $filename;
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
}

// Product Functions
function get_product_by_id($product_id) {
    global $conn;
    $product_id = (int)$product_id;
    $query = "SELECT p.*, c.category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.category_id 
              WHERE p.product_id = $product_id";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

function get_all_products($limit = null, $offset = 0) {
    global $conn;
    $query = "SELECT p.*, c.category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.category_id 
              WHERE p.status = 'active' 
              ORDER BY p.created_at DESC";
    
    if ($limit !== null) {
        $limit = (int)$limit;
        $offset = (int)$offset;
        $query .= " LIMIT $offset, $limit";
    }
    
    $result = mysqli_query($conn, $query);
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    return $products;
}

function get_featured_products($limit = 8) {
    global $conn;
    $limit = (int)$limit;
    $query = "SELECT p.*, c.category_name 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.category_id 
              WHERE p.status = 'active' AND p.featured = 1 
              ORDER BY p.created_at DESC 
              LIMIT $limit";
    
    $result = mysqli_query($conn, $query);
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    return $products;
}

function count_products($search = '', $category_id = null) {
    global $conn;
    $query = "SELECT COUNT(*) as total FROM products WHERE status = 'active'";
    
    if (!empty($search)) {
        $search = sanitize_input($search);
        $query .= " AND (product_name LIKE '%$search%' OR description LIKE '%$search%')";
    }
    
    if ($category_id !== null) {
        $category_id = (int)$category_id;
        $query .= " AND category_id = $category_id";
    }
    
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

// Category Functions
function get_all_categories() {
    global $conn;
    $query = "SELECT * FROM categories WHERE status = 'active' ORDER BY category_name";
    $result = mysqli_query($conn, $query);
    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    return $categories;
}

function get_category_by_id($category_id) {
    global $conn;
    $category_id = (int)$category_id;
    $query = "SELECT * FROM categories WHERE category_id = $category_id";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

// Cart Functions
function get_cart_count() {
    global $conn;
    if (!is_logged_in()) {
        return 0;
    }
    
    $user_id = (int)$_SESSION['user_id'];
    $query = "SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

function get_cart_items($user_id) {
    global $conn;
    $user_id = (int)$user_id;
    $query = "SELECT c.*, p.product_name, p.price, p.discount_price, p.image, p.stock 
              FROM cart c 
              JOIN products p ON c.product_id = p.product_id 
              WHERE c.user_id = $user_id";
    $result = mysqli_query($conn, $query);
    $items = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
    return $items;
}

function calculate_cart_total($user_id) {
    global $conn;
    $user_id = (int)$user_id;
    $query = "SELECT SUM(c.quantity * IFNULL(p.discount_price, p.price)) as total 
              FROM cart c 
              JOIN products p ON c.product_id = p.product_id 
              WHERE c.user_id = $user_id";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

function add_to_cart($user_id, $product_id, $quantity = 1, $size = '', $color = '') {
    global $conn;
    $user_id = (int)$user_id;
    $product_id = (int)$product_id;
    $quantity = (int)$quantity;
    $size = sanitize_input($size);
    $color = sanitize_input($color);
    
    // Check if item already exists
    $check_query = "SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id AND size = '$size' AND color = '$color'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Update quantity
        $query = "UPDATE cart SET quantity = quantity + $quantity WHERE user_id = $user_id AND product_id = $product_id AND size = '$size' AND color = '$color'";
    } else {
        // Insert new item
        $query = "INSERT INTO cart (user_id, product_id, quantity, size, color) VALUES ($user_id, $product_id, $quantity, '$size', '$color')";
    }
    
    return mysqli_query($conn, $query);
}

function remove_from_cart($cart_id, $user_id) {
    global $conn;
    $cart_id = (int)$cart_id;
    $user_id = (int)$user_id;
    $query = "DELETE FROM cart WHERE cart_id = $cart_id AND user_id = $user_id";
    return mysqli_query($conn, $query);
}

function clear_cart($user_id) {
    global $conn;
    $user_id = (int)$user_id;
    $query = "DELETE FROM cart WHERE user_id = $user_id";
    return mysqli_query($conn, $query);
}

// Wishlist Functions
function get_wishlist_count() {
    global $conn;
    if (!is_logged_in()) {
        return 0;
    }
    
    $user_id = (int)$_SESSION['user_id'];
    $query = "SELECT COUNT(*) as total FROM wishlist WHERE user_id = $user_id";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

function is_in_wishlist($user_id, $product_id) {
    global $conn;
    $user_id = (int)$user_id;
    $product_id = (int)$product_id;
    $query = "SELECT * FROM wishlist WHERE user_id = $user_id AND product_id = $product_id";
    $result = mysqli_query($conn, $query);
    return mysqli_num_rows($result) > 0;
}

// Order Functions
function generate_order_number() {
    return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
}

// Discount Functions
function validate_discount_code($code, $total_amount) {
    global $conn;
    $code = sanitize_input($code);
    
    $query = "SELECT * FROM discounts WHERE code = '$code' AND status = 'active' 
              AND (start_date IS NULL OR start_date <= CURDATE()) 
              AND (end_date IS NULL OR end_date >= CURDATE()) 
              AND (usage_limit = 0 OR used_count < usage_limit)
              AND min_purchase <= $total_amount";
    
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return false;
}

function calculate_discount($discount, $total_amount) {
    if ($discount['discount_type'] === 'percentage') {
        $discount_amount = ($total_amount * $discount['discount_value']) / 100;
        
        if ($discount['max_discount'] && $discount_amount > $discount['max_discount']) {
            $discount_amount = $discount['max_discount'];
        }
    } else {
        $discount_amount = $discount['discount_value'];
    }
    
    return min($discount_amount, $total_amount);
}

// Utility Functions
function format_price($price) {
    return '$' . number_format($price, 2);
}

function time_ago($timestamp) {
    $time_ago = strtotime($timestamp);
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    $seconds = $time_difference;
    
    $minutes = round($seconds / 60);
    $hours = round($seconds / 3600);
    $days = round($seconds / 86400);
    $weeks = round($seconds / 604800);
    $months = round($seconds / 2629440);
    $years = round($seconds / 31553280);
    
    if ($seconds <= 60) {
        return "Just now";
    } else if ($minutes <= 60) {
        return "$minutes min ago";
    } else if ($hours <= 24) {
        return "$hours hours ago";
    } else if ($days <= 7) {
        return "$days days ago";
    } else if ($weeks <= 4.3) {
        return "$weeks weeks ago";
    } else if ($months <= 12) {
        return "$months months ago";
    } else {
        return "$years years ago";
    }
}

function truncate_text($text, $length = 100) {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . '...';
    }
    return $text;
}

// Pagination Function
function create_pagination($total_items, $items_per_page, $current_page, $base_url) {
    $total_pages = ceil($total_items / $items_per_page);
    
    if ($total_pages <= 1) {
        return '';
    }
    
    $pagination = '<nav><ul class="pagination justify-content-center">';
    
    // Previous button
    if ($current_page > 1) {
        $pagination .= '<li class="page-item"><a class="page-link" href="' . $base_url . ($current_page - 1) . '">Previous</a></li>';
    } else {
        $pagination .= '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
    }
    
    // Page numbers
    for ($i = 1; $i <= $total_pages; $i++) {
        if ($i == $current_page) {
            $pagination .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $pagination .= '<li class="page-item"><a class="page-link" href="' . $base_url . $i . '">' . $i . '</a></li>';
        }
    }
    
    // Next button
    if ($current_page < $total_pages) {
        $pagination .= '<li class="page-item"><a class="page-link" href="' . $base_url . ($current_page + 1) . '">Next</a></li>';
    } else {
        $pagination .= '<li class="page-item disabled"><span class="page-link">Next</span></li>';
    }
    
    $pagination .= '</ul></nav>';
    
    return $pagination;
}

// Get Product Reviews
function get_product_reviews($product_id) {
    global $conn;
    $product_id = (int)$product_id;
    $query = "SELECT r.*, u.full_name, u.username 
              FROM reviews r 
              JOIN users u ON r.user_id = u.user_id 
              WHERE r.product_id = $product_id AND r.status = 'approved' 
              ORDER BY r.created_at DESC";
    $result = mysqli_query($conn, $query);
    $reviews = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $reviews[] = $row;
    }
    return $reviews;
}

function get_average_rating($product_id) {
    global $conn;
    $product_id = (int)$product_id;
    $query = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
              FROM reviews 
              WHERE product_id = $product_id AND status = 'approved'";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}
?>
