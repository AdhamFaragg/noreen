<div class="container-fluid mt-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>Manage Products</h1>
            <a href="<?php echo BASE_URL; ?>?page=admin&action=addProduct" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Product
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['product_id']; ?></td>
                                <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo $product['stock']; ?></td>
                                <td><span class="badge bg-success"><?php echo $product['status']; ?></span></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>?page=admin&action=editProduct&id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>?page=admin&action=deleteProduct&id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No products found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <a href="<?php echo BASE_URL; ?>?page=admin&action=dashboard" class="btn btn-outline-secondary mt-3">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

ob_start();
require APP_PATH . '/Views/layout.php';
?>
