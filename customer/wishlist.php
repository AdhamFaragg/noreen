<?php
require_once '../db/config.php';
require_once '../includes/functions.php';

require_login();

// Handle remove from wishlist
if (isset($_GET['remove'])) {
    $wishlist_id = (int)$_GET['remove'];
    mysqli_query($conn, "DELETE FROM wishlist WHERE wishlist_id = $wishlist_id AND user_id = {$_SESSION['user_id']}");
    set_message('Item removed from wishlist.', 'success');
    redirect(BASE_URL . 'customer/wishlist.php');
}

// Get wishlist items
$query = "SELECT w.*, p.* FROM wishlist w 
          JOIN products p ON w.product_id = p.product_id 
          WHERE w.user_id = {$_SESSION['user_id']} 
          ORDER BY w.created_at DESC";
$result = mysqli_query($conn, $query);

include '../includes/header.php';
?>

<div class="container mt-4 mb-5">
    <h2 class="mb-4"><i class="fas fa-heart"></i> My Wishlist</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
    <div class="row">
        <?php while ($item = mysqli_fetch_assoc($result)): ?>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card h-100">
                <a href="product_details.php?id=<?php echo $item['product_id']; ?>">
                    <?php if ($item['image']): ?>
                        <img src="<?php echo UPLOAD_URL . $item['image']; ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                             style="height: 250px; object-fit: cover;">
                    <?php else: ?>
                        <div style="height: 250px; background: #ddd; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    <?php endif; ?>
                </a>
                
                <div class="card-body">
                    <h6 class="card-title">
                        <a href="product_details.php?id=<?php echo $item['product_id']; ?>" class="text-decoration-none text-dark">
                            <?php echo htmlspecialchars(truncate_text($item['product_name'], 50)); ?>
                        </a>
                    </h6>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <?php if ($item['discount_price']): ?>
                            <div>
                                <span class="text-muted text-decoration-line-through small"><?php echo format_price($item['price']); ?></span><br>
                                <strong class="text-danger"><?php echo format_price($item['discount_price']); ?></strong>
                            </div>
                        <?php else: ?>
                            <strong><?php echo format_price($item['price']); ?></strong>
                        <?php endif; ?>
                        
                        <?php if ($item['stock'] > 0): ?>
                            <span class="badge bg-success">In Stock</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Out of Stock</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex gap-2">
                        <a href="product_details.php?id=<?php echo $item['product_id']; ?>" class="btn btn-primary btn-sm flex-grow-1">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="?remove=<?php echo $item['wishlist_id']; ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Remove from wishlist?');">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="text-center py-5">
        <i class="fas fa-heart fa-5x text-muted mb-3"></i>
        <h4>Your wishlist is empty</h4>
        <p class="text-muted">Save your favorite items here!</p>
        <a href="index.php" class="btn btn-primary">
            <i class="fas fa-shopping-bag"></i> Browse Products
        </a>
    </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
