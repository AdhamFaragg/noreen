<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-body">
                    <h2><?php echo htmlspecialchars($product['product_name']); ?></h2>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <img src="<?php echo UPLOAD_URL . $product['image']; ?>" class="img-fluid" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted"><?php echo htmlspecialchars($product['category_name']); ?></p>
                            
                            <h3 class="text-primary mb-3">$<?php echo number_format($product['price'], 2); ?></h3>
                            
                            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                            
                            <p class="mb-3">
                                <strong>Stock Available:</strong> <?php echo $product['stock']; ?>
                            </p>
                            
                            <?php if ($product['stock'] > 0): ?>
                                <div class="input-group mb-3" style="width: 150px;">
                                    <span class="input-group-text">Qty:</span>
                                    <input type="number" class="form-control" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                                </div>
                                
                                <button class="btn btn-success btn-lg" id="addToCartBtn" data-product-id="<?php echo $product['product_id']; ?>" data-product-name="<?php echo htmlspecialchars($product['product_name']); ?>">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            <?php else: ?>
                                <p class="text-danger">Out of Stock</p>
                            <?php endif; ?>
                            
                            <hr class="my-4">
                            
                            <a href="<?php echo BASE_URL; ?>?page=products" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Products
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

ob_start();
require APP_PATH . '/Views/layout.php';
?>
