# Validator Singleton Pattern Implementation

## Overview
The Validator Singleton ensures a **single instance** of validation logic throughout your application. This prevents duplication and maintains consistency across all validations.

## Files Created/Modified

### 1. **Backend (PHP)**
- **File**: `app/Services/Validator.php`
- **Type**: Singleton class
- **Purpose**: Centralized server-side validation

### 2. **Frontend (JavaScript)**
- **File**: `assets/js/form-validation.js`
- **Type**: Module pattern with Singleton
- **Purpose**: Centralized client-side validation

### 3. **Examples**
- **File**: `app/Services/ValidatorExample.php`
- **Type**: Usage examples
- **Purpose**: Reference for implementing in Controllers

---

## How It Works

### PHP Singleton Pattern
```php
// Get the single instance
$validator = Validator::getInstance();

// Use validation methods
$validator->validateEmail('user@example.com');
$validator->validatePassword('SecurePass123!');

// Validate entire forms
$validator->validateRegistration($formData);

// Check errors
if ($validator->hasErrors()) {
    $errors = $validator->getErrors();
}
```

### JavaScript Singleton Pattern
```javascript
// Get the single instance
const validator = FormValidator.getInstance();

// Methods are automatically available
validator.validateEmail('user@example.com');
validator.validatePasswordStrength('SecurePass123!');
```

---

## Available Validation Methods

### Email & Phone
- `validateEmail(email)` - Checks email format
- `validatePhone(phone)` - Validates phone number (7-20 characters)

### Password
- `validatePasswordStrength(password)` - Returns strength score and feedback
- `validatePasswordMatch(pwd1, pwd2)` - Checks if passwords match

### Numeric Values
- `validatePrice(price)` - Positive decimal up to 999999.99
- `validateInteger(value)` - Positive integer validation

### Text
- `validateUsername(username)` - 3-20 chars, alphanumeric + underscores
- `validateMinLength(value, length)` - Minimum character count
- `validateMaxLength(value, length)` - Maximum character count
- `validateRequired(value)` - Non-empty check

### URL
- `validateUrl(url)` - Valid URL format

### Pre-built Form Validations
- `validateRegistration(data)` - Validates entire registration form
- `validateLogin(data)` - Validates login credentials
- `validateProduct(data)` - Validates product data
- `validateCategory(data)` - Validates category data
- `validateDiscount(data)` - Validates discount form

---

## Usage in Controllers

### Step 1: Include the Validator
```php
// In your bootstrap or controller
require_once 'app/Services/Validator.php';
```

### Step 2: Use in Controller Method
```php
public function handleRegister() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect(BASE_URL . 'register');
    }

    // Get singleton instance
    $validator = Validator::getInstance();

    // Prepare form data
    $formData = [
        'username' => trim($_POST['username'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? '',
        'full_name' => trim($_POST['full_name'] ?? '')
    ];

    // Validate
    if (!$validator->validateRegistration($formData)) {
        $_SESSION['errors'] = $validator->getErrors();
        $_SESSION['old_input'] = $formData;
        $this->redirect(BASE_URL . 'register');
    }

    // Continue with registration logic...
    $user = $this->userModel->createUser($formData);
}
```

---

## Error Handling

### Get All Errors
```php
$errors = $validator->getErrors();
// Returns: ['email' => ['Invalid format'], 'password' => ['Too weak']]
```

### Get Field-Specific Errors
```php
$emailErrors = $validator->getFieldErrors('email');
// Returns: ['Invalid format']
```

### Check for Errors
```php
if ($validator->hasErrors()) {
    // Handle errors
}
```

### Error Count
```php
$count = $validator->getErrorCount();
```

---

## Reset Between Validations
```php
$validator = Validator::getInstance();

// First validation
$validator->validateLogin($loginData);

// Reset for new validation
$validator->reset();

// Second validation
$validator->validateProduct($productData);
```

---

## Benefits of Singleton Pattern

✅ **Single Instance** - Only one validator exists in memory
✅ **Consistent** - Same validation rules everywhere
✅ **Easy Maintenance** - Update rules in one place
✅ **Memory Efficient** - No duplicate validator objects
✅ **Centralized** - All validation logic in one file
✅ **Reusable** - Use same methods across all controllers

---

## Design Diagram Integration

```
┌─────────────────────────────────────────┐
│         APPLICATION LAYER               │
├─────────────────────────────────────────┤
│  Controllers (Auth, Admin, Product...)  │
│           ↓                             │
│  ┌─────────────────────────────────┐   │
│  │  Validator Singleton            │   │
│  │  ├─ getInstance()               │   │
│  │  ├─ validateEmail()             │   │
│  │  ├─ validatePassword()          │   │
│  │  ├─ validateRegistration()      │   │
│  │  └─ ...more methods             │   │
│  └─────────────────────────────────┘   │
│           ↓                             │
│  Models (User, Product, Order...)       │
└─────────────────────────────────────────┘
```

---

## Next Steps

1. **Update AuthController** - Use `validateRegistration()` and `validateLogin()`
2. **Update AdminController** - Use `validateProduct()`, `validateCategory()`, `validateDiscount()`
3. **Frontend Form Validation** - Use `FormValidator.getInstance()` for client-side checks
4. **Error Display** - Update views to display `$_SESSION['errors']` from validator
