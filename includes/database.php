<?php
/**
 * Kết nối database sử dụng PDO
 * Cung cấp các function truy vấn cơ bản
 */

require_once __DIR__ . '/config.php';

class Database {
    private $pdo;
    
    public function __construct() {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die('Kết nối database thất bại: ' . $e->getMessage());
        }
    }
    
    /**
     * Lấy kết nối PDO
     */
    public function getConnection() {
        return $this->pdo;
    }
    
    /**
     * Thực thi query với prepared statement
     * @param string $sql
     * @param array $params
     * @return PDOStatement
     */
    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Lấy một dòng kết quả
     */
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    /**
     * Lấy tất cả kết quả
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Lấy giá trị một cột
     */
    public function fetchColumn($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchColumn();
    }
    
    /**
     * Chèn dữ liệu, trả về ID
     */
    public function insert($sql, $params = []) {
        $this->query($sql, $params);
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Cập nhật/xóa, trả về số dòng bị ảnh hưởng
     */
    public function execute($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
}

// Tạo instance database global
$db = new Database();
