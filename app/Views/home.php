<div class="container mt-5">
    <!-- Hero Banner -->
    <div class="row mb-5">
        <div class="col-md-12">
            <div class="bg-light p-5 rounded text-center">
                <h1 class="display-4">Welcome to Fashion Store</h1>
                <p class="lead">Discover the latest trends in fashion</p>
                <a href="<?php echo BASE_URL; ?>?page=products" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-bag"></i> Shop Now
                </a>
            </div>
        </div>
    </div>

    <!-- Featured Products -->
    <div class="row mb-5">
        <div class="col-md-12">
            <h2 class="mb-4">Featured Products</h2>
        </div>
    </div>

    <div class="row">
        <?php if (!empty($featuredProducts)): ?>
            <?php foreach ($featuredProducts as $product): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100 product-card">
                        <img src="<?php echo UPLOAD_URL . $product['image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['product_name']); ?></h5>
                            <p class="card-text text-muted"><?php echo substr(htmlspecialchars($product['description']), 0, 50) . '...'; ?></p>
                            <p class="card-text"><strong>$<?php echo number_format($product['price'], 2); ?></strong></p>
                            <p class="card-text"><small class="text-muted"><?php echo htmlspecialchars($product['category_name']); ?></small></p>
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
                <p class="text-center text-muted">No featured products available</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Categories Preview -->
    <div class="row mt-5 mb-5">
        <div class="col-md-12">
            <h2 class="mb-4">Shop by Category</h2>
        </div>
    </div>

    <div class="row">
        <?php if (!empty($categories)): ?>
            <?php foreach (array_slice($categories, 0, 4) as $category): ?>
                <div class="col-md-3 mb-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($category['category_name']); ?></h5>
                            <a href="<?php echo BASE_URL; ?>?page=products&category=<?php echo $category['category_id']; ?>" class="btn btn-outline-primary">
                                Browse
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
