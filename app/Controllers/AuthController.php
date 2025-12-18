<?php
/**
 * Auth Controller
 */
class AuthController extends Controller {
    private $userModel;

    public function __construct() {
         
        $this->userModel = new UserModel();
    }

    /**
     * Show login page
     */
    public function login() {
        if ($this->isLoggedIn()) {
            $this->redirect(BASE_URL);
        }
        return $this->render('auth/login');
    }

    /**
     * Handle login form submission
     */
    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . 'login');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Please fill in all fields';
            $this->redirect(BASE_URL . 'login');
        }

        $user = $this->userModel->authenticateUser($email, $password);

        if ($user) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];

            if ($user['role'] === 'admin' || $user['role'] === 'staff') {
                $this->redirect(BASE_URL . 'admin');
            } else {
                $this->redirect(BASE_URL);
            }
        } else {
            $_SESSION['error'] = 'Invalid email or password';
            $this->redirect(BASE_URL . 'login');
        }
    }

    /**
     * Show register page
     */
    public function register() {
        if ($this->isLoggedIn()) {
            $this->redirect(BASE_URL);
        }
        return $this->render('auth/register');
    }

    /**
     * Handle register form submission
     */
    public function handleRegister() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . 'register');
        }

        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $phone = trim($_POST['phone'] ?? '');

        // Validation
        if (empty($full_name) || empty($email) || empty($password)) {
            $_SESSION['error'] = 'Please fill in all required fields';
            $this->redirect(BASE_URL . 'register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Invalid email format';
            $this->redirect(BASE_URL . 'register');
        }

        if ($password !== $confirm_password) {
            $_SESSION['error'] = 'Passwords do not match';
            $this->redirect(BASE_URL . 'register');
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Password must be at least 6 characters';
            $this->redirect(BASE_URL . 'register');
        }

        // Check if email already exists
        $existingUser = $this->userModel->getUserByEmail($email);
        if ($existingUser) {
            $_SESSION['error'] = 'Email already registered';
            $this->redirect(BASE_URL . 'register');
        }

        // Create new user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $userId = $this->userModel->createUser([
            'full_name' => $full_name,
            'email' => $email,
            'password' => $hashedPassword,
            'phone' => $phone,
            'role' => 'customer'
        ]);

        if ($userId) {
            $_SESSION['success'] = 'Registration successful! Please login.';
            $this->redirect(BASE_URL . 'login');
        } else {
            $_SESSION['error'] = 'Registration failed. Please try again.';
            $this->redirect(BASE_URL . 'register');
        }
    }

    /**
     * Handle logout
     */
    public function logout() {
        session_destroy();
        $this->redirect(BASE_URL);
    }
}
?>
