<?php
/**
 * Category Model
 */
class CategoryModel extends Model {
    public function __construct() {
        parent::__construct();
        $this->table = 'categories';
    }

    public function getCategoryById($categoryId) {
        $categoryId = (int)$categoryId;
        $sql = "SELECT * FROM categories WHERE category_id = {$categoryId}";
        $result = $this->query($sql);
        return $this->fetch($result);
    }

    public function getAllCategories() {
        $sql = "SELECT * FROM categories ORDER BY category_name ASC";
        $result = $this->query($sql);
        return $this->fetch_all($result);
    }

    public function createCategory($data) {
        $sql = "INSERT INTO categories (category_name, description, created_at)
                VALUES (
                    '{$this->escape($data['category_name'])}',
                    '{$this->escape($data['description'] ?? '')}',
                    '" . date('Y-m-d H:i:s') . "'
                )";

        $this->query($sql);
        return $this->lastInsertId();
    }

    public function updateCategory($categoryId, $data) {
        $categoryId = (int)$categoryId;
        $set = [];

        if (isset($data['category_name'])) {
            $set[] = "category_name = '" . $this->escape($data['category_name']) . "'";
        }
        if (isset($data['description'])) {
            $set[] = "description = '" . $this->escape($data['description']) . "'";
        }

        if (empty($set)) {
            return 0;
        }

        $setStr = implode(', ', $set);
        $sql = "UPDATE categories SET {$setStr} WHERE category_id = {$categoryId}";

        $this->query($sql);
        return $this->affectedRows();
    }

    public function deleteCategory($categoryId) {
        $categoryId = (int)$categoryId;
        $sql = "DELETE FROM categories WHERE category_id = {$categoryId}";
        $this->query($sql);
        return $this->affectedRows();
    }

    public function countCategories() {
        $sql = "SELECT COUNT(*) as total FROM categories";
        $result = $this->query($sql);
        $data = $this->fetch($result);
        return $data['total'] ?? 0;
    }
}
?>
