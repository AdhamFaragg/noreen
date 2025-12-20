<?php
require_once '../db/config.php';
require_once '../includes/functions.php';

require_login();

$cart_items = get_cart_items($_SESSION['user_id']);
if (count($cart_items) === 0) {
    set_message('Your cart is empty.', 'warning');
    redirect(BASE_URL . 'customer/cart.php');
}

$cart_total = calculate_cart_total($_SESSION['user_id']);
$discount_amount = 0;
$discount_code = '';
$discount_error = '';

// Handle discount code
if (isset($_GET['discount_code'])) {
    $discount_code = strtoupper(sanitize_input($_GET['discount_code']));
    $discount = validate_discount_code($discount_code, $cart_total);
    
    if ($discount) {
        $discount_amount = calculate_discount($discount, $cart_total);
        $_SESSION['discount_code'] = $discount_code;
        $_SESSION['discount_amount'] = $discount_amount;
    } else {
        $discount_error = 'Invalid or expired discount code.';
    }
} elseif (isset($_SESSION['discount_code'])) {
    $discount_code = $_SESSION['discount_code'];
    $discount_amount = $_SESSION['discount_amount'];
}

$final_total = $cart_total - $discount_amount;

// Handle order placement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipping_address = sanitize_input($_POST['shipping_address']);
    $shipping_city = sanitize_input($_POST['shipping_city']);
    $shipping_postal_code = sanitize_input($_POST['shipping_postal_code']);
    $shipping_phone = sanitize_input($_POST['shipping_phone']);
    $payment_method = sanitize_input($_POST['payment_method']);
    $notes = sanitize_input($_POST['notes']);
    
    // Server-side validation
    $checkout_errors = [];
    
    if (empty($shipping_address) || strlen($shipping_address) < 5) {
        $checkout_errors[] = 'Please provide a valid shipping address (at least 5 characters).';
    } elseif (strlen($shipping_address) > 255) {
        $checkout_errors[] = 'Shipping address cannot exceed 255 characters.';
    }
    
    if (empty($shipping_city) || strlen($shipping_city) < 2) {
        $checkout_errors[] = 'Please provide a valid city name.';
    } elseif (strlen($shipping_city) > 50) {
        $checkout_errors[] = 'City name cannot exceed 50 characters.';
    }
    
    if (empty($shipping_phone) || strlen($shipping_phone) < 7) {
        $checkout_errors[] = 'Please provide a valid phone number (at least 7 characters).';
    } elseif (!preg_match('/^[\d\s\-\+\(\)]+$/', $shipping_phone)) {
        $checkout_errors[] = 'Phone number contains invalid characters.';
    } elseif (strlen($shipping_phone) > 20) {
        $checkout_errors[] = 'Phone number cannot exceed 20 characters.';
    }
    
    if (!in_array($payment_method, ['cod', 'online'])) {
        $checkout_errors[] = 'Please select a valid payment method.';
    }
    
    if (!empty($notes) && strlen($notes) > 500) {
        $checkout_errors[] = 'Order notes cannot exceed 500 characters.';
    }
    
    if (empty($checkout_errors)) {
        $order_number = generate_order_number();
        $user_id = $_SESSION['user_id'];
        
        // Insert order
        $query = "INSERT INTO orders (user_id, order_number, total_amount, discount_amount, discount_code, payment_method, 
                  shipping_address, shipping_city, shipping_postal_code, shipping_phone, notes) 
                  VALUES ($user_id, '$order_number', $final_total, $discount_amount, '$discount_code', '$payment_method', 
                  '$shipping_address', '$shipping_city', '$shipping_postal_code', '$shipping_phone', '$notes')";
        
        if (mysqli_query($conn, $query)) {
            $order_id = mysqli_insert_id($conn);
            
            // Insert order items
            foreach ($cart_items as $item) {
                $price = $item['discount_price'] ?? $item['price'];
                $subtotal = $price * $item['quantity'];
                
                // Properly escape string values to prevent SQL injection
                $product_name = mysqli_real_escape_string($conn, $item['product_name']);
                $size = mysqli_real_escape_string($conn, $item['size']);
                $color = mysqli_real_escape_string($conn, $item['color']);
                
                $item_query = "INSERT INTO order_items (order_id, product_id, product_name, price, quantity, size, color, subtotal) 
                               VALUES ($order_id, {$item['product_id']}, '$product_name', $price, {$item['quantity']}, 
                               '$size', '$color', $subtotal)";
                if (!mysqli_query($conn, $item_query)) {
                    // Log error for debugging purposes
                    error_log("Order item insert error: " . mysqli_error($conn));
                }
                
                // Update product stock
                $stock_query = "UPDATE products SET stock = stock - {$item['quantity']} WHERE product_id = {$item['product_id']}";
                if (!mysqli_query($conn, $stock_query)) {
                    error_log("Product stock update error: " . mysqli_error($conn));
                }
            }
            
            // Update discount usage
            if ($discount_code) {
                $discount_update = "UPDATE discounts SET used_count = used_count + 1 WHERE code = '$discount_code'";
                if (!mysqli_query($conn, $discount_update)) {
                    error_log("Discount update error: " . mysqli_error($conn));
                }
            }
            
            // Clear cart
            clear_cart($user_id);
            unset($_SESSION['discount_code']);
            unset($_SESSION['discount_amount']);
            
            set_message('Order placed successfully! Order #: ' . $order_number, 'success');
            redirect(BASE_URL . 'customer/orders.php');
        } else {
            set_message('Error placing order. Please try again.', 'error');
        }
    } else {
        foreach ($checkout_errors as $error) {
            set_message($error, 'error');
        }
    }
}
        } else {
            error_log("Order insert error: " . mysqli_error($conn));
            set_message('Error placing order. Please try again.', 'error');
        }
    }
}

// Get user info for prefill
$user_query = "SELECT * FROM users WHERE user_id = {$_SESSION['user_id']}";
$user = mysqli_fetch_assoc(mysqli_query($conn, $user_query));

include '../includes/header.php';
?>

<div class="container mt-4 mb-5">
    <h2 class="mb-4"><i class="fas fa-lock"></i> Checkout</h2>

    <?php if ($discount_error): ?>
        <div class="alert alert-danger"><?php echo $discount_error; ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="row">
            <div class="col-md-7">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Shipping Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Shipping Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="shipping_address" rows="3" minlength="5" maxlength="255" required><?php echo htmlspecialchars($_POST['shipping_address'] ?? ($user['address'] ?? '')); ?></textarea>
                            <small class="text-muted">5-255 characters required</small>
                            <div class="invalid-feedback">Please provide a valid shipping address (5-255 characters).</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="shipping_city" value="<?php echo htmlspecialchars($_POST['shipping_city'] ?? ($user['city'] ?? '')); ?>" minlength="2" maxlength="50" required
                                       pattern="[a-zA-Z\s'-]+"
                                       title="City must contain only letters, spaces, hyphens, or apostrophes">
                                <div class="invalid-feedback">Please provide a valid city name.</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Postal Code</label>
                                <input type="text" class="form-control" name="shipping_postal_code" value="<?php echo htmlspecialchars($_POST['shipping_postal_code'] ?? ($user['postal_code'] ?? '')); ?>"
                                       maxlength="20"
                                       pattern="[a-zA-Z0-9\s\-]+"
                                       title="Invalid postal code format">
                                <div class="invalid-feedback">Please provide a valid postal code.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" name="shipping_phone" value="<?php echo htmlspecialchars($_POST['shipping_phone'] ?? ($user['phone'] ?? '')); ?>" 
                                   minlength="7" maxlength="20" required
                                   pattern="[\d\s\-\+\(\)]+"
                                   title="Phone must contain only numbers, spaces, hyphens, plus, or parentheses">
                            <div class="invalid-feedback">Please provide a valid phone number (7-20 characters).</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Order Notes (Optional)</label>
                            <textarea class="form-control" name="notes" rows="2" placeholder="Special instructions for delivery..." maxlength="500"><?php echo htmlspecialchars($_POST['notes'] ?? ''); ?></textarea>
                            <small class="text-muted">Max 500 characters</small>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Payment Method</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked required>
                            <label class="form-check-label" for="cod">
                                <strong>Cash on Delivery</strong><br>
                                <small class="text-muted">Pay when you receive your order</small>
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="online" value="online" required>
                            <label class="form-check-label" for="online">
                                <strong>Online Payment</strong><br>
                                <small class="text-muted">Pay securely online</small>
                            </label>
                        </div>
                        <div class="invalid-feedback d-block">Please select a payment method.</div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <span><?php echo htmlspecialchars($item['product_name']); ?> × <?php echo $item['quantity']; ?></span>
                                    <span><?php echo format_price(($item['discount_price'] ?? $item['price']) * $item['quantity']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <strong><?php echo format_price($cart_total); ?></strong>
                        </div>

                        <?php if ($discount_amount > 0): ?>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Discount (<?php echo htmlspecialchars($discount_code); ?>):</span>
                            <strong>-<?php echo format_price($discount_amount); ?></strong>
                        </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span class="text-success">FREE</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-4">
                            <h5>Total:</h5>
                            <h5 class="text-primary"><?php echo format_price($final_total); ?></h5>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="fas fa-check-circle"></i> Place Order
                        </button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-shield-alt text-success fa-2x me-3"></i>
                            <div>
                                <strong>Secure Checkout</strong><br>
                                <small class="text-muted">Your information is protected</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
