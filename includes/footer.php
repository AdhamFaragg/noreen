    </div> <!-- End Main Content -->

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
                        <li><a href="<?php echo BASE_URL; ?>customer/index.php" class="text-muted">Shop</a></li>
                        <li><a href="#" class="text-muted">About Us</a></li>
                        <li><a href="#" class="text-muted">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6>Categories</h6>
                    <ul class="list-unstyled">
                        <?php
                        $categories = get_all_categories();
                        foreach (array_slice($categories, 0, 4) as $category) {
                            echo '<li><a href="' . BASE_URL . 'customer/index.php?category=' . $category['category_id'] . '" class="text-muted">' . htmlspecialchars($category['category_name']) . '</a></li>';
                        }
                        ?>
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
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0 text-muted">
                        <a href="#" class="text-muted">Privacy Policy</a> | 
                        <a href="#" class="text-muted">Terms & Conditions</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
</body>
</html>
