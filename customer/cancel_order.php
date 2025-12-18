<?php
require_once '../db/config.php';
require_once '../includes/functions.php';

require_login();

if (!isset($_GET['id'])) {
    set_message('Invalid order ID.', 'error');
    redirect(BASE_URL . 'customer/orders.php');
}

$order_id = (int)$_GET['id'];
$user_id = (int)$_SESSION['user_id'];

// Verify the order belongs to the user
$order_query = "SELECT * FROM orders WHERE order_id = $order_id AND user_id = $user_id";
$order_result = mysqli_query($conn, $order_query);

if (mysqli_num_rows($order_result) === 0) {
    set_message('Order not found or you do not have permission to cancel it.', 'error');
    redirect(BASE_URL . 'customer/orders.php');
}

$order = mysqli_fetch_assoc($order_result);

// Only allow cancellation of pending orders
if ($order['order_status'] !== 'pending') {
    set_message('Only pending orders can be cancelled.', 'error');
    redirect(BASE_URL . 'customer/orders.php');
}

// Get order items to restore stock
$items_query = "SELECT * FROM order_items WHERE order_id = $order_id";
$items_result = mysqli_query($conn, $items_query);

// Update order status
$update_query = "UPDATE orders SET order_status = 'cancelled' WHERE order_id = $order_id";
if (mysqli_query($conn, $update_query)) {
    // Restore product stock
    while ($item = mysqli_fetch_assoc($items_result)) {
        $restore_query = "UPDATE products SET stock = stock + {$item['quantity']} WHERE product_id = {$item['product_id']}";
        mysqli_query($conn, $restore_query);
    }
    
    // If discount was used, decrement usage count
    if ($order['discount_code']) {
        $discount_query = "UPDATE discounts SET used_count = used_count - 1 WHERE code = '{$order['discount_code']}'";
        mysqli_query($conn, $discount_query);
    }
    
    set_message('Order cancelled successfully.', 'success');
} else {
    set_message('Error cancelling order. Please try again.', 'error');
}

redirect(BASE_URL . 'customer/orders.php');
?>
