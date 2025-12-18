<?php
require_once '../db/config.php';
require_once '../includes/functions.php';

require_login();
require_admin();

// Handle delete
if (isset($_GET['delete'])) {
    $product_id = (int)$_GET['delete'];
    $product = get_product_by_id($product_id);
    
    if ($product) {
        // Delete product image
        delete_image($product['image']);
        
        $query = "DELETE FROM products WHERE product_id = $product_id";
        if (mysqli_query($conn, $query)) {
            set_message('Product deleted successfully!', 'success');
        } else {
            set_message('Error deleting product.', 'error');
        }
    }
    redirect(BASE_URL . 'admin/manage_products.php');
}

// Get all products
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : null;

$query = "SELECT p.*, c.category_name FROM products p 
          LEFT JOIN categories c ON p.category_id = c.category_id WHERE 1=1";

if (!empty($search)) {
    $query .= " AND (p.product_name LIKE '%$search%' OR p.description LIKE '%$search%')";
}

if ($category_filter) {
    $query .= " AND p.category_id = $category_filter";
}

$query .= " ORDER BY p.created_at DESC";
$result = mysqli_query($conn, $query);

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
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="manage_products.php">
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
                <h1 class="h2">Manage Products</h1>
                <a href="add_product.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Product
                </a>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="search" placeholder="Search products..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" name="category">
                                <option value="">All Categories</option>
                                <?php
                                $categories = get_all_categories();
                                foreach ($categories as $cat) {
                                    $selected = ($category_filter == $cat['category_id']) ? 'selected' : '';
                                    echo '<option value="' . $cat['category_id'] . '" ' . $selected . '>' . htmlspecialchars($cat['category_name']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Products Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Featured</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($product = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td>
                                        <?php if ($product['image']): ?>
                                            <img src="<?php echo UPLOAD_URL . $product['image']; ?>" 
                                                 alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <div style="width: 50px; height: 50px; background: #ddd;"></div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                    <td>
                                        <?php if ($product['discount_price']): ?>
                                            <span class="text-muted text-decoration-line-through"><?php echo format_price($product['price']); ?></span><br>
                                            <strong class="text-danger"><?php echo format_price($product['discount_price']); ?></strong>
                                        <?php else: ?>
                                            <?php echo format_price($product['price']); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($product['stock'] < 10): ?>
                                            <span class="badge bg-danger"><?php echo $product['stock']; ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><?php echo $product['stock']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $product['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                            <?php echo ucfirst($product['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($product['featured']): ?>
                                            <i class="fas fa-star text-warning"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" 
                                           class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?delete=<?php echo $product['product_id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this product?');" 
                                           title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php if (mysqli_num_rows($result) === 0): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                        <p>No products found.</p>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
