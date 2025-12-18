<div class="container-fluid mt-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>Manage Orders</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo $order['order_id']; ?></td>
                                <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                <td><?php echo date('Y-m-d H:i', strtotime($order['order_date'])); ?></td>
                                <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td><span class="badge bg-secondary"><?php echo $order['status']; ?></span></td>
                                <td><?php echo $order['payment_method']; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No orders found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <a href="<?php echo BASE_URL; ?>?page=admin&action=dashboard" class="btn btn-outline-secondary mt-3">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

ob_start();
require APP_PATH . '/Views/layout.php';
?>
