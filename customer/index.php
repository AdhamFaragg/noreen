<?php
require_once '../db/config.php';
require_once '../includes/functions.php';

// Get filters
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$sort = isset($_GET['sort']) ? sanitize_input($_GET['sort']) : 'newest';

// Pagination
$items_per_page = 12;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

// Build query
$query = "SELECT p.*, c.category_name FROM products p 
          LEFT JOIN categories c ON p.category_id = c.category_id 
          WHERE p.status = 'active'";

if (!empty($search)) {
    $query .= " AND (p.product_name LIKE '%$search%' OR p.description LIKE '%$search%')";
}

if ($category_id) {
    $query .= " AND p.category_id = $category_id";
}

// Sorting
switch ($sort) {
    case 'price_low':
        $query .= " ORDER BY IFNULL(p.discount_price, p.price) ASC";
        break;
    case 'price_high':
        $query .= " ORDER BY IFNULL(p.discount_price, p.price) DESC";
        break;
    case 'name':
        $query .= " ORDER BY p.product_name ASC";
        break;
    default:
        $query .= " ORDER BY p.created_at DESC";
}

// Get total count
$count_query = str_replace("SELECT p.*, c.category_name", "SELECT COUNT(*) as total", $query);
$count_result = mysqli_query($conn, $count_query);
$total_products = mysqli_fetch_assoc($count_result)['total'];

// Add limit
$query .= " LIMIT $offset, $items_per_page";
$result = mysqli_query($conn, $query);

include '../includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-filter"></i> Filters</h5>
                </div>
                <div class="card-body">
                    <h6>Categories</h6>
                    <div class="list-group mb-3">
                        <a href="index.php" class="list-group-item list-group-item-action <?php echo !$category_id ? 'active' : ''; ?>">
                            All Products
                        </a>
                        <?php
                        $categories = get_all_categories();
                        foreach ($categories as $cat) {
                            $active = ($category_id == $cat['category_id']) ? 'active' : '';
                            echo '<a href="?category=' . $cat['category_id'] . '" class="list-group-item list-group-item-action ' . $active . '">';
                            echo htmlspecialchars($cat['category_name']);
                            echo '</a>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products -->
        <div class="col-md-9">
            <!-- Search & Sort Bar -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4>
                        <?php 
                        if ($search) {
                            echo 'Search Results for: "' . htmlspecialchars($search) . '"';
                        } elseif ($category_id) {
                            $cat = get_category_by_id($category_id);
                            echo htmlspecialchars($cat['category_name']);
                        } else {
                            echo 'All Products';
                        }
                        ?>
                    </h4>
                    <small class="text-muted"><?php echo $total_products; ?> products found</small>
                </div>
                <div>
                    <select class="form-select" onchange="window.location.href=this.value;">
                        <option value="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'newest'])); ?>" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest</option>
                        <option value="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'price_low'])); ?>" <?php echo $sort === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'price_high'])); ?>" <?php echo $sort === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'name'])); ?>" <?php echo $sort === 'name' ? 'selected' : ''; ?>>Name</option>
                    </select>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="row">
                <?php while ($product = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="card h-100 product-card">
                        <?php if ($product['discount_price']): ?>
                            <span class="badge bg-danger position-absolute" style="top: 10px; right: 10px; z-index: 1;">
                                SALE
                            </span>
                        <?php endif; ?>
                        
                        <a href="product_details.php?id=<?php echo $product['product_id']; ?>">
                            <?php if ($product['image']): ?>
                                <img src="<?php echo UPLOAD_URL . $product['image']; ?>" 
                                     class="card-img-top" alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                     style="height: 250px; object-fit: cover;">
                            <?php else: ?>
                                <div style="height: 250px; background: #ddd; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </a>
                        
                        <div class="card-body">
                            <h6 class="card-title">
                                <a href="product_details.php?id=<?php echo $product['product_id']; ?>" class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars(truncate_text($product['product_name'], 50)); ?>
                                </a>
                            </h6>
                            <p class="text-muted small mb-2"><?php echo htmlspecialchars($product['category_name']); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <?php if ($product['discount_price']): ?>
                                        <span class="text-muted text-decoration-line-through small"><?php echo format_price($product['price']); ?></span><br>
                                        <strong class="text-danger"><?php echo format_price($product['discount_price']); ?></strong>
                                    <?php else: ?>
                                        <strong><?php echo format_price($product['price']); ?></strong>
                                    <?php endif; ?>
                                </div>
                                <?php if ($product['stock'] > 0): ?>
                                    <span class="badge bg-success">In Stock</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Out of Stock</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <a href="product_details.php?id=<?php echo $product['product_id']; ?>" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
                
                <?php if ($total_products === 0): ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                    <h4>No products found</h4>
                    <p class="text-muted">Try adjusting your search or filter criteria</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_products > $items_per_page): ?>
                <div class="mt-4">
                    <?php
                    $base_url = 'index.php?' . http_build_query(array_diff_key($_GET, ['page' => ''])) . '&page=';
                    echo create_pagination($total_products, $items_per_page, $current_page, $base_url);
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.product-card {
    transition: transform 0.3s, box-shadow 0.3s;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}
</style>

<?php include '../includes/footer.php'; ?>
