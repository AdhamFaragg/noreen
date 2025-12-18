<?php
/**
 * User Model
 */
class UserModel extends Model {
    public function __construct() {
        parent::__construct();
        $this->table = 'users';
    }

    public function getUserById($userId) {
        $userId = (int)$userId;
        $sql = "SELECT * FROM users WHERE user_id = {$userId}";
        $result = $this->query($sql);
        return $this->fetch($result);
    }

    public function getUserByEmail($email) {
        $email = $this->escape($email);
        $sql = "SELECT * FROM users WHERE email = '{$email}'";
        $result = $this->query($sql);
        return $this->fetch($result);
    }

    public function getAllUsers($limit = null, $offset = 0) {
        $sql = "SELECT * FROM users ORDER BY created_at DESC";

        if ($limit !== null) {
            $limit = (int)$limit;
            $offset = (int)$offset;
            $sql .= " LIMIT {$offset}, {$limit}";
        }

        $result = $this->query($sql);
        return $this->fetch_all($result);
    }

    public function getUsersByRole($role, $limit = null, $offset = 0) {
        $role = $this->escape($role);
        $sql = "SELECT * FROM users WHERE role = '{$role}' ORDER BY created_at DESC";

        if ($limit !== null) {
            $limit = (int)$limit;
            $offset = (int)$offset;
            $sql .= " LIMIT {$offset}, {$limit}";
        }

        $result = $this->query($sql);
        return $this->fetch_all($result);
    }

    public function createUser($data) {
        $sql = "INSERT INTO users (full_name, email, password, phone, address, role, created_at)
                VALUES (
                    '{$this->escape($data['full_name'])}',
                    '{$this->escape($data['email'])}',
                    '{$this->escape($data['password'])}',
                    '{$this->escape($data['phone'] ?? '')}',
                    '{$this->escape($data['address'] ?? '')}',
                    '{$this->escape($data['role'] ?? 'customer')}',
                    '" . date('Y-m-d H:i:s') . "'
                )";

        $this->query($sql);
        return $this->lastInsertId();
    }

    public function updateUser($userId, $data) {
        $userId = (int)$userId;
        $set = [];

        $allowedFields = ['full_name', 'email', 'phone', 'address', 'role'];
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $set[] = "{$field} = '" . $this->escape($data[$field]) . "'";
            }
        }

        if (isset($data['password'])) {
            $set[] = "password = '" . $this->escape($data['password']) . "'";
        }

        if (empty($set)) {
            return 0;
        }

        $setStr = implode(', ', $set);
        $sql = "UPDATE users SET {$setStr} WHERE user_id = {$userId}";

        $this->query($sql);
        return $this->affectedRows();
    }

    public function deleteUser($userId) {
        $userId = (int)$userId;
        $sql = "DELETE FROM users WHERE user_id = {$userId}";
        $this->query($sql);
        return $this->affectedRows();
    }

    public function authenticateUser($email, $password) {
        $user = $this->getUserByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }

    public function countUsers() {
        $sql = "SELECT COUNT(*) as total FROM users";
        $result = $this->query($sql);
        $data = $this->fetch($result);
        return $data['total'] ?? 0;
    }
}
?>
