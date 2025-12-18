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

<!-- Hero Section with Carousel -->
<div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <?php
        mysqli_data_seek($banners, 0);
        $active_index = 0;
        while (mysqli_fetch_assoc($banners)) {
            $active = ($active_index === 0) ? 'active' : '';
            echo '<button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="' . $active_index . '" class="' . $active . '"></button>';
            $active_index++;
        }
        ?>
    </div>
    <div class="carousel-inner">
        <?php
        mysqli_data_seek($banners, 0);
        $is_first = true;
        while ($banner = mysqli_fetch_assoc($banners)):
        ?>
        <div class="carousel-item <?php echo $is_first ? 'active' : ''; ?>">
            <div class="hero-slide" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 500px; display: flex; align-items: center;">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-md-7 text-white">
                            <h1 class="display-4 fw-bold mb-3"><?php echo htmlspecialchars($banner['title']); ?></h1>
                            <p class="lead mb-4"><?php echo htmlspecialchars($banner['subtitle']); ?></p>
                            <a href="customer/index.php" class="btn btn-light btn-lg">
                                <i class="fas fa-shopping-bag"></i> Shop Now
                            </a>
                        </div>
                        <div class="col-md-5">
                            <i class="fas fa-tshirt fa-10x text-white opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $is_first = false;
        endwhile;
        ?>
        
        <?php if (mysqli_num_rows($banners) === 0): ?>
        <div class="carousel-item active">
            <div class="hero-slide" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 500px; display: flex; align-items: center;">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-md-7 text-white">
                            <h1 class="display-4 fw-bold mb-3">Welcome to Fashion Store</h1>
                            <p class="lead mb-4">Discover the latest trends in fashion for men, women, and kids</p>
                            <a href="customer/index.php" class="btn btn-light btn-lg">
                                <i class="fas fa-shopping-bag"></i> Shop Now
                            </a>
                        </div>
                        <div class="col-md-5 text-center">
                            <i class="fas fa-tshirt fa-10x text-white opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<!-- Categories Section -->
<div class="container my-5">
    <h2 class="text-center mb-4">Shop by Category</h2>
    <div class="row">
        <?php foreach ($categories as $category): ?>
        <div class="col-md-3 col-sm-6 mb-4">
            <a href="customer/index.php?category=<?php echo $category['category_id']; ?>" class="text-decoration-none">
                <div class="card text-center h-100 category-card">
                    <div class="card-body">
                        <div class="category-icon mb-3">
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
                            <i class="fas <?php echo $icon; ?> fa-4x text-primary"></i>
                        </div>
                        <h5 class="card-title"><?php echo htmlspecialchars($category['category_name']); ?></h5>
                        <p class="card-text text-muted"><?php echo htmlspecialchars($category['description']); ?></p>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Featured Products Section -->
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Featured Products</h2>
        <a href="customer/index.php" class="btn btn-outline-primary">View All</a>
    </div>
    <div class="row">
        <?php foreach ($featured_products as $product): ?>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card h-100 product-card">
                <?php if ($product['discount_price']): ?>
                    <span class="badge bg-danger position-absolute" style="top: 10px; right: 10px; z-index: 1;">
                        SALE
                    </span>
                <?php endif; ?>
                
                <a href="customer/product_details.php?id=<?php echo $product['product_id']; ?>">
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
                        <a href="customer/product_details.php?id=<?php echo $product['product_id']; ?>" class="text-decoration-none text-dark">
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
                    <a href="customer/product_details.php?id=<?php echo $product['product_id']; ?>" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Features Section -->
<div class="bg-light py-5 my-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <div class="feature-box">
                    <i class="fas fa-shipping-fast fa-3x text-primary mb-3"></i>
                    <h5>Free Shipping</h5>
                    <p class="text-muted">On orders over $50</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="feature-box">
                    <i class="fas fa-undo fa-3x text-primary mb-3"></i>
                    <h5>Easy Returns</h5>
                    <p class="text-muted">30-day return policy</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="feature-box">
                    <i class="fas fa-lock fa-3x text-primary mb-3"></i>
                    <h5>Secure Payment</h5>
                    <p class="text-muted">100% secure transactions</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="feature-box">
                    <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                    <h5>24/7 Support</h5>
                    <p class="text-muted">Always here to help</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Newsletter Section -->
<div class="container my-5">
    <div class="card bg-primary text-white">
        <div class="card-body text-center py-5">
            <h3 class="mb-3">Subscribe to Our Newsletter</h3>
            <p class="mb-4">Get the latest updates on new products and upcoming sales</p>
            <form class="row g-3 justify-content-center">
                <div class="col-md-4">
                    <input type="email" class="form-control" placeholder="Enter your email" required>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-light">
                        <i class="fas fa-paper-plane"></i> Subscribe
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.category-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.category-card:hover {
    border-color: var(--primary-color);
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.category-icon {
    transition: transform 0.3s ease;
}

.category-card:hover .category-icon {
    transform: scale(1.1);
}

.feature-box {
    padding: 2rem 1rem;
}

.hero-slide {
    animation: fadeIn 0.5s ease;
}
</style>

<?php include 'includes/footer.php'; ?>
