<?php
require_once '../db/config.php';
require_once '../includes/functions.php';
require_login();
require_admin();

// Handle delete
if (isset($_GET['delete'])) {
    $category_id = (int)$_GET['delete'];
    $query = "DELETE FROM categories WHERE category_id = $category_id";
    if (mysqli_query($conn, $query)) {
        set_message('Category deleted successfully!', 'success');
    }
    redirect(BASE_URL . 'admin/manage_categories.php');
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $category_name = sanitize_input($_POST['category_name']);
    $description = sanitize_input($_POST['description']);
    $status = sanitize_input($_POST['status']);
    
    if ($category_id > 0) {
        $query = "UPDATE categories SET category_name = '$category_name', description = '$description', status = '$status' WHERE category_id = $category_id";
        $msg = 'Category updated successfully!';
    } else {
        $query = "INSERT INTO categories (category_name, description, status) VALUES ('$category_name', '$description', '$status')";
        $msg = 'Category added successfully!';
    }
    
    if (mysqli_query($conn, $query)) {
        set_message($msg, 'success');
        redirect(BASE_URL . 'admin/manage_categories.php');
    }
}

$result = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_name");
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
                    <li class="nav-item"><a class="nav-link active" href="manage_categories.php"><i class="fas fa-tags"></i> Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_users.php"><i class="fas fa-users"></i> Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_discounts.php"><i class="fas fa-percentage"></i> Discounts</a></li>
                    <li class="nav-item"><a class="nav-link" href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                </ul>
            </div>
        </nav>

        <main class="col-md-10 ms-sm-auto px-md-4">
            <div class="d-flex justify-content-between pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manage Categories</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus"></i> Add Category</button>
            </div>

            <div class="card">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr><th>Category Name</th><th>Description</th><th>Status</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php while ($cat = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cat['category_name']); ?></td>
                                <td><?php echo htmlspecialchars($cat['description']); ?></td>
                                <td><span class="badge bg-<?php echo $cat['status'] === 'active' ? 'success' : 'secondary'; ?>"><?php echo ucfirst($cat['status']); ?></span></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="editCategory(<?php echo htmlspecialchars(json_encode($cat)); ?>)"><i class="fas fa-edit"></i></button>
                                    <a href="?delete=<?php echo $cat['category_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?');"><i class="fas fa-trash"></i></a>
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
                    <h5 class="modal-title" id="modalTitle">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="category_id" id="category_id">
                    <div class="mb-3">
                        <label class="form-label">Category Name *</label>
                        <input type="text" class="form-control" name="category_name" id="category_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="description" rows="3"></textarea>
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
function editCategory(cat) {
    document.getElementById('modalTitle').textContent = 'Edit Category';
    document.getElementById('category_id').value = cat.category_id;
    document.getElementById('category_name').value = cat.category_name;
    document.getElementById('description').value = cat.description;
    document.getElementById('status').value = cat.status;
    new bootstrap.Modal(document.getElementById('addModal')).show();
}
</script>

<style>.sidebar {position: fixed; top: 56px; bottom: 0; left: 0; z-index: 100; overflow-y: auto;}
.sidebar .nav-link {padding: 12px 20px; color: #333;}
.sidebar .nav-link:hover {background-color: #e9ecef; color: #007bff;}
.sidebar .nav-link.active {color: #007bff; background-color: #e7f3ff; border-left: 3px solid #007bff;}
main {margin-top: 56px;}</style>

<?php include '../includes/footer.php'; ?>
