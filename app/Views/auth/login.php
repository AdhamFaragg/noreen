<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h3 class="mb-0">Login</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo BASE_URL; ?>?page=auth&action=handleLogin">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                    </form>
                    
                    <hr>
                    
                    <p class="text-center">Don't have an account? 
                        <a href="<?php echo BASE_URL; ?>?page=auth&action=register">Register here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

ob_start();
require APP_PATH . '/Views/layout.php';
?>
