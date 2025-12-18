<?php
/**
 * Order Model
 */
class OrderModel extends Model {
    public function __construct() {
        parent::__construct();
        $this->table = 'orders';
    }

    public function getOrderById($orderId) {
        $orderId = (int)$orderId;
        $sql = "SELECT o.*, u.full_name, u.email, u.phone, u.address
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.user_id
                WHERE o.order_id = {$orderId}";

        $result = $this->query($sql);
        return $this->fetch($result);
    }

    public function getOrdersByUser($userId, $limit = null, $offset = 0) {
        $userId = (int)$userId;
        $sql = "SELECT * FROM orders WHERE user_id = {$userId} ORDER BY order_date DESC";

        if ($limit !== null) {
            $limit = (int)$limit;
            $offset = (int)$offset;
            $sql .= " LIMIT {$offset}, {$limit}";
        }

        $result = $this->query($sql);
        return $this->fetch_all($result);
    }

    public function getAllOrders($limit = null, $offset = 0) {
        $sql = "SELECT o.*, u.full_name, u.email
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.user_id
                ORDER BY o.order_date DESC";

        if ($limit !== null) {
            $limit = (int)$limit;
            $offset = (int)$offset;
            $sql .= " LIMIT {$offset}, {$limit}";
        }

        $result = $this->query($sql);
        return $this->fetch_all($result);
    }

    public function createOrder($data) {
        $sql = "INSERT INTO orders (user_id, order_date, status, total_amount, payment_method, shipping_address)
                VALUES (
                    {$data['user_id']},
                    '" . date('Y-m-d H:i:s') . "',
                    '{$this->escape($data['status'] ?? 'pending')}',
                    {$data['total_amount']},
                    '{$this->escape($data['payment_method'] ?? 'credit_card')}',
                    '{$this->escape($data['shipping_address'] ?? '')}'
                )";

        $this->query($sql);
        return $this->lastInsertId();
    }

    public function updateOrder($orderId, $data) {
        $orderId = (int)$orderId;
        $set = [];

        $allowedFields = ['status', 'total_amount', 'payment_method', 'shipping_address'];
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                if (in_array($field, ['total_amount'])) {
                    $set[] = "{$field} = " . (float)$data[$field];
                } else {
                    $set[] = "{$field} = '" . $this->escape($data[$field]) . "'";
                }
            }
        }

        if (empty($set)) {
            return 0;
        }

        $setStr = implode(', ', $set);
        $sql = "UPDATE orders SET {$setStr} WHERE order_id = {$orderId}";

        $this->query($sql);
        return $this->affectedRows();
    }

    public function countOrders() {
        $sql = "SELECT COUNT(*) as total FROM orders";
        $result = $this->query($sql);
        $data = $this->fetch($result);
        return $data['total'] ?? 0;
    }

    public function getTotalRevenue() {
        $sql = "SELECT SUM(total_amount) as revenue FROM orders WHERE status = 'completed'";
        $result = $this->query($sql);
        $data = $this->fetch($result);
        return $data['revenue'] ?? 0;
    }
}
?>
