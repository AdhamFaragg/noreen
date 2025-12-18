<?php
require_once '../db/config.php';
require_once '../includes/functions.php';

require_login();
require_admin();

$errors = [];
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit = $product_id > 0;

if ($is_edit) {
    $product = get_product_by_id($product_id);
    if (!$product) {
        set_message('Product not found.', 'error');
        redirect(BASE_URL . 'admin/manage_products.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = (int)$_POST['category_id'];
    $product_name = sanitize_input($_POST['product_name']);
    $description = sanitize_input($_POST['description']);
    $price = (float)$_POST['price'];
    $discount_price = !empty($_POST['discount_price']) ? (float)$_POST['discount_price'] : null;
    $stock = (int)$_POST['stock'];
    $sizes = sanitize_input($_POST['sizes']);
    $colors = sanitize_input($_POST['colors']);
    $status = sanitize_input($_POST['status']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    // Validation
    if (empty($product_name) || empty($price) || empty($category_id)) {
        $errors[] = 'Please fill in all required fields.';
    }
    
    // Handle image upload
    $image_filename = $is_edit ? $product['image'] : '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_result = upload_image($_FILES['image'], 'product_');
        if ($upload_result['success']) {
            // Delete old image
            if ($is_edit && !empty($product['image'])) {
                delete_image($product['image']);
            }
            $image_filename = $upload_result['filename'];
        } else {
            $errors[] = $upload_result['message'];
        }
    }
    
    if (empty($errors)) {
        if ($is_edit) {
            $query = "UPDATE products SET 
                      category_id = $category_id,
                      product_name = '$product_name',
                      description = '$description',
                      price = $price,
                      discount_price = " . ($discount_price ? $discount_price : 'NULL') . ",
                      stock = $stock,
                      sizes = '$sizes',
                      colors = '$colors',
                      status = '$status',
                      featured = $featured";
            
            if (!empty($image_filename)) {
                $query .= ", image = '$image_filename'";
            }
            
            $query .= " WHERE product_id = $product_id";
            
            if (mysqli_query($conn, $query)) {
                set_message('Product updated successfully!', 'success');
                redirect(BASE_URL . 'admin/manage_products.php');
            } else {
                $errors[] = 'Error updating product.';
            }
        } else {
            $query = "INSERT INTO products (category_id, product_name, description, price, discount_price, stock, sizes, colors, image, status, featured) 
                      VALUES ($category_id, '$product_name', '$description', $price, " . ($discount_price ? $discount_price : 'NULL') . ", $stock, '$sizes', '$colors', '$image_filename', '$status', $featured)";
            
            if (mysqli_query($conn, $query)) {
                set_message('Product added successfully!', 'success');
                redirect(BASE_URL . 'admin/manage_products.php');
            } else {
                $errors[] = 'Error adding product.';
            }
        }
    }
}

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
                <h1 class="h2"><?php echo $is_edit ? 'Edit' : 'Add New'; ?> Product</h1>
                <a href="manage_products.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Products
                </a>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="product_name" class="form-label">Product Name *</label>
                                    <input type="text" class="form-control" id="product_name" name="product_name" 
                                           value="<?php echo $is_edit ? htmlspecialchars($product['product_name']) : ''; ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4"><?php echo $is_edit ? htmlspecialchars($product['description']) : ''; ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="category_id" class="form-label">Category *</label>
                                        <select class="form-select" id="category_id" name="category_id" required>
                                            <option value="">Select Category</option>
                                            <?php
                                            $categories = get_all_categories();
                                            foreach ($categories as $cat) {
                                                $selected = ($is_edit && $product['category_id'] == $cat['category_id']) ? 'selected' : '';
                                                echo '<option value="' . $cat['category_id'] . '" ' . $selected . '>' . htmlspecialchars($cat['category_name']) . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">Status *</label>
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="active" <?php echo ($is_edit && $product['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                                            <option value="inactive" <?php echo ($is_edit && $product['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                            <option value="out_of_stock" <?php echo ($is_edit && $product['status'] === 'out_of_stock') ? 'selected' : ''; ?>>Out of Stock</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="price" class="form-label">Price ($) *</label>
                                        <input type="number" step="0.01" class="form-control" id="price" name="price" 
                                               value="<?php echo $is_edit ? $product['price'] : ''; ?>" required>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="discount_price" class="form-label">Discount Price ($)</label>
                                        <input type="number" step="0.01" class="form-control" id="discount_price" name="discount_price" 
                                               value="<?php echo $is_edit ? $product['discount_price'] : ''; ?>">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="stock" class="form-label">Stock *</label>
                                        <input type="number" class="form-control" id="stock" name="stock" 
                                               value="<?php echo $is_edit ? $product['stock'] : '0'; ?>" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="sizes" class="form-label">Sizes (comma separated)</label>
                                        <input type="text" class="form-control" id="sizes" name="sizes" 
                                               placeholder="S,M,L,XL,XXL"
                                               value="<?php echo $is_edit ? htmlspecialchars($product['sizes']) : ''; ?>">
                                        <small class="text-muted">Example: S,M,L,XL</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="colors" class="form-label">Colors (comma separated)</label>
                                        <input type="text" class="form-control" id="colors" name="colors" 
                                               placeholder="Red,Blue,Black,White"
                                               value="<?php echo $is_edit ? htmlspecialchars($product['colors']) : ''; ?>">
                                        <small class="text-muted">Example: Red,Blue,Black</small>
                                    </div>
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="featured" name="featured" 
                                           <?php echo ($is_edit && $product['featured']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="featured">
                                        Featured Product (Show on homepage)
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="image" class="form-label">Product Image</label>
                                    <?php if ($is_edit && $product['image']): ?>
                                        <div class="mb-2">
                                            <img src="<?php echo UPLOAD_URL . $product['image']; ?>" 
                                                 alt="Current Image" class="img-fluid img-thumbnail">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    <small class="text-muted">Max 5MB. Allowed: JPG, PNG, GIF, WEBP</small>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> <?php echo $is_edit ? 'Update' : 'Add'; ?> Product
                            </button>
                        </div>
                    </form>
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
