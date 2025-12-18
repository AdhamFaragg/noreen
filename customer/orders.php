<?php
require_once '../db/config.php';
require_once '../includes/functions.php';

require_login();

$query = "SELECT * FROM orders WHERE user_id = {$_SESSION['user_id']} ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

include '../includes/header.php';
?>

<div class="container mt-4 mb-5">
    <h2 class="mb-4"><i class="fas fa-box"></i> My Orders</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
    <div class="row">
        <?php while ($order = mysqli_fetch_assoc($result)): ?>
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Order #<?php echo htmlspecialchars($order['order_number']); ?></strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Placed on: <?php echo date('M d, Y', strtotime($order['created_at'])); ?></small>
                        </div>
                        <div class="col-md-3">
                            <?php
                            $status_badge = [
                                'pending' => 'warning',
                                'processing' => 'info',
                                'shipped' => 'primary',
                                'delivered' => 'success',
                                'cancelled' => 'danger'
                            ][$order['order_status']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?php echo $status_badge; ?>">
                                <?php echo ucfirst($order['order_status']); ?>
                            </span>
                        </div>
                        <div class="col-md-3 text-end">
                            <strong><?php echo format_price($order['total_amount']); ?></strong>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php
                    // Get order items
                    $items_query = "SELECT * FROM order_items WHERE order_id = {$order['order_id']}";
                    $items_result = mysqli_query($conn, $items_query);
                    ?>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                            <div class="d-flex align-items-center mb-2">
                                <div class="me-3">
                                    <i class="fas fa-box text-muted"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        Qty: <?php echo $item['quantity']; ?>
                                        <?php if ($item['size']): ?> | Size: <?php echo htmlspecialchars($item['size']); ?><?php endif; ?>
                                        <?php if ($item['color']): ?> | Color: <?php echo htmlspecialchars($item['color']); ?><?php endif; ?>
                                    </small>
                                </div>
                                <div>
                                    <?php echo format_price($item['subtotal']); ?>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <div class="col-md-4">
                            <div class="text-end">
                                <p class="mb-1"><strong>Payment Method:</strong> <?php echo strtoupper($order['payment_method']); ?></p>
                                <p class="mb-1">
                                    <strong>Payment Status:</strong> 
                                    <span class="badge bg-<?php echo $order['payment_status'] === 'paid' ? 'success' : 'warning'; ?>">
                                        <?php echo ucfirst($order['payment_status']); ?>
                                    </span>
                                </p>
                                <?php if ($order['discount_amount'] > 0): ?>
                                <p class="mb-1 text-success"><strong>Discount Applied:</strong> -<?php echo format_price($order['discount_amount']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h6>Shipping Address:</h6>
                            <p class="mb-0">
                                <?php echo htmlspecialchars($order['shipping_address']); ?><br>
                                <?php if ($order['shipping_city']): ?>
                                    <?php echo htmlspecialchars($order['shipping_city']); ?>, <?php echo htmlspecialchars($order['shipping_postal_code']); ?><br>
                                <?php endif; ?>
                                Phone: <?php echo htmlspecialchars($order['shipping_phone']); ?>
                            </p>
                        </div>
                        <div class="col-md-6 text-end">
                            <?php if ($order['order_status'] === 'pending'): ?>
                                <button class="btn btn-sm btn-danger" onclick="if(confirm('Cancel this order?')) window.location.href='cancel_order.php?id=<?php echo $order['order_id']; ?>'">
                                    <i class="fas fa-times"></i> Cancel Order
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="text-center py-5">
        <i class="fas fa-box-open fa-5x text-muted mb-3"></i>
        <h4>No orders yet</h4>
        <p class="text-muted">Start shopping to place your first order!</p>
        <a href="index.php" class="btn btn-primary">
            <i class="fas fa-shopping-bag"></i> Browse Products
        </a>
    </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
