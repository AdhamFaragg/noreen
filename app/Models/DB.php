<?php
/**
 * Database Wrapper Class
 * Centralized database connection and query execution
 */
class DB {
    private static $connection = null;

    public static function connect() {
        if (self::$connection === null) {
            $host = 'localhost';
            $user = 'root';
            $pass = '';
            $name = 'clothing_store';

            self::$connection = mysqli_connect($host, $user, $pass, $name);

            if (!self::$connection) {
                die("DB Connection failed: " . mysqli_connect_error());
            }

            mysqli_set_charset(self::$connection, "utf8");
        }

        return self::$connection;
    }

    public static function query($sql) {
        $conn = self::connect();
        $result = mysqli_query($conn, $sql);
        
        if (!$result) {
            error_log("Query Error: " . mysqli_error($conn) . " | SQL: " . $sql);
            return false;
        }

        return $result;
    }

    public static function fetch($result) {
        return mysqli_fetch_assoc($result);
    }

    public static function fetch_all($result) {
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

    public static function escape($string) {
        $conn = self::connect();
        return mysqli_real_escape_string($conn, $string);
    }

    public static function lastInsertId() {
        $conn = self::connect();
        return mysqli_insert_id($conn);
    }

    public static function affectedRows() {
        $conn = self::connect();
        return mysqli_affected_rows($conn);
    }

    public static function close() {
        if (self::$connection) {
            mysqli_close(self::$connection);
        }
    }
}
?>
