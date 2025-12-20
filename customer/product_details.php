<?php
require_once '../db/config.php';
require_once '../includes/functions.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = get_product_by_id($product_id);

if (!$product) {
    set_message('Product not found.', 'error');
    redirect(BASE_URL . 'customer/index.php');
}

// Update views
mysqli_query($conn, "UPDATE products SET views = views + 1 WHERE product_id = $product_id");

// Handle add to cart
if (isset($_POST['add_to_cart'])) {
    if (!is_logged_in()) {
        set_message('Please login to add items to cart.', 'warning');
        redirect(BASE_URL . 'login.php');
    }
    
    $quantity = (int)$_POST['quantity'];
    $size = isset($_POST['size']) ? sanitize_input($_POST['size']) : '';
    $color = isset($_POST['color']) ? sanitize_input($_POST['color']) : '';
    
    if (add_to_cart($_SESSION['user_id'], $product_id, $quantity, $size, $color)) {
        set_message('Product added to cart!', 'success');
        redirect(BASE_URL . 'customer/cart.php');
    }
}

// Handle add to wishlist
if (isset($_POST['add_to_wishlist'])) {
    if (!is_logged_in()) {
        set_message('Please login to add items to wishlist.', 'warning');
        redirect(BASE_URL . 'login.php');
    }
    
    $check = mysqli_query($conn, "SELECT * FROM wishlist WHERE user_id = {$_SESSION['user_id']} AND product_id = $product_id");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "INSERT INTO wishlist (user_id, product_id) VALUES ({$_SESSION['user_id']}, $product_id)");
        set_message('Product added to wishlist!', 'success');
    } else {
        set_message('Product already in wishlist.', 'info');
    }
}

// Get reviews
$reviews = get_product_reviews($product_id);
$rating_data = get_average_rating($product_id);

// Get related products
$related_query = "SELECT * FROM products WHERE category_id = {$product['category_id']} AND product_id != $product_id AND status = 'active' LIMIT 4";
$related_products = mysqli_query($conn, $related_query);

$sizes = !empty($product['sizes']) ? explode(',', $product['sizes']) : [];
$colors = !empty($product['colors']) ? explode(',', $product['colors']) : [];
$current_price = $product['discount_price'] ?? $product['price'];

include '../includes/header.php';
?>

<div class="container mt-4 mb-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="index.php">Shop</a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['product_name']); ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-5 mb-4">
            <!-- Main Image Gallery -->
            <div class="product-image-container mb-3">
                <div id="mainImage" style="position: relative; overflow: hidden; border-radius: 8px;">
                    <?php if ($product['image']): ?>
                        <img id="mainImg" src="<?php echo UPLOAD_URL . $product['image']; ?>" 
                             class="img-fluid rounded" 
                             alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                             style="width: 100%; height: auto;">
                    <?php else: ?>
                        <div class="bg-light p-5 text-center rounded" style="height: 400px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-image fa-5x text-muted"></i>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Navigation Arrows (hidden if only one image) -->
                    <div id="imageNavigation" style="display: none; position: absolute; top: 50%; left: 0; right: 0; display: flex; justify-content: space-between; align-items: center; padding: 0 10px;">
                        <button class="btn btn-light btn-sm" onclick="previousImage()">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="btn btn-light btn-sm" onclick="nextImage()">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Thumbnail Gallery -->
            <div id="thumbnailGallery" style="display: none;">
                <div class="row g-2">
                    <div class="col-3 mb-2">
                        <img src="<?php echo UPLOAD_URL . $product['image']; ?>" 
                             class="img-fluid rounded thumbnail-img" 
                             alt="Main image" 
                             onclick="changeImage(0)"
                             style="cursor: pointer; border: 3px solid #667eea; opacity: 0.8;">
                    </div>
                    <div id="additionalThumbnails"></div>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <h2><?php echo htmlspecialchars($product['product_name']); ?></h2>
            
            <div class="mb-3">
                <?php if ($rating_data['total_reviews'] > 0): ?>
                    <div class="d-flex align-items-center">
                        <?php
                        $avg_rating = round($rating_data['avg_rating']);
                        for ($i = 1; $i <= 5; $i++) {
                            echo '<i class="fas fa-star ' . ($i <= $avg_rating ? 'text-warning' : 'text-muted') . '"></i> ';
                        }
                        ?>
                        <span class="ms-2">(<?php echo $rating_data['total_reviews']; ?> reviews)</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <?php if ($product['discount_price']): ?>
                    <h3 class="text-danger mb-0"><?php echo format_price($product['discount_price']); ?></h3>
                    <small class="text-muted text-decoration-line-through"><?php echo format_price($product['price']); ?></small>
                    <span class="badge bg-danger ms-2">
                        <?php echo round((($product['price'] - $product['discount_price']) / $product['price']) * 100); ?>% OFF
                    </span>
                <?php else: ?>
                    <h3><?php echo format_price($product['price']); ?></h3>
                <?php endif; ?>
            </div>

            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

            <form method="POST" novalidate>
                <?php if (!empty($sizes)): ?>
                <div class="mb-3">
                    <label class="form-label"><strong>Size: <span class="text-danger">*</span></strong></label>
                    <div class="btn-group" role="group">
                        <?php foreach ($sizes as $size): ?>
                            <input type="radio" class="btn-check" name="size" id="size_<?php echo trim($size); ?>" value="<?php echo trim($size); ?>" required>
                            <label class="btn btn-outline-primary" for="size_<?php echo trim($size); ?>"><?php echo trim($size); ?></label>
                        <?php endforeach; ?>
                    </div>
                    <div class="invalid-feedback d-block">Please select a size.</div>
                </div>
                <?php endif; ?>

                <?php if (!empty($colors)): ?>
                <div class="mb-3">
                    <label class="form-label"><strong>Color: <span class="text-danger">*</span></strong></label>
                    <select class="form-select" name="color" style="max-width: 200px;" required>
                        <option value="">Select Color</option>
                        <?php foreach ($colors as $color): ?>
                            <option value="<?php echo trim($color); ?>"><?php echo trim($color); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback d-block">Please select a color.</div>
                </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label"><strong>Quantity: <span class="text-danger">*</span></strong></label>
                    <input type="number" class="form-control" name="quantity" value="1" 
                           min="1" max="<?php echo $product['stock']; ?>" 
                           style="max-width: 100px;" required>
                    <small class="text-muted"><?php echo $product['stock']; ?> available</small>
                    <div class="invalid-feedback d-block">Quantity must be between 1 and <?php echo $product['stock']; ?>.</div>
                </div>

                <?php if ($product['stock'] > 0): ?>
                <div class="d-flex gap-2">
                    <button type="submit" name="add_to_cart" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                    <button type="submit" name="add_to_wishlist" class="btn btn-outline-danger btn-lg">
                        <i class="fas fa-heart"></i> Wishlist
                    </button>
                </div>
                <?php else: ?>
                <div class="alert alert-danger">Out of Stock</div>
                <?php endif; ?>
            </form>

            <hr class="my-4">

            <div class="row text-center">
                <div class="col-4">
                    <i class="fas fa-shipping-fast fa-2x text-primary mb-2"></i>
                    <p class="small mb-0">Free Shipping</p>
                </div>
                <div class="col-4">
                    <i class="fas fa-undo fa-2x text-primary mb-2"></i>
                    <p class="small mb-0">Easy Returns</p>
                </div>
                <div class="col-4">
                    <i class="fas fa-shield-alt fa-2x text-primary mb-2"></i>
                    <p class="small mb-0">Secure Payment</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="mt-5">
        <h4>Customer Reviews</h4>
        <hr>
        <?php if (count($reviews) > 0): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="mb-3 p-3 border rounded">
                    <div class="d-flex justify-content-between">
                        <strong><?php echo htmlspecialchars($review['full_name']); ?></strong>
                        <small class="text-muted"><?php echo time_ago($review['created_at']); ?></small>
                    </div>
                    <div class="mb-2">
                        <?php
                        for ($i = 1; $i <= 5; $i++) {
                            echo '<i class="fas fa-star ' . ($i <= $review['rating'] ? 'text-warning' : 'text-muted') . '"></i>';
                        }
                        ?>
                    </div>
                    <p class="mb-0"><?php echo htmlspecialchars($review['review_text']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">No reviews yet. Be the first to review this product!</p>
        <?php endif; ?>
    </div>

    <!-- Related Products -->
    <?php if (mysqli_num_rows($related_products) > 0): ?>
    <div class="mt-5">
        <h4>Related Products</h4>
        <hr>
        <div class="row">
            <?php while ($related = mysqli_fetch_assoc($related_products)): ?>
            <div class="col-md-3 mb-3">
                <div class="card">
                    <a href="product_details.php?id=<?php echo $related['product_id']; ?>">
                        <?php if ($related['image']): ?>
                            <img src="<?php echo UPLOAD_URL . $related['image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($related['product_name']); ?>" style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                    </a>
                    <div class="card-body">
                        <h6><?php echo htmlspecialchars(truncate_text($related['product_name'], 40)); ?></h6>
                        <p class="mb-0"><strong><?php echo format_price($related['discount_price'] ?? $related['price']); ?></strong></p>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
<script>
// Product Image Gallery Handler
let currentImageIndex = 0;
let productImages = [];

<?php 
// Fetch additional images if using product_images table
$additional_images_query = "SELECT image_path FROM product_images WHERE product_id = $product_id ORDER BY image_order ASC";
$additional_images_result = mysqli_query($conn, $additional_images_query);
$has_additional_images = mysqli_num_rows($additional_images_result) > 0;
?>

// Initialize images array
productImages.push('<?php echo UPLOAD_URL . $product['image']; ?>');

<?php 
if ($has_additional_images) {
    while ($img = mysqli_fetch_assoc($additional_images_result)) {
        echo "productImages.push('" . UPLOAD_URL . $img['image_path'] . "');\n";
    }
}
?>

// Initialize gallery
document.addEventListener('DOMContentLoaded', function() {
    if (productImages.length > 1) {
        // Show navigation if multiple images
        document.getElementById('imageNavigation').style.display = 'flex';
        document.getElementById('thumbnailGallery').style.display = 'block';
        
        // Populate thumbnails
        const thumbnailContainer = document.getElementById('additionalThumbnails');
        for (let i = 1; i < productImages.length; i++) {
            const col = document.createElement('div');
            col.className = 'col-3 mb-2';
            col.innerHTML = `
                <img src="${productImages[i]}" 
                     class="img-fluid rounded thumbnail-img" 
                     alt="Image ${i + 1}" 
                     onclick="changeImage(${i})"
                     style="cursor: pointer; border: 3px solid transparent; opacity: 0.7;">
            `;
            thumbnailContainer.appendChild(col);
        }
    }
});

function changeImage(index) {
    if (index >= 0 && index < productImages.length) {
        currentImageIndex = index;
        document.getElementById('mainImg').src = productImages[index];
        updateThumbnailSelection();
    }
}

function nextImage() {
    currentImageIndex = (currentImageIndex + 1) % productImages.length;
    changeImage(currentImageIndex);
}

function previousImage() {
    currentImageIndex = (currentImageIndex - 1 + productImages.length) % productImages.length;
    changeImage(currentImageIndex);
}

function updateThumbnailSelection() {
    const thumbnails = document.querySelectorAll('.thumbnail-img');
    thumbnails.forEach((thumb, idx) => {
        if (idx === currentImageIndex) {
            thumb.style.borderColor = '#667eea';
            thumb.style.opacity = '1';
        } else {
            thumb.style.borderColor = 'transparent';
            thumb.style.opacity = '0.7';
        }
    });
}

// Keyboard navigation
document.addEventListener('keydown', function(event) {
    if (productImages.length > 1) {
        if (event.key === 'ArrowRight') nextImage();
        if (event.key === 'ArrowLeft') previousImage();
    }
});
</script>