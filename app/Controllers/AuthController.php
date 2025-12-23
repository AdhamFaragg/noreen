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

        // Collect form data
        $formData = [
            'full_name' => trim($_POST['full_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? '',
            'phone' => trim($_POST['phone'] ?? '')
        ];

        // Get validator singleton
        $validator = Validator::getInstance();
        $validator->reset();

        // Step 1: Validate format
        if (empty($formData['full_name'])) {
            $validator->addError('full_name', 'Full name is required');
        }

        if (empty($formData['email'])) {
            $validator->addError('email', 'Email is required');
        } elseif (!$validator->validateEmail($formData['email'])) {
            $validator->addError('email', 'Invalid email format');
        }

        if (empty($formData['password'])) {
            $validator->addError('password', 'Password is required');
        } else {
            $strength = $validator->validatePasswordStrength($formData['password']);
            if (!$strength['isValid']) {
                foreach ($strength['feedback'] as $feedback) {
                    $validator->addError('password', $feedback);
                }
            }
        }

        if (!$validator->validatePasswordMatch($formData['password'], $formData['confirm_password'])) {
            $validator->addError('confirm_password', 'Passwords do not match');
        }

        // If format validation fails, save old input and redirect
        if ($validator->hasErrors()) {
            $_SESSION['errors'] = $validator->getErrors();
            $_SESSION['old_input'] = $formData;
            $this->redirect(BASE_URL . 'register');
        }

        // Step 2: Check if email already exists (ONLY if format validation passed)
        $existingUser = $this->userModel->getUserByEmail($formData['email']);
        if ($existingUser) {
            $validator->addError('email', 'Email already registered');
            $_SESSION['errors'] = $validator->getErrors();
            $_SESSION['old_input'] = $formData;
            $this->redirect(BASE_URL . 'register');
        }

        // Step 3: Create user (ONLY if all validation passed)
        $hashedPassword = password_hash($formData['password'], PASSWORD_DEFAULT);
        $userId = $this->userModel->createUser([
            'full_name' => $formData['full_name'],
            'email' => $formData['email'],
            'password' => $hashedPassword,
            'phone' => $formData['phone'],
            'role' => 'customer'
        ]);

        if ($userId) {
            // Clear old input on successful registration
            unset($_SESSION['old_input']);
            unset($_SESSION['errors']);
            $_SESSION['success'] = 'Registration successful! Please login.';
            $this->redirect(BASE_URL . 'login');
        } else {
            $_SESSION['error'] = 'Registration failed. Please try again.';
            $_SESSION['old_input'] = $formData;
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
