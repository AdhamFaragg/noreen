<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>Products</h1>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>Filter</h5>
                </div>
                <div class="card-body">
                    <h6>Categories</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo BASE_URL; ?>?page=products" class="text-decoration-none">All</a></li>
                        <?php foreach ($categories as $cat): ?>
                            <li>
                                <a href="<?php echo BASE_URL; ?>?page=products&category=<?php echo $cat['category_id']; ?>" 
                                   class="text-decoration-none <?php echo $selectedCategory == $cat['category_id'] ? 'fw-bold text-primary' : ''; ?>">
                                    <?php echo htmlspecialchars($cat['category_name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-md-9">
            <?php if (!empty($searchTerm)): ?>
                <p class="mb-3">Search results for: <strong><?php echo htmlspecialchars($searchTerm); ?></strong></p>
            <?php endif; ?>

            <div class="row">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 product-card">
                                <img src="<?php echo UPLOAD_URL . $product['image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($product['product_name']); ?></h5>
                                    <p class="card-text text-muted"><?php echo substr(htmlspecialchars($product['description']), 0, 60) . '...'; ?></p>
                                    <p class="card-text"><strong>$<?php echo number_format($product['price'], 2); ?></strong></p>
                                    <p class="card-text"><small class="text-muted"><?php echo htmlspecialchars($product['category_name']); ?></small></p>
                                    <p class="card-text"><small class="text-muted">Stock: <?php echo $product['stock']; ?></small></p>
                                </div>
                                <div class="card-footer bg-white border-top">
                                    <a href="<?php echo BASE_URL; ?>?page=products&action=show&id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <button class="btn btn-sm btn-success add-to-cart" data-product-id="<?php echo $product['product_id']; ?>" data-product-name="<?php echo htmlspecialchars($product['product_name']); ?>">
                                        <i class="fas fa-cart-plus"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-md-12">
                        <p class="text-center text-muted">No products found</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                                <a class="page-link" href="<?php echo BASE_URL; ?>?page=products&p=<?php echo $i; ?><?php echo $selectedCategory ? '&category=' . $selectedCategory : ''; ?><?php echo $searchTerm ? '&search=' . urlencode($searchTerm) : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

ob_start();
require APP_PATH . '/Views/layout.php';
?>
