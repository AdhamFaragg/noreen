<?php
require_once '../db/config.php';
require_once '../includes/functions.php';

require_login();
require_admin();

// Get statistics
$stats_query = "SELECT 
    (SELECT COUNT(*) FROM products WHERE status = 'active') as total_products,
    (SELECT COUNT(*) FROM users WHERE role = 'customer') as total_customers,
    (SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()) as today_orders,
    (SELECT IFNULL(SUM(total_amount), 0) FROM orders WHERE DATE(created_at) = CURDATE()) as today_sales,
    (SELECT COUNT(*) FROM orders WHERE order_status = 'pending') as pending_orders,
    (SELECT IFNULL(SUM(total_amount), 0) FROM orders WHERE MONTH(created_at) = MONTH(CURDATE())) as monthly_sales";

$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

// Get recent orders
$recent_orders_query = "SELECT o.*, u.full_name, u.email 
                        FROM orders o 
                        JOIN users u ON o.user_id = u.user_id 
                        ORDER BY o.created_at DESC 
                        LIMIT 10";
$recent_orders = mysqli_query($conn, $recent_orders_query);

// Get low stock products
$low_stock_query = "SELECT * FROM products WHERE stock < 10 AND status = 'active' ORDER BY stock ASC LIMIT 10";
$low_stock = mysqli_query($conn, $low_stock_query);

include '../includes/header.php';
?>

<div class="container-fluid mt-4">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-md-block bg-light sidebar">
            <div class="position-sticky pt-3">
                <h6 class="sidebar-heading px-3 mt-2 mb-3 text-muted">
                    <i class="fas fa-tachometer-alt"></i> ADMIN PANEL
                </h6>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_products.php">
                            <i class="fas fa-box"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_categories.php">
                            <i class="fas fa-tags"></i> Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_orders.php">
                            <i class="fas fa-shopping-cart"></i> Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_users.php">
                            <i class="fas fa-users"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_discounts.php">
                            <i class="fas fa-percentage"></i> Discounts
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reports.php">
                            <i class="fas fa-chart-bar"></i> Reports
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Total Products</h6>
                                    <h2 class="mb-0"><?php echo $stats['total_products']; ?></h2>
                                </div>
                                <i class="fas fa-box fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Total Customers</h6>
                                    <h2 class="mb-0"><?php echo $stats['total_customers']; ?></h2>
                                </div>
                                <i class="fas fa-users fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Today's Orders</h6>
                                    <h2 class="mb-0"><?php echo $stats['today_orders']; ?></h2>
                                </div>
                                <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Today's Sales</h6>
                                    <h2 class="mb-0"><?php echo format_price($stats['today_sales']); ?></h2>
                                </div>
                                <i class="fas fa-dollar-sign fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Stats -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="card border-danger">
                        <div class="card-body">
                            <h6 class="card-title text-danger">
                                <i class="fas fa-exclamation-triangle"></i> Pending Orders
                            </h6>
                            <h3 class="mb-0"><?php echo $stats['pending_orders']; ?></h3>
                            <a href="manage_orders.php?status=pending" class="btn btn-sm btn-outline-danger mt-2">View Orders</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-8 mb-3">
                    <div class="card border-info">
                        <div class="card-body">
                            <h6 class="card-title text-info">
                                <i class="fas fa-chart-line"></i> Monthly Sales
                            </h6>
                            <h3 class="mb-0"><?php echo format_price($stats['monthly_sales']); ?></h3>
                            <a href="reports.php" class="btn btn-sm btn-outline-info mt-2">View Reports</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart"></i> Recent Orders</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($order = mysqli_fetch_assoc($recent_orders)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                                    <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                    <td><?php echo format_price($order['total_amount']); ?></td>
                                    <td>
                                        <?php
                                        $badge_class = 'secondary';
                                        switch ($order['order_status']) {
                                            case 'pending': $badge_class = 'warning'; break;
                                            case 'processing': $badge_class = 'info'; break;
                                            case 'shipped': $badge_class = 'primary'; break;
                                            case 'delivered': $badge_class = 'success'; break;
                                            case 'cancelled': $badge_class = 'danger'; break;
                                        }
                                        ?>
                                        <span class="badge bg-<?php echo $badge_class; ?>">
                                            <?php echo ucfirst($order['order_status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $order['payment_status'] === 'paid' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($order['payment_status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <a href="view_order.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Low Stock Products -->
            <?php if (mysqli_num_rows($low_stock) > 0): ?>
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Low Stock Alert</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Stock</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($product = mysqli_fetch_assoc($low_stock)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                    <td>
                                        <?php 
                                        $cat = get_category_by_id($product['category_id']);
                                        echo htmlspecialchars($cat['category_name']);
                                        ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger"><?php echo $product['stock']; ?> left</span>
                                    </td>
                                    <td>
                                        <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Update Stock
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<style>
.sidebar {
    position: fixed;
    top: 56px;
    bottom: 0;
    left: 0;
    z-index: 100;
    padding: 0;
    box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
    overflow-y: auto;
}

.sidebar .nav-link {
    font-weight: 500;
    color: #333;
    padding: 12px 20px;
}

.sidebar .nav-link:hover {
    background-color: #e9ecef;
    color: #007bff;
}

.sidebar .nav-link.active {
    color: #007bff;
    background-color: #e7f3ff;
    border-left: 3px solid #007bff;
}

.sidebar .nav-link i {
    margin-right: 8px;
}

main {
    margin-top: 56px;
}
</style>

<?php include '../includes/footer.php'; ?>
