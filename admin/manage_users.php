<?php
require_once '../db/config.php';
require_once '../includes/functions.php';
require_login();
require_admin();

$result = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
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
                    <li class="nav-item"><a class="nav-link active" href="manage_users.php"><i class="fas fa-users"></i> Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_discounts.php"><i class="fas fa-percentage"></i> Discounts</a></li>
                    <li class="nav-item"><a class="nav-link" href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                </ul>
            </div>
        </nav>

        <main class="col-md-10 ms-sm-auto px-md-4">
            <div class="d-flex justify-content-between pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manage Users</h1>
            </div>

            <div class="card">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr><th>Username</th><th>Full Name</th><th>Email</th><th>Role</th><th>Status</th><th>Joined</th></tr>
                        </thead>
                        <tbody>
                            <?php while ($user = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><span class="badge bg-primary"><?php echo ucfirst($user['role']); ?></span></td>
                                <td><span class="badge bg-<?php echo $user['status'] === 'active' ? 'success' : 'danger'; ?>"><?php echo ucfirst($user['status']); ?></span></td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
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
