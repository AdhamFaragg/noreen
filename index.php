<?php
require_once 'db/config.php';
require_once 'includes/functions.php';

// Get featured products
$featured_products = get_featured_products(8);

// Get all categories
$categories = get_all_categories();

// Get banners
$banners_query = "SELECT * FROM banners WHERE status = 'active' ORDER BY display_order LIMIT 3";
$banners = mysqli_query($conn, $banners_query);

include 'includes/header.php';
?>

<!-- Hero Section with Modern Design -->
<div class="hero-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 600px; display: flex; align-items: center; position: relative; overflow: hidden;">
    <div class="position-absolute" style="top: -50px; right: -50px; width: 300px; height: 300px; background: rgba(255,255,255,0.1); border-radius: 50%; opacity: 0.3;"></div>
    <div class="position-absolute" style="bottom: -80px; left: -80px; width: 400px; height: 400px; background: rgba(255,255,255,0.1); border-radius: 50%; opacity: 0.2;"></div>
    
    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-6 text-white">
                <div class="mb-4">
                    <span class="badge bg-light text-primary mb-3 px-3 py-2">Welcome to jainz</span>
                </div>
                <h1 class="display-3 fw-bold mb-3" style="line-height: 1.2;">Premium Fashion for Everyone</h1>
                <p class="lead mb-4 opacity-90">Discover exclusive collections curated for style, comfort, and confidence. From timeless classics to trending pieces.</p>
                <div class="d-flex gap-3">
                    <a href="customer/index.php" class="btn btn-light btn-lg px-4">
                        <i class="fas fa-shopping-bag"></i> Shop Now
                    </a>
                    <a href="#featured" class="btn btn-outline-light btn-lg px-4">
                        <i class="fas fa-arrow-down"></i> Explore
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div style="font-size: 150px;">
                    <i class="fas fa-shopping-bags text-white opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section - Top -->
<div class="py-5" style="background: #f8f9fa;">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-sm-6">
                <div class="text-center p-4">
                    <div class="mb-3" style="font-size: 40px; color: #667eea;">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h6 class="fw-bold">Free Shipping</h6>
                    <p class="text-muted small">On orders over $50</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="text-center p-4">
                    <div class="mb-3" style="font-size: 40px; color: #667eea;">
                        <i class="fas fa-undo"></i>
                    </div>
                    <h6 class="fw-bold">Easy Returns</h6>
                    <p class="text-muted small">30-day guarantee</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="text-center p-4">
                    <div class="mb-3" style="font-size: 40px; color: #667eea;">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h6 class="fw-bold">Secure Payment</h6>
                    <p class="text-muted small">100% protected</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="text-center p-4">
                    <div class="mb-3" style="font-size: 40px; color: #667eea;">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h6 class="fw-bold">24/7 Support</h6>
                    <p class="text-muted small">Always here to help</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Categories Section - Modern Grid -->
<div class="container my-5">
    <div class="mb-5">
        <h2 class="display-5 fw-bold mb-2">Shop by Category</h2>
        <p class="text-muted lead">Find exactly what you're looking for</p>
    </div>
    <div class="row g-4">
        <?php foreach ($categories as $category): ?>
        <div class="col-lg-4 col-md-6">
            <a href="customer/index.php?category=<?php echo $category['category_id']; ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm overflow-hidden category-card" style="height: 250px; transition: all 0.3s ease;">
                    <div class="card-body d-flex flex-column justify-content-end p-4" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); height: 100%;">
                        <div class="mb-3">
                            <?php
                            $icons = [
                                'Men' => 'fa-male',
                                'Women' => 'fa-female',
                                'Kids' => 'fa-child',
                                'Accessories' => 'fa-gem',
                                'Footwear' => 'fa-shoe-prints'
                            ];
                            $icon = $icons[$category['category_name']] ?? 'fa-tag';
                            ?>
                            <i class="fas <?php echo $icon; ?> fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title fw-bold"><?php echo htmlspecialchars($category['category_name']); ?></h5>
                        <p class="card-text text-muted small"><?php echo htmlspecialchars($category['description']); ?></p>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Featured Products Section - Modern Cards -->
<div id="featured" class="bg-light py-5 my-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <h2 class="display-5 fw-bold mb-2">Featured Collections</h2>
                <p class="text-muted lead">Handpicked styles just for you</p>
            </div>
            <a href="customer/index.php" class="btn btn-primary">View All Products</a>
        </div>
        <div class="row g-4">
            <?php foreach ($featured_products as $product): ?>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm overflow-hidden product-card h-100" style="transition: all 0.3s ease;">
                    <div class="position-relative" style="height: 280px; overflow: hidden;">
                        <?php if ($product['discount_price']): ?>
                            <span class="badge bg-danger position-absolute" style="top: 10px; right: 10px; z-index: 1; font-size: 0.9rem; padding: 0.5rem 0.75rem;">
                                SALE
                            </span>
                        <?php endif; ?>
                        
                        <a href="customer/product_details.php?id=<?php echo $product['product_id']; ?>">
                            <?php if ($product['image']): ?>
                                <img src="<?php echo UPLOAD_URL . $product['image']; ?>" 
                                     class="card-img-top" alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                     style="height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                            <?php else: ?>
                                <div style="height: 100%; background: linear-gradient(135deg, #ddd 0%, #eee 100%); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </a>
                    </div>
                    
                    <div class="card-body d-flex flex-column">
                        <div class="mb-2">
                            <small class="text-primary fw-bold"><?php echo htmlspecialchars($product['category_name']); ?></small>
                        </div>
                        <h6 class="card-title">
                            <a href="customer/product_details.php?id=<?php echo $product['product_id']; ?>" class="text-decoration-none text-dark">
                                <?php echo htmlspecialchars(truncate_text($product['product_name'], 40)); ?>
                            </a>
                        </h6>
                        <div class="my-3">
                            <?php if ($product['discount_price']): ?>
                                <span class="text-muted text-decoration-line-through small"><?php echo format_price($product['price']); ?></span><br>
                                <strong class="text-danger h6"><?php echo format_price($product['discount_price']); ?></strong>
                            <?php else: ?>
                                <strong class="h6"><?php echo format_price($product['price']); ?></strong>
                            <?php endif; ?>
                        </div>
                        <div class="mt-auto">
                            <?php if ($product['stock'] > 0): ?>
                                <span class="badge bg-success mb-3">In Stock</span>
                            <?php else: ?>
                                <span class="badge bg-danger mb-3">Out of Stock</span>
                            <?php endif; ?>
                            <a href="customer/product_details.php?id=<?php echo $product['product_id']; ?>" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Call to Action Section -->
<div class="container my-5">
    <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); overflow: hidden;">
        <div class="row align-items-center">
            <div class="col-lg-7 text-white p-5">
                <h3 class="fw-bold mb-3">Exclusive Offers & Updates</h3>
                <p class="mb-4 opacity-90">Subscribe to get special discounts, early access to new collections, and style tips delivered to your inbox.</p>
                <form class="row g-2" onsubmit="return false;">
                    <div class="col-md-8">
                        <input type="email" class="form-control form-control-lg" placeholder="Enter your email" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-light btn-lg w-100">Subscribe</button>
                    </div>
                </form>
            </div>
            <div class="col-lg-5 text-center text-white p-5">
                <i class="fas fa-gift fa-5x opacity-25"></i>
            </div>
        </div>
    </div>
</div>

<style>
.category-card {
    cursor: pointer;
}

.category-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.2) !important;
}

.product-card {
    cursor: pointer;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.1) !important;
}

.product-card:hover img {
    transform: scale(1.05);
}

.hero-section {
    animation: fadeIn 0.6s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@media (max-width: 768px) {
    .display-3 {
        font-size: 2.5rem;
    }
    
    .display-5 {
        font-size: 1.8rem;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
