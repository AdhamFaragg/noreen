<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Shopping Cart</h1>
            
            <?php if (empty($cart)): ?>
                <div class="alert alert-info">
                    Your cart is empty. 
                    <a href="<?php echo BASE_URL; ?>?page=products">Continue shopping</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    <td>
                                        <form method="POST" action="<?php echo BASE_URL; ?>?page=cart&action=update" style="display: inline;">
                                            <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                            <div class="input-group" style="width: 100px;">
                                                <input type="number" class="form-control" name="quantity" value="<?php echo $item['quantity']; ?>" min="1">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary">Update</button>
                                            </div>
                                        </form>
                                    </td>
                                    <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>?page=cart&action=remove&id=<?php echo $item['product_id']; ?>" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Remove
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <a href="<?php echo BASE_URL; ?>?page=products" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Continue Shopping
                        </a>
                        <a href="<?php echo BASE_URL; ?>?page=cart&action=clear" class="btn btn-outline-danger" onclick="return confirm('Clear entire cart?');">
                            <i class="fas fa-trash"></i> Clear Cart
                        </a>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="card">
                            <div class="card-body">
                                <h5>Subtotal: $<?php echo number_format($total, 2); ?></h5>
                                <p class="text-muted">Shipping will be calculated at checkout</p>
                                <a href="<?php echo BASE_URL; ?>?page=cart&action=checkout" class="btn btn-primary w-100">
                                    <i class="fas fa-credit-card"></i> Proceed to Checkout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

ob_start();
require APP_PATH . '/Views/layout.php';
?>
