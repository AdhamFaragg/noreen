<?php
/**
 * Product Images Management Functions
 */

/**
 * Get all images for a product
 */
function get_product_images($product_id) {
    global $conn;
    $product_id = (int)$product_id;
    
    // Check if table exists first
    $table_check = mysqli_query($conn, "SHOW TABLES LIKE 'product_images'");
    if (mysqli_num_rows($table_check) === 0) {
        return false; // Table doesn't exist, return false
    }
    
    $query = "SELECT * FROM product_images WHERE product_id = $product_id ORDER BY image_order ASC";
    $result = mysqli_query($conn, $query);
    return $result;
}

/**
 * Add image to product
 */
function add_product_image($product_id, $image_path, $is_primary = 0, $image_order = 0) {
    global $conn;
    $product_id = (int)$product_id;
    $image_path = sanitize_input($image_path);
    $is_primary = (int)$is_primary;
    $image_order = (int)$image_order;
    
    if ($is_primary) {
        // If this is primary, unset other primary images
        mysqli_query($conn, "UPDATE product_images SET is_primary = 0 WHERE product_id = $product_id");
    }
    
    $query = "INSERT INTO product_images (product_id, image_path, is_primary, image_order) 
              VALUES ($product_id, '$image_path', $is_primary, $image_order)";
    return mysqli_query($conn, $query);
}

/**
 * Delete product image
 */
function delete_product_image($image_id) {
    global $conn;
    
    // Get the image details first
    $image_id = (int)$image_id;
    $result = mysqli_query($conn, "SELECT * FROM product_images WHERE image_id = $image_id");
    $image = mysqli_fetch_assoc($result);
    
    if ($image) {
        // Delete from filesystem
        $upload_dir = UPLOAD_DIR;
        $file_path = $upload_dir . $image['image_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Delete from database
        return mysqli_query($conn, "DELETE FROM product_images WHERE image_id = $image_id");
    }
    
    return false;
}

/**
 * Update image order
 */
function update_image_order($image_id, $new_order) {
    global $conn;
    $image_id = (int)$image_id;
    $new_order = (int)$new_order;
    
    return mysqli_query($conn, "UPDATE product_images SET image_order = $new_order WHERE image_id = $image_id");
}

/**
 * Handle multiple file uploads
 */
function handle_product_image_uploads($product_id, $files) {
    global $conn;
    $product_id = (int)$product_id;
    $uploaded_files = [];
    
    if (!is_array($files['name'])) {
        $files = array(
            'name' => array($files['name']),
            'type' => array($files['type']),
            'tmp_name' => array($files['tmp_name']),
            'error' => array($files['error']),
            'size' => array($files['size'])
        );
    }
    
    $upload_dir = UPLOAD_DIR;
    
    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_OK) {
            $filename = time() . '_' . basename($files['name'][$i]);
            $target = $upload_dir . $filename;
            
            if (move_uploaded_file($files['tmp_name'][$i], $target)) {
                add_product_image($product_id, $filename, 0, $i);
                $uploaded_files[] = $filename;
            }
        }
    }
    
    return $uploaded_files;
}
?>
