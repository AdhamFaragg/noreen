<?php
require_once 'db/config.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect(BASE_URL . 'index.php');
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = sanitize_input($_POST['full_name']);
    $phone = sanitize_input($_POST['phone'] ?? '');
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
        $errors[] = 'Please fill in all required fields.';
    }
    
    if (strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters long.';
    } elseif (strlen($username) > 50) {
        $errors[] = 'Username cannot exceed 50 characters.';
    } elseif (!preg_match('/^[a-zA-Z0-9._-]+$/', $username)) {
        $errors[] = 'Username can only contain letters, numbers, dots, underscores, and hyphens.';
    }
    
    if (!validate_email($email)) {
        $errors[] = 'Please enter a valid email address.';
    } elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $email)) {
        $errors[] = 'Email must be a Gmail address (@gmail.com).';
    } elseif (strlen($email) > 100) {
        $errors[] = 'Email cannot exceed 100 characters.';
    }
    
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    } elseif (strlen($password) > 100) {
        $errors[] = 'Password cannot exceed 100 characters.';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }
    
    if (strlen($full_name) < 2) {
        $errors[] = 'Full name must be at least 2 characters long.';
    } elseif (strlen($full_name) > 100) {
        $errors[] = 'Full name cannot exceed 100 characters.';
    } elseif (!preg_match('/^[a-zA-Z ]+$/', $full_name)) {
        $errors[] = 'Full name can only contain letters and spaces.';
    }
    
    if (!empty($phone) && !preg_match('/^[0-9 +()-]+$/', $phone)) {
        $errors[] = 'Phone number format is invalid.';
    }
    
    // Check if username exists
    if (empty($errors)) {
        $check_query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $errors[] = 'Username or email already exists.';
        }
    }
    
    // Insert user
    if (empty($errors)) {
        $hashed_password = hash_password($password);
        
        // Escape strings for database
        $username_db = mysqli_real_escape_string($conn, $username);
        $email_db = mysqli_real_escape_string($conn, $email);
        $full_name_db = mysqli_real_escape_string($conn, $full_name);
        $phone_db = mysqli_real_escape_string($conn, $phone);
        
        $query = "INSERT INTO users (username, email, password, full_name, phone, role) 
                  VALUES ('$username_db', '$email_db', '$hashed_password', '$full_name_db', '$phone_db', 'customer')";
        
        if (mysqli_query($conn, $query)) {
            set_message('Registration successful! Please login to continue.', 'success');
            redirect(BASE_URL . 'login.php');
        } else {
            $errors[] = 'Registration failed. Please try again.';
        }
    }
}

include 'includes/header.php';
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h3 class="text-center mb-4">
                        <i class="fas fa-user-plus"></i> Register
                    </h3>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" novalidate>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                                       minlength="3" maxlength="50" required
                                       pattern="[a-zA-Z0-9._-]+"
                                       title="Username must be 3+ characters with only letters, numbers, dots, underscores, or hyphens">
                                <div class="invalid-feedback">Username must be at least 3 characters and contain only valid characters.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                       pattern="[a-zA-Z0-9._%+-]+@gmail\.com"
                                       maxlength="100" required
                                       title="Email must be a Gmail address (@gmail.com)">
                                <div class="invalid-feedback">Please provide a valid Gmail address (e.g., user@gmail.com).</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                   value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" 
                                   minlength="2" maxlength="100" required
                                   pattern="[a-zA-Z ]+"
                                   title="Full name must contain only letters and spaces">
                            <div class="invalid-feedback">Please provide a valid full name (2+ characters).</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                                   pattern="[0-9 +()-]+"
                                   maxlength="20"
                                   title="Phone must contain only numbers, spaces, hyphens, plus, or parentheses">
                            <div class="invalid-feedback">Please provide a valid phone number.</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       minlength="6" maxlength="100" required>
                                <small class="text-muted">Minimum 6 characters</small>
                                <div class="invalid-feedback">Password must be at least 6 characters long.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                       minlength="6" maxlength="100" required>
                                <div class="invalid-feedback">Passwords must match and be at least 6 characters.</div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-user-plus"></i> Register
                        </button>
                        
                        <div class="text-center">
                            <p class="mb-0">Already have an account? 
                                <a href="login.php">Login here</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
