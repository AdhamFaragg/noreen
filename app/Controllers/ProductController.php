<?php
/**
 * Product Controller
 */
class ProductController extends Controller {
    private $productModel;
    private $categoryModel;

    public function __construct() {
         
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
    }

    /**
     * Show all products
     */
    public function index() {
        $pageNum = (int)($_GET['p'] ?? 1);
        $perPage = 12;
        $offset = ($pageNum - 1) * $perPage;

        $categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
        $searchTerm = $_GET['search'] ?? '';

        if ($categoryId) {
            $products = $this->productModel->getProductsByCategory($categoryId, $perPage, $offset);
        } elseif ($searchTerm) {
            $products = $this->productModel->searchProducts($searchTerm, $perPage, $offset);
        } else {
            $products = $this->productModel->getActiveProducts($perPage, $offset);
        }

        $categories = $this->categoryModel->getAllCategories();
        $totalProducts = $this->productModel->countActiveProducts();
        $totalPages = ceil($totalProducts / $perPage);

        return $this->render('products/index', [
            'products' => $products,
            'categories' => $categories,
            'currentPage' => $pageNum,
            'totalPages' => $totalPages,
            'searchTerm' => $searchTerm,
            'selectedCategory' => $categoryId
        ]);
    }

    /**
     * Show product details
     */
    public function show() {
        $productId = (int)($_GET['id'] ?? 0);

        if (!$productId) {
            $this->redirect(BASE_URL . 'products');
        }

        $product = $this->productModel->getProductById($productId);

        if (!$product) {
            $_SESSION['error'] = 'Product not found';
            $this->redirect(BASE_URL . 'products');
        }

        return $this->render('products/show', [
            'product' => $product
        ]);
    }

    /**
     * Add product to cart (AJAX)
     */
    public function addToCart() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        if (!$this->isLoggedIn()) {
            $this->json(['success' => false, 'message' => 'Please login first'], 401);
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $productId = (int)($data['product_id'] ?? 0);
        $quantity = (int)($data['quantity'] ?? 1);

        if (!$productId || $quantity < 1) {
            $this->json(['success' => false, 'message' => 'Invalid product'], 400);
        }

        $product = $this->productModel->getProductById($productId);
        if (!$product) {
            $this->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        // Initialize cart in session
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Add or update cart item
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = [
                'product_id' => $productId,
                'product_name' => $product['product_name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'quantity' => $quantity
            ];
        }

        $this->json(['success' => true, 'message' => 'Product added to cart']);
    }
}
?>
