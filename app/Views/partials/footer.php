<!-- Footer -->
<footer class="bg-dark text-light py-5 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5><i class="fas fa-shopping-bag"></i> Fashion Store</h5>
                <p class="text-muted">Your one-stop destination for trendy and affordable clothing for men, women, and kids.</p>
                <div class="social-links">
                    <a href="#" class="text-light me-3"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="#" class="text-light me-3"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="#" class="text-light me-3"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" class="text-light me-3"><i class="fab fa-pinterest fa-lg"></i></a>
                </div>
            </div>
            <div class="col-md-2 mb-4">
                <h6>Quick Links</h6>
                <ul class="list-unstyled">
                    <li><a href="<?php echo BASE_URL; ?>" class="text-muted">Home</a></li>
                    <li><a href="<?php echo BASE_URL; ?>?page=products" class="text-muted">Shop</a></li>
                    <li><a href="<?php echo BASE_URL; ?>?page=home&action=about" class="text-muted">About Us</a></li>
                    <li><a href="<?php echo BASE_URL; ?>?page=home&action=contact" class="text-muted">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-4">
                <h6>Categories</h6>
                <ul class="list-unstyled">
                    <?php 
                    $categoryModel = new CategoryModel();
                    $allCats = $categoryModel->getAllCategories();
                    foreach (array_slice($allCats, 0, 4) as $cat): 
                    ?>
                        <li><a href="<?php echo BASE_URL; ?>?page=products&category=<?php echo $cat['category_id']; ?>" class="text-muted"><?php echo htmlspecialchars($cat['category_name']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-md-3 mb-4">
                <h6>Contact Info</h6>
                <ul class="list-unstyled text-muted">
                    <li><i class="fas fa-map-marker-alt"></i> 123 Fashion Street, NY</li>
                    <li><i class="fas fa-phone"></i> +1 234 567 8900</li>
                    <li><i class="fas fa-envelope"></i> info@fashionstore.com</li>
                </ul>
            </div>
        </div>
        <hr class="bg-secondary">
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0 text-muted">&copy; <?php echo date('Y'); ?> Fashion Store. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>
