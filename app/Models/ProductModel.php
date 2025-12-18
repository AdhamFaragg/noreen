<?php
/**
 * Product Model
 */
class ProductModel extends Model {
    public function __construct() {
        parent::__construct();
        $this->table = 'products';
    }

    public function getActiveProducts($limit = null, $offset = 0) {
        $sql = "SELECT p.*, c.category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.category_id 
                WHERE p.status = 'active' 
                ORDER BY p.created_at DESC";

        if ($limit !== null) {
            $limit = (int)$limit;
            $offset = (int)$offset;
            $sql .= " LIMIT {$offset}, {$limit}";
        }

        $result = $this->query($sql);
        return $this->fetch_all($result);
    }

    public function getFeaturedProducts($limit = 8) {
        $limit = (int)$limit;
        $sql = "SELECT p.*, c.category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.category_id 
                WHERE p.status = 'active' AND p.featured = 1 
                ORDER BY p.created_at DESC 
                LIMIT {$limit}";

        $result = $this->query($sql);
        return $this->fetch_all($result);
    }

    public function getProductById($productId) {
        $productId = (int)$productId;
        $sql = "SELECT p.*, c.category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.category_id 
                WHERE p.product_id = {$productId}";

        $result = $this->query($sql);
        return $this->fetch($result);
    }

    public function getProductsByCategory($categoryId, $limit = null, $offset = 0) {
        $categoryId = (int)$categoryId;
        $sql = "SELECT p.*, c.category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.category_id 
                WHERE p.category_id = {$categoryId} AND p.status = 'active' 
                ORDER BY p.created_at DESC";

        if ($limit !== null) {
            $limit = (int)$limit;
            $offset = (int)$offset;
            $sql .= " LIMIT {$offset}, {$limit}";
        }

        $result = $this->query($sql);
        return $this->fetch_all($result);
    }

    public function searchProducts($searchTerm, $limit = null, $offset = 0) {
        $searchTerm = $this->escape($searchTerm);
        $sql = "SELECT p.*, c.category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.category_id 
                WHERE p.status = 'active' 
                AND (p.product_name LIKE '%{$searchTerm}%' OR p.description LIKE '%{$searchTerm}%')
                ORDER BY p.created_at DESC";

        if ($limit !== null) {
            $limit = (int)$limit;
            $offset = (int)$offset;
            $sql .= " LIMIT {$offset}, {$limit}";
        }

        $result = $this->query($sql);
        return $this->fetch_all($result);
    }

    public function countActiveProducts() {
        $sql = "SELECT COUNT(*) as total FROM products WHERE status = 'active'";
        $result = $this->query($sql);
        $data = $this->fetch($result);
        return $data['total'] ?? 0;
    }

    public function createProduct($data) {
        $columns = ['product_name', 'description', 'price', 'category_id', 'image', 'stock', 'status', 'featured', 'created_at'];
        $values = [
            $this->escape($data['product_name']),
            $this->escape($data['description']),
            (float)$data['price'],
            (int)$data['category_id'],
            $this->escape($data['image'] ?? ''),
            (int)$data['stock'],
            $this->escape($data['status'] ?? 'active'),
            isset($data['featured']) ? 1 : 0,
            date('Y-m-d H:i:s')
        ];

        $colStr = implode(', ', $columns);
        $valStr = implode("', '", $values);
        $sql = "INSERT INTO products ({$colStr}) VALUES ('{$valStr}')";

        $this->query($sql);
        return $this->lastInsertId();
    }

    public function updateProduct($productId, $data) {
        $productId = (int)$productId;
        $set = [];

        foreach (['product_name', 'description', 'price', 'category_id', 'image', 'stock', 'status', 'featured'] as $field) {
            if (isset($data[$field])) {
                if ($field === 'featured') {
                    $set[] = "{$field} = " . (isset($data[$field]) ? 1 : 0);
                } else {
                    $set[] = "{$field} = '" . $this->escape($data[$field]) . "'";
                }
            }
        }

        if (empty($set)) {
            return 0;
        }

        $setStr = implode(', ', $set);
        $sql = "UPDATE products SET {$setStr} WHERE product_id = {$productId}";

        $this->query($sql);
        return $this->affectedRows();
    }

    public function deleteProduct($productId) {
        $productId = (int)$productId;
        $sql = "DELETE FROM products WHERE product_id = {$productId}";
        $this->query($sql);
        return $this->affectedRows();
    }
}
?>
