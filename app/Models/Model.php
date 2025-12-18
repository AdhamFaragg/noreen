<?php
/**
 * Base Model Class
 * Provides common functionality for all models
 */
class Model {
    protected $table;
    protected $db;

    public function __construct() {
        $this->db = DB::connect();
    }

    protected function query($sql) {
        return DB::query($sql);
    }

    protected function fetch($result) {
        return DB::fetch($result);
    }

    protected function fetch_all($result) {
        return DB::fetch_all($result);
    }

    protected function escape($string) {
        return DB::escape($string);
    }

    protected function lastInsertId() {
        return DB::lastInsertId();
    }

    protected function affectedRows() {
        return DB::affectedRows();
    }

    /**
     * Generic method to get all records
     */
    public function getAll($orderBy = 'id DESC', $limit = null) {
        $sql = "SELECT * FROM {$this->table}";
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        $result = $this->query($sql);
        return $this->fetch_all($result);
    }

    /**
     * Get record by ID
     */
    public function getById($id) {
        $id = (int)$id;
        $sql = "SELECT * FROM {$this->table} WHERE id = {$id}";
        $result = $this->query($sql);
        return $this->fetch($result);
    }

    /**
     * Insert a new record
     */
    public function insert($data) {
        $columns = implode(', ', array_keys($data));
        $values = implode("', '", array_map([$this, 'escape'], $data));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ('{$values}')";
        
        $this->query($sql);
        return $this->lastInsertId();
    }

    /**
     * Update a record
     */
    public function update($id, $data) {
        $id = (int)$id;
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "{$key} = '" . $this->escape($value) . "'";
        }
        $setStr = implode(', ', $set);
        $sql = "UPDATE {$this->table} SET {$setStr} WHERE id = {$id}";
        
        $this->query($sql);
        return $this->affectedRows();
    }

    /**
     * Delete a record
     */
    public function delete($id) {
        $id = (int)$id;
        $sql = "DELETE FROM {$this->table} WHERE id = {$id}";
        $this->query($sql);
        return $this->affectedRows();
    }
}
?>
