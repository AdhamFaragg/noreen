<?php
require_once '../db/config.php';
require_once '../includes/functions.php';

require_login();

// Handle cart operations
if (isset($_POST['update_cart'])) {
    $cart_id = (int)$_POST['cart_id'];
    $quantity = (int)$_POST['quantity'];
    mysqli_query($conn, "UPDATE cart SET quantity = $quantity WHERE cart_id = $cart_id AND user_id = {$_SESSION['user_id']}");
    set_message('Cart updated!', 'success');
}

if (isset($_GET['remove'])) {
    $cart_id = (int)$_GET['remove'];
    remove_from_cart($cart_id, $_SESSION['user_id']);
    set_message('Item removed from cart.', 'success');
    redirect(BASE_URL . 'customer/cart.php');
}

$cart_items = get_cart_items($_SESSION['user_id']);
$cart_total = calculate_cart_total($_SESSION['user_id']);

include '../includes/header.php';
?>

<div class="container mt-4 mb-5">
    <h2 class="mb-4"><i class="fas fa-shopping-cart"></i> Shopping Cart</h2>

    <?php if (count($cart_items) > 0): ?>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                            <?php $price = $item['discount_price'] ?? $item['price']; ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if ($item['image']): ?>
                                            <img src="<?php echo UPLOAD_URL . $item['image']; ?>" 
                                                 alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                                 style="width: 60px; height: 60px; object-fit: cover;" class="me-3">
                                        <?php endif; ?>
                                        <div>
                                            <strong><?php echo htmlspecialchars($item['product_name']); ?></strong><br>
                                            <?php if ($item['size']): ?>
                                                <small class="text-muted">Size: <?php echo htmlspecialchars($item['size']); ?></small><br>
                                            <?php endif; ?>
                                            <?php if ($item['color']): ?>
                                                <small class="text-muted">Color: <?php echo htmlspecialchars($item['color']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo format_price($price); ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                        <div class="input-group" style="width: 120px;">
                                            <input type="number" class="form-control" name="quantity" 
                                                   value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>">
                                            <button type="submit" name="update_cart" class="btn btn-sm btn-primary">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </div>
                                    </form>
                                </td>
                                <td><strong><?php echo format_price($price * $item['quantity']); ?></strong></td>
                                <td>
                                    <a href="?remove=<?php echo $item['cart_id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Remove this item?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-3">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong><?php echo format_price($cart_total); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping:</span>
                        <span class="text-success">FREE</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <h5>Total:</h5>
                        <h5><?php echo format_price($cart_total); ?></h5>
                    </div>

                    <a href="checkout.php" class="btn btn-primary w-100 btn-lg">
                        <i class="fas fa-lock"></i> Proceed to Checkout
                    </a>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h6>Have a discount code?</h6>
                    <form action="checkout.php" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control" name="discount_code" placeholder="Enter code">
                            <button type="submit" class="btn btn-outline-primary">Apply</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="text-center py-5">
        <i class="fas fa-shopping-cart fa-5x text-muted mb-3"></i>
        <h4>Your cart is empty</h4>
        <p class="text-muted">Add some products to get started!</p>
        <a href="index.php" class="btn btn-primary">
            <i class="fas fa-shopping-bag"></i> Start Shopping
        </a>
    </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
