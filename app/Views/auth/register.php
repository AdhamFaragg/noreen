<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h3 class="mb-0">Register</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($_SESSION['errors'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach ($_SESSION['errors'] as $field => $messages): ?>
                                    <?php foreach ($messages as $message): ?>
                                        <li><?php echo htmlspecialchars($message); ?></li>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['errors']); ?>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo BASE_URL; ?>?page=auth&action=handleRegister" novalidate>
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                   value="<?php echo htmlspecialchars($_SESSION['old_input']['full_name'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($_SESSION['old_input']['email'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone (Optional)</label>
                            <input type="tel" class="form-control" id="phone" name="phone"
                                   value="<?php echo htmlspecialchars($_SESSION['old_input']['phone'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-user-plus"></i> Register
                        </button>
                    </form>
                    
                    <hr>
                    
                    <p class="text-center">Already have an account? 
                        <a href="<?php echo BASE_URL; ?>?page=auth&action=login">Login here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

ob_start();
require APP_PATH . '/Views/layout.php';
?>
