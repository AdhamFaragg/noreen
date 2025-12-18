<?php
require_once '../db/config.php';
require_once '../includes/functions.php';
require_login();
require_admin();

// Handle status update
if (isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $order_status = sanitize_input($_POST['order_status']);
    $payment_status = sanitize_input($_POST['payment_status']);
    
    $query = "UPDATE orders SET order_status = '$order_status', payment_status = '$payment_status' WHERE order_id = $order_id";
    if (mysqli_query($conn, $query)) {
        set_message('Order status updated!', 'success');
    }
}

$status_filter = isset($_GET['status']) ? sanitize_input($_GET['status']) : '';
$query = "SELECT o.*, u.full_name FROM orders o JOIN users u ON o.user_id = u.user_id WHERE 1=1";
if ($status_filter) {
    $query .= " AND o.order_status = '$status_filter'";
}
$query .= " ORDER BY o.created_at DESC";
$result = mysqli_query($conn, $query);

include '../includes/header.php';
?>

<div class="container-fluid mt-4">
    <div class="row">
        <nav class="col-md-2 d-md-block bg-light sidebar">
            <div class="position-sticky pt-3">
                <h6 class="sidebar-heading px-3 mt-2 mb-3 text-muted"><i class="fas fa-tachometer-alt"></i> ADMIN PANEL</h6>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_products.php"><i class="fas fa-box"></i> Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_categories.php"><i class="fas fa-tags"></i> Categories</a></li>
                    <li class="nav-item"><a class="nav-link active" href="manage_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_users.php"><i class="fas fa-users"></i> Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_discounts.php"><i class="fas fa-percentage"></i> Discounts</a></li>
                    <li class="nav-item"><a class="nav-link" href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                </ul>
            </div>
        </nav>

        <main class="col-md-10 ms-sm-auto px-md-4">
            <div class="d-flex justify-content-between pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manage Orders</h1>
            </div>

            <div class="btn-group mb-3" role="group">
                <a href="manage_orders.php" class="btn btn-<?php echo empty($status_filter) ? 'primary' : 'outline-primary'; ?>">All</a>
                <a href="?status=pending" class="btn btn-<?php echo $status_filter === 'pending' ? 'warning' : 'outline-warning'; ?>">Pending</a>
                <a href="?status=processing" class="btn btn-<?php echo $status_filter === 'processing' ? 'info' : 'outline-info'; ?>">Processing</a>
                <a href="?status=shipped" class="btn btn-<?php echo $status_filter === 'shipped' ? 'primary' : 'outline-primary'; ?>">Shipped</a>
                <a href="?status=delivered" class="btn btn-<?php echo $status_filter === 'delivered' ? 'success' : 'outline-success'; ?>">Delivered</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr><th>Order #</th><th>Customer</th><th>Amount</th><th>Status</th><th>Payment</th><th>Date</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php while ($order = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                                <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                <td><?php echo format_price($order['total_amount']); ?></td>
                                <td>
                                    <?php
                                    $badge_class = ['pending' => 'warning', 'processing' => 'info', 'shipped' => 'primary', 'delivered' => 'success', 'cancelled' => 'danger'][$order['order_status']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?php echo $badge_class; ?>"><?php echo ucfirst($order['order_status']); ?></span>
                                </td>
                                <td><span class="badge bg-<?php echo $order['payment_status'] === 'paid' ? 'success' : 'warning'; ?>"><?php echo ucfirst($order['payment_status']); ?></span></td>
                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="viewOrder(<?php echo $order['order_id']; ?>)"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-sm btn-warning" onclick="editOrder(<?php echo htmlspecialchars(json_encode($order)); ?>)"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Edit Order Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Update Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="order_id" id="order_id">
                    <input type="hidden" name="update_status" value="1">
                    <div class="mb-3">
                        <label class="form-label">Order Status</label>
                        <select class="form-select" name="order_status" id="order_status">
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Status</label>
                        <select class="form-select" name="payment_status" id="payment_status">
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editOrder(order) {
    document.getElementById('order_id').value = order.order_id;
    document.getElementById('order_status').value = order.order_status;
    document.getElementById('payment_status').value = order.payment_status;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
function viewOrder(id) {
    window.location.href = 'view_order.php?id=' + id;
}
</script>

<style>.sidebar {position: fixed; top: 56px; bottom: 0; left: 0; z-index: 100; overflow-y: auto;}
.sidebar .nav-link {padding: 12px 20px; color: #333;}
.sidebar .nav-link:hover {background-color: #e9ecef; color: #007bff;}
.sidebar .nav-link.active {color: #007bff; background-color: #e7f3ff; border-left: 3px solid #007bff;}
main {margin-top: 56px;}</style>

<?php include '../includes/footer.php'; ?>
