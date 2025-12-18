<?php
/**
 * Home Controller
 */
class HomeController extends Controller {
    private $productModel;
    private $categoryModel;

    public function __construct() {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
    }

    /**
     * Show home page
     */
    public function index() {
        $featuredProducts = $this->productModel->getFeaturedProducts(8);
        $categories = $this->categoryModel->getAllCategories();

        return $this->render('home', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories
        ]);
    }

    /**
     * Show about page
     */
    public function about() {
        return $this->render('about');
    }

    /**
     * Get cart count (API endpoint)
     */
    public function getCartCount() {
        $count = 0;
        if (isset($_SESSION['cart'])) {
            $count = count($_SESSION['cart']);
        }
        
        header('Content-Type: application/json');
        echo json_encode(['count' => $count]);
        exit;
    }

    /**
     * Show contact page
     */
    public function contact() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $message = trim($_POST['message'] ?? '');

            if (!empty($name) && !empty($email) && !empty($message)) {
                // TODO: Send email or save to DB
                $_SESSION['success'] = 'Thank you for your message. We will get back to you soon.';
            } else {
                $_SESSION['error'] = 'Please fill in all fields';
            }
        }

        return $this->render('contact');
    }
}
?>
