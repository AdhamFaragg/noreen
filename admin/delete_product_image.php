<?php
require_once '../db/config.php';
require_once '../includes/functions.php';
require_once '../includes/product_images_functions.php';

require_login();
require_admin();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Unknown error'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image_id'])) {
    $image_id = (int)$_POST['image_id'];
    
    // Verify the image exists and get product_id
    $verify = mysqli_query($conn, "SELECT product_id FROM product_images WHERE image_id = $image_id");
    if (mysqli_num_rows($verify) === 0) {
        $response['message'] = 'Image not found';
        echo json_encode($response);
        exit;
    }
    
    if (delete_product_image($image_id)) {
        $response['success'] = true;
        $response['message'] = 'Image deleted successfully';
    } else {
        $response['message'] = 'Error deleting image from database';
    }
} else {
    $response['message'] = 'Invalid request';
}

echo json_encode($response);
?>
