<?php
/**
 * Admin Controller
 */
class AdminController extends Controller {
    private $productModel;
    private $userModel;
    private $orderModel;
    private $categoryModel;

    public function __construct() {
         
        $this->requireAdmin();
        $this->productModel = new ProductModel();
        $this->userModel = new UserModel();
        $this->orderModel = new OrderModel();
        $this->categoryModel = new CategoryModel();
    }

    /**
     * Show admin dashboard
     */
    public function dashboard() {
        $stats = [
            'totalProducts' => $this->productModel->countActiveProducts(),
            'totalUsers' => $this->userModel->countUsers(),
            'totalOrders' => $this->orderModel->countOrders(),
            'totalRevenue' => $this->orderModel->getTotalRevenue(),
            'totalCategories' => $this->categoryModel->countCategories()
        ];

        $recentOrders = $this->orderModel->getAllOrders(10);

        return $this->render('admin/dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders
        ]);
    }

    /**
     * Show products management
     */
    public function manageProducts() {
        $products = $this->productModel->getActiveProducts(null);

        return $this->render('admin/manage_products', [
            'products' => $products
        ]);
    }

    /**
     * Show add product form
     */
    public function addProduct() {
        $categories = $this->categoryModel->getAllCategories();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productName = trim($_POST['product_name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = (float)($_POST['price'] ?? 0);
            $categoryId = (int)($_POST['category_id'] ?? 0);
            $stock = (int)($_POST['stock'] ?? 0);

            if (empty($productName) || $price <= 0) {
                $_SESSION['error'] = 'Please fill in all required fields';
                $this->redirect(BASE_URL . 'admin/products/add');
            }

            $imageName = '';
            if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
                $uploadResult = $this->uploadImage($_FILES['image']);
                if ($uploadResult['success']) {
                    $imageName = $uploadResult['filename'];
                } else {
                    $_SESSION['error'] = $uploadResult['message'];
                    $this->redirect(BASE_URL . 'admin/products/add');
                }
            }

            $productId = $this->productModel->createProduct([
                'product_name' => $productName,
                'description' => $description,
                'price' => $price,
                'category_id' => $categoryId,
                'image' => $imageName,
                'stock' => $stock,
                'status' => 'active',
                'featured' => isset($_POST['featured']) ? 1 : 0
            ]);

            if ($productId) {
                $_SESSION['success'] = 'Product added successfully';
                $this->redirect(BASE_URL . 'admin/products');
            } else {
                $_SESSION['error'] = 'Failed to add product';
            }
        }

        return $this->render('admin/add_product', [
            'categories' => $categories
        ]);
    }

    /**
     * Show edit product form
     */
    public function editProduct() {
        $productId = (int)($_GET['id'] ?? 0);

        if (!$productId) {
            $this->redirect(BASE_URL . 'admin/products');
        }

        $product = $this->productModel->getProductById($productId);

        if (!$product) {
            $_SESSION['error'] = 'Product not found';
            $this->redirect(BASE_URL . 'admin/products');
        }

        $categories = $this->categoryModel->getAllCategories();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'product_name' => trim($_POST['product_name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'price' => (float)($_POST['price'] ?? 0),
                'category_id' => (int)($_POST['category_id'] ?? 0),
                'stock' => (int)($_POST['stock'] ?? 0),
                'featured' => isset($_POST['featured']) ? 1 : 0
            ];

            if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
                $uploadResult = $this->uploadImage($_FILES['image']);
                if ($uploadResult['success']) {
                    if ($product['image']) {
                        $this->deleteImage($product['image']);
                    }
                    $data['image'] = $uploadResult['filename'];
                }
            }

            $this->productModel->updateProduct($productId, $data);
            $_SESSION['success'] = 'Product updated successfully';
            $this->redirect(BASE_URL . 'admin/products');
        }

        return $this->render('admin/edit_product', [
            'product' => $product,
            'categories' => $categories
        ]);
    }

    /**
     * Delete product
     */
    public function deleteProduct() {
        $productId = (int)($_GET['id'] ?? 0);

        if (!$productId) {
            $this->redirect(BASE_URL . 'admin/products');
        }

        $product = $this->productModel->getProductById($productId);

        if ($product) {
            if ($product['image']) {
                $this->deleteImage($product['image']);
            }
            $this->productModel->deleteProduct($productId);
            $_SESSION['success'] = 'Product deleted successfully';
        }

        $this->redirect(BASE_URL . 'admin/products');
    }

    /**
     * Show users management
     */
    public function manageUsers() {
        $users = $this->userModel->getAllUsers();

        return $this->render('admin/manage_users', [
            'users' => $users
        ]);
    }

    /**
     * Show orders management
     */
    public function manageOrders() {
        $orders = $this->orderModel->getAllOrders();

        return $this->render('admin/manage_orders', [
            'orders' => $orders
        ]);
    }

    /**
     * Show categories management
     */
    public function manageCategories() {
        $categories = $this->categoryModel->getAllCategories();

        return $this->render('admin/manage_categories', [
            'categories' => $categories
        ]);
    }

    /**
     * Upload image helper
     */
    private function uploadImage($file) {
        $uploadDir = UPLOAD_DIR;

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $newFilename = 'img_' . time() . '_' . uniqid() . '.' . $fileExt;
        $targetFile = $uploadDir . $newFilename;

        $check = getimagesize($file['tmp_name']);
        if ($check === false) {
            return ['success' => false, 'message' => 'File is not a valid image'];
        }

        if ($file['size'] > 5000000) {
            return ['success' => false, 'message' => 'File is too large'];
        }

        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($fileExt, $allowedExt)) {
            return ['success' => false, 'message' => 'Invalid file type'];
        }

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return ['success' => true, 'filename' => $newFilename];
        }

        return ['success' => false, 'message' => 'Failed to upload image'];
    }

    /**
     * Delete image helper
     */
    private function deleteImage($filename) {
        $filePath = UPLOAD_DIR . $filename;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
?>
