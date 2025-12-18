<?php
require_once '../db/config.php';
require_once '../includes/functions.php';
require_login();
require_admin();

$period = isset($_GET['period']) ? $_GET['period'] : 'monthly';

// Sales data
switch ($period) {
    case 'daily':
        $query = "SELECT DATE(created_at) as date, COUNT(*) as orders, SUM(total_amount) as sales FROM orders WHERE DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) GROUP BY DATE(created_at) ORDER BY date DESC";
        break;
    case 'weekly':
        $query = "SELECT WEEK(created_at) as week, YEAR(created_at) as year, COUNT(*) as orders, SUM(total_amount) as sales FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 WEEK) GROUP BY YEAR(created_at), WEEK(created_at) ORDER BY year DESC, week DESC";
        break;
    default:
        $query = "SELECT MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as orders, SUM(total_amount) as sales FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH) GROUP BY YEAR(created_at), MONTH(created_at) ORDER BY year DESC, month DESC";
}

$result = mysqli_query($conn, $query);

// Top products
$top_products_query = "SELECT p.product_name, SUM(oi.quantity) as total_sold, SUM(oi.subtotal) as revenue 
                       FROM order_items oi 
                       JOIN products p ON oi.product_id = p.product_id 
                       GROUP BY oi.product_id 
                       ORDER BY total_sold DESC LIMIT 10";
$top_products = mysqli_query($conn, $top_products_query);

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
                    <li class="nav-item"><a class="nav-link" href="manage_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_users.php"><i class="fas fa-users"></i> Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_discounts.php"><i class="fas fa-percentage"></i> Discounts</a></li>
                    <li class="nav-item"><a class="nav-link active" href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                </ul>
            </div>
        </nav>

        <main class="col-md-10 ms-sm-auto px-md-4">
            <div class="d-flex justify-content-between pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Sales Reports</h1>
            </div>

            <div class="btn-group mb-3" role="group">
                <a href="?period=daily" class="btn btn-<?php echo $period === 'daily' ? 'primary' : 'outline-primary'; ?>">Daily</a>
                <a href="?period=weekly" class="btn btn-<?php echo $period === 'weekly' ? 'primary' : 'outline-primary'; ?>">Weekly</a>
                <a href="?period=monthly" class="btn btn-<?php echo $period === 'monthly' ? 'primary' : 'outline-primary'; ?>">Monthly</a>
            </div>

            <div class="row">
                <div class="col-md-7 mb-4">
                    <div class="card">
                        <div class="card-header"><h5><?php echo ucfirst($period); ?> Sales Report</h5></div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr><th>Period</th><th>Orders</th><th>Sales</th></tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total_orders = 0;
                                    $total_sales = 0;
                                    while ($row = mysqli_fetch_assoc($result)): 
                                        $total_orders += $row['orders'];
                                        $total_sales += $row['sales'];
                                    ?>
                                    <tr>
                                        <td>
                                            <?php 
                                            if ($period === 'monthly') {
                                                echo date('F Y', mktime(0, 0, 0, $row['month'], 1, $row['year']));
                                            } elseif ($period === 'weekly') {
                                                echo 'Week ' . $row['week'] . ', ' . $row['year'];
                                            } else {
                                                echo date('M d, Y', strtotime($row['date']));
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo $row['orders']; ?></td>
                                        <td><?php echo format_price($row['sales']); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <tr class="fw-bold">
                                        <td>TOTAL</td>
                                        <td><?php echo $total_orders; ?></td>
                                        <td><?php echo format_price($total_sales); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-5 mb-4">
                    <div class="card">
                        <div class="card-header"><h5>Top Selling Products</h5></div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <thead>
                                    <tr><th>Product</th><th>Sold</th><th>Revenue</th></tr>
                                </thead>
                                <tbody>
                                    <?php while ($product = mysqli_fetch_assoc($top_products)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars(truncate_text($product['product_name'], 30)); ?></td>
                                        <td><?php echo $product['total_sold']; ?></td>
                                        <td><?php echo format_price($product['revenue']); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<style>.sidebar {position: fixed; top: 56px; bottom: 0; left: 0; z-index: 100; overflow-y: auto;}
.sidebar .nav-link {padding: 12px 20px; color: #333;}
.sidebar .nav-link:hover {background-color: #e9ecef; color: #007bff;}
.sidebar .nav-link.active {color: #007bff; background-color: #e7f3ff; border-left: 3px solid #007bff;}
main {margin-top: 56px;}</style>

<?php include '../includes/footer.php'; ?>
