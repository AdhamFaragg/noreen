<?php
// Staff panel for order management
require_once '../db/config.php';
require_once '../includes/functions.php';

require_login();
require_staff();

// Handle status update
if (isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $order_status = sanitize_input($_POST['order_status']);
    
    $query = "UPDATE orders SET order_status = '$order_status' WHERE order_id = $order_id";
    if (mysqli_query($conn, $query)) {
        set_message('Order status updated!', 'success');
    }
}

$status_filter = isset($_GET['status']) ? sanitize_input($_GET['status']) : 'pending';
$query = "SELECT o.*, u.full_name FROM orders o 
          JOIN users u ON o.user_id = u.user_id 
          WHERE o.order_status = '$status_filter' 
          ORDER BY o.created_at DESC";
$result = mysqli_query($conn, $query);

include '../includes/header.php';
?>

<div class="container mt-4 mb-5">
    <h2 class="mb-4"><i class="fas fa-tasks"></i> Order Management (Staff Panel)</h2>

    <div class="btn-group mb-3" role="group">
        <a href="?status=pending" class="btn btn-<?php echo $status_filter === 'pending' ? 'warning' : 'outline-warning'; ?>">Pending</a>
        <a href="?status=processing" class="btn btn-<?php echo $status_filter === 'processing' ? 'info' : 'outline-info'; ?>">Processing</a>
        <a href="?status=shipped" class="btn btn-<?php echo $status_filter === 'shipped' ? 'primary' : 'outline-primary'; ?>">Shipped</a>
        <a href="?status=delivered" class="btn btn-<?php echo $status_filter === 'delivered' ? 'success' : 'outline-success'; ?>">Delivered</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                        <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                        <td><?php echo format_price($order['total_amount']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                <input type="hidden" name="update_status" value="1">
                                <select class="form-select form-select-sm d-inline-block" name="order_status" style="width: auto;" onchange="this.form.submit()">
                                    <option value="pending" <?php echo $order['order_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo $order['order_status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo $order['order_status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo $order['order_status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if (mysqli_num_rows($result) === 0): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4">No orders found in this status.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
