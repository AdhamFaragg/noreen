<?php
/**
 * Validator Singleton
 * Centralized validation logic for entire application
 * Ensures single instance and consistent validation across all controllers
 */
class Validator {
    private static $instance = null;
    private $errors = [];
    private $data = [];

    /**
     * Private constructor - prevents direct instantiation
     */
    private function __construct() {}

    /**
     * Get singleton instance
     * @return Validator
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}

    /**
     * Prevent unserializing
     */
    private function __wakeup() {}

    /**
     * Reset validator for new validation
     */
    public function reset() {
        $this->errors = [];
        $this->data = [];
        return $this;
    }

    /**
     * Set data to validate
     */
    public function setData(array $data) {
        $this->data = $data;
        return $this;
    }

    /**
     * Validate email format
     */
    public function validateEmail($email) {
        $emailRegex = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
        return preg_match($emailRegex, $email) === 1;
    }

    /**
     * Validate password strength
     */
    public function validatePasswordStrength($password) {
        $strength = [
            'score' => 0,
            'feedback' => [],
            'isValid' => true
        ];

        if (strlen($password) >= 8) {
            $strength['score']++;
        } else {
            $strength['feedback'][] = 'At least 8 characters required';
            $strength['isValid'] = false;
        }

        if (preg_match('/[a-z]/', $password) && preg_match('/[A-Z]/', $password)) {
            $strength['score']++;
        } else {
            $strength['feedback'][] = 'Mix uppercase and lowercase letters';
        }

        if (preg_match('/\d/', $password)) {
            $strength['score']++;
        } else {
            $strength['feedback'][] = 'Include at least one number';
        }

        if (preg_match('/[!@#$%^&*]/', $password)) {
            $strength['score']++;
        } else {
            $strength['feedback'][] = 'Include special characters (!@#$%^&*)';
        }

        return $strength;
    }

    /**
     * Validate phone number
     */
    public function validatePhone($phone) {
        $phoneRegex = '/^[\d\s\-\+\(\)]{7,20}$/';
        return preg_match($phoneRegex, $phone) === 1;
    }

    /**
     * Validate price (must be positive decimal)
     */
    public function validatePrice($price) {
        $priceNum = (float)$price;
        return is_numeric($price) && $priceNum > 0 && $priceNum <= 999999.99;
    }

    /**
     * Validate positive integer
     */
    public function validateInteger($value) {
        return filter_var($value, FILTER_VALIDATE_INT) !== false && (int)$value > 0;
    }

    /**
     * Validate username
     */
    public function validateUsername($username) {
        // 3-20 characters, alphanumeric and underscores only
        $usernameRegex = '/^[a-zA-Z0-9_]{3,20}$/';
        return preg_match($usernameRegex, $username) === 1;
    }

    /**
     * Validate password match
     */
    public function validatePasswordMatch($password1, $password2) {
        return $password1 === $password2;
    }

    /**
     * Validate required field
     */
    public function validateRequired($value) {
        return !empty(trim((string)$value));
    }

    /**
     * Validate min length
     */
    public function validateMinLength($value, $minLength) {
        return strlen((string)$value) >= $minLength;
    }

    /**
     * Validate max length
     */
    public function validateMaxLength($value, $maxLength) {
        return strlen((string)$value) <= $maxLength;
    }

    /**
     * Validate URL
     */
    public function validateUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Add error message
     */
    public function addError($field, $message) {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
        return $this;
    }

    /**
     * Get all errors
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Get errors for specific field
     */
    public function getFieldErrors($field) {
        return $this->errors[$field] ?? [];
    }

    /**
     * Check if there are any errors
     */
    public function hasErrors() {
        return !empty($this->errors);
    }

    /**
     * Get error count
     */
    public function getErrorCount() {
        $count = 0;
        foreach ($this->errors as $fieldErrors) {
            $count += count($fieldErrors);
        }
        return $count;
    }

    /**
     * Validate registration form
     */
    public function validateRegistration($data) {
        $this->reset();

        // Validate username
        if (empty($data['username'])) {
            $this->addError('username', 'Username is required');
        } elseif (!$this->validateUsername($data['username'])) {
            $this->addError('username', 'Username must be 3-20 characters (alphanumeric and underscores only)');
        }

        // Validate email
        if (empty($data['email'])) {
            $this->addError('email', 'Email is required');
        } elseif (!$this->validateEmail($data['email'])) {
            $this->addError('email', 'Invalid email format');
        }

        // Validate password strength
        if (empty($data['password'])) {
            $this->addError('password', 'Password is required');
        } else {
            $strength = $this->validatePasswordStrength($data['password']);
            if (!$strength['isValid']) {
                foreach ($strength['feedback'] as $feedback) {
                    $this->addError('password', $feedback);
                }
            }
        }

        // Validate password confirmation
        if (!$this->validatePasswordMatch($data['password'] ?? '', $data['confirm_password'] ?? '')) {
            $this->addError('confirm_password', 'Passwords do not match');
        }

        // Validate full name
        if (empty($data['full_name'])) {
            $this->addError('full_name', 'Full name is required');
        }

        return !$this->hasErrors();
    }

    /**
     * Validate login form
     */
    public function validateLogin($data) {
        $this->reset();

        if (empty($data['email'])) {
            $this->addError('email', 'Email is required');
        } elseif (!$this->validateEmail($data['email'])) {
            $this->addError('email', 'Invalid email format');
        }

        if (empty($data['password'])) {
            $this->addError('password', 'Password is required');
        }

        return !$this->hasErrors();
    }

    /**
     * Validate product form
     */
    public function validateProduct($data) {
        $this->reset();

        if (empty($data['product_name'])) {
            $this->addError('product_name', 'Product name is required');
        }

        if (empty($data['category_id'])) {
            $this->addError('category_id', 'Category is required');
        } elseif (!$this->validateInteger($data['category_id'])) {
            $this->addError('category_id', 'Invalid category');
        }

        if (empty($data['price'])) {
            $this->addError('price', 'Price is required');
        } elseif (!$this->validatePrice($data['price'])) {
            $this->addError('price', 'Price must be a positive number');
        }

        if (!empty($data['discount_price']) && !$this->validatePrice($data['discount_price'])) {
            $this->addError('discount_price', 'Discount price must be a positive number');
        }

        if (!empty($data['stock']) && !$this->validateInteger($data['stock'])) {
            $this->addError('stock', 'Stock must be a positive integer');
        }

        return !$this->hasErrors();
    }

    /**
     * Validate category form
     */
    public function validateCategory($data) {
        $this->reset();

        if (empty($data['category_name'])) {
            $this->addError('category_name', 'Category name is required');
        } elseif (!$this->validateMinLength($data['category_name'], 2)) {
            $this->addError('category_name', 'Category name must be at least 2 characters');
        }

        return !$this->hasErrors();
    }

    /**
     * Validate discount form
     */
    public function validateDiscount($data) {
        $this->reset();

        if (empty($data['code'])) {
            $this->addError('code', 'Discount code is required');
        }

        if (empty($data['discount_value'])) {
            $this->addError('discount_value', 'Discount value is required');
        } elseif (!is_numeric($data['discount_value']) || $data['discount_value'] <= 0) {
            $this->addError('discount_value', 'Discount value must be a positive number');
        }

        if (!empty($data['min_purchase']) && !$this->validatePrice($data['min_purchase'])) {
            $this->addError('min_purchase', 'Minimum purchase must be a valid price');
        }

        return !$this->hasErrors();
    }
}
