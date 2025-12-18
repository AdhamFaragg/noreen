<?php
/**
 * Cart Controller
 */
class CartController extends Controller {
    private $productModel;
    private $orderModel;

    public function __construct() {
         
        $this->productModel = new ProductModel();
        $this->orderModel = new OrderModel();
    }

    /**
     * Show cart
     */
    public function index() {
        $cart = $_SESSION['cart'] ?? [];
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $this->render('cart/index', [
            'cart' => $cart,
            'total' => $total
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . 'cart');
        }

        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 0);

        if (!isset($_SESSION['cart'][$productId])) {
            $_SESSION['error'] = 'Item not found in cart';
            $this->redirect(BASE_URL . 'cart');
        }

        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]);
        } else {
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
        }

        $_SESSION['success'] = 'Cart updated';
        $this->redirect(BASE_URL . 'cart');
    }

    /**
     * Remove item from cart
     */
    public function remove() {
        $productId = (int)($_GET['id'] ?? 0);

        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            $_SESSION['success'] = 'Item removed from cart';
        }

        $this->redirect(BASE_URL . 'cart');
    }

    /**
     * Show checkout page
     */
    public function checkout() {
        $this->requireLogin();

        $cart = $_SESSION['cart'] ?? [];

        if (empty($cart)) {
            $_SESSION['error'] = 'Your cart is empty';
            $this->redirect(BASE_URL . 'cart');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $this->render('cart/checkout', [
            'cart' => $cart,
            'total' => $total
        ]);
    }

    /**
     * Process checkout
     */
    public function processCheckout() {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . 'cart/checkout');
        }

        $cart = $_SESSION['cart'] ?? [];

        if (empty($cart)) {
            $_SESSION['error'] = 'Your cart is empty';
            $this->redirect(BASE_URL . 'cart');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        $shippingAddress = trim($_POST['shipping_address'] ?? '');
        $paymentMethod = $_POST['payment_method'] ?? 'credit_card';

        if (empty($shippingAddress)) {
            $_SESSION['error'] = 'Please provide shipping address';
            $this->redirect(BASE_URL . 'cart/checkout');
        }

        // Create order
        $orderId = $this->orderModel->createOrder([
            'user_id' => $this->userId(),
            'status' => 'pending',
            'total_amount' => $total,
            'payment_method' => $paymentMethod,
            'shipping_address' => $shippingAddress
        ]);

        if ($orderId) {
            // TODO: Save order items to a separate table
            unset($_SESSION['cart']);
            $_SESSION['success'] = 'Order placed successfully!';
            $this->redirect(BASE_URL . 'customer/orders');
        } else {
            $_SESSION['error'] = 'Failed to place order';
            $this->redirect(BASE_URL . 'cart/checkout');
        }
    }

    /**
     * Clear cart
     */
    public function clear() {
        unset($_SESSION['cart']);
        $_SESSION['success'] = 'Cart cleared';
        $this->redirect(BASE_URL . 'cart');
    }
}
?>
