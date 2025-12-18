<?php
require_once '../db/config.php';
require_once '../includes/functions.php';
require_login();
require_admin();

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $discount_id = isset($_POST['discount_id']) ? (int)$_POST['discount_id'] : 0;
    $code = strtoupper(sanitize_input($_POST['code']));
    $description = sanitize_input($_POST['description']);
    $discount_type = sanitize_input($_POST['discount_type']);
    $discount_value = (float)$_POST['discount_value'];
    $min_purchase = (float)$_POST['min_purchase'];
    $start_date = sanitize_input($_POST['start_date']);
    $end_date = sanitize_input($_POST['end_date']);
    $status = sanitize_input($_POST['status']);
    
    if ($discount_id > 0) {
        $query = "UPDATE discounts SET code='$code', description='$description', discount_type='$discount_type', discount_value=$discount_value, min_purchase=$min_purchase, start_date='$start_date', end_date='$end_date', status='$status' WHERE discount_id=$discount_id";
    } else {
        $query = "INSERT INTO discounts (code, description, discount_type, discount_value, min_purchase, start_date, end_date, status) VALUES ('$code', '$description', '$discount_type', $discount_value, $min_purchase, '$start_date', '$end_date', '$status')";
    }
    
    if (mysqli_query($conn, $query)) {
        set_message('Discount code saved!', 'success');
        redirect(BASE_URL . 'admin/manage_discounts.php');
    }
}

$result = mysqli_query($conn, "SELECT * FROM discounts ORDER BY created_at DESC");
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
                    <li class="nav-item"><a class="nav-link active" href="manage_discounts.php"><i class="fas fa-percentage"></i> Discounts</a></li>
                    <li class="nav-item"><a class="nav-link" href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                </ul>
            </div>
        </nav>

        <main class="col-md-10 ms-sm-auto px-md-4">
            <div class="d-flex justify-content-between pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manage Discount Codes</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus"></i> Add Discount</button>
            </div>

            <div class="card">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr><th>Code</th><th>Type</th><th>Value</th><th>Min Purchase</th><th>Valid Until</th><th>Status</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php while ($discount = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($discount['code']); ?></strong></td>
                                <td><?php echo ucfirst($discount['discount_type']); ?></td>
                                <td><?php echo $discount['discount_type'] === 'percentage' ? $discount['discount_value'] . '%' : format_price($discount['discount_value']); ?></td>
                                <td><?php echo format_price($discount['min_purchase']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($discount['end_date'])); ?></td>
                                <td><span class="badge bg-<?php echo $discount['status'] === 'active' ? 'success' : 'secondary'; ?>"><?php echo ucfirst($discount['status']); ?></span></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick='editDiscount(<?php echo json_encode($discount); ?>)'><i class="fas fa-edit"></i></button>
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

<!-- Add/Edit Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Discount Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="discount_id" id="discount_id">
                    <div class="mb-3">
                        <label class="form-label">Code *</label>
                        <input type="text" class="form-control" name="code" id="code" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" class="form-control" name="description" id="description">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-select" name="discount_type" id="discount_type">
                                <option value="percentage">Percentage</option>
                                <option value="fixed">Fixed Amount</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Value *</label>
                            <input type="number" step="0.01" class="form-control" name="discount_value" id="discount_value" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Minimum Purchase</label>
                        <input type="number" step="0.01" class="form-control" name="min_purchase" id="min_purchase" value="0">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date" id="start_date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date" id="end_date">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editDiscount(d) {
    document.getElementById('modalTitle').textContent = 'Edit Discount';
    document.getElementById('discount_id').value = d.discount_id;
    document.getElementById('code').value = d.code;
    document.getElementById('description').value = d.description;
    document.getElementById('discount_type').value = d.discount_type;
    document.getElementById('discount_value').value = d.discount_value;
    document.getElementById('min_purchase').value = d.min_purchase;
    document.getElementById('start_date').value = d.start_date;
    document.getElementById('end_date').value = d.end_date;
    document.getElementById('status').value = d.status;
    new bootstrap.Modal(document.getElementById('addModal')).show();
}
</script>

<style>.sidebar {position: fixed; top: 56px; bottom: 0; left: 0; z-index: 100; overflow-y: auto;}
.sidebar .nav-link {padding: 12px 20px; color: #333;}
.sidebar .nav-link:hover {background-color: #e9ecef; color: #007bff;}
.sidebar .nav-link.active {color: #007bff; background-color: #e7f3ff; border-left: 3px solid #007bff;}
main {margin-top: 56px;}</style>

<?php include '../includes/footer.php'; ?>
