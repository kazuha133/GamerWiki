<?php
/**
 * Database Configuration
 * Cấu hình kết nối database cho GamerWiki
 */

// Thông tin kết nối database
$host = 'localhost';
$dbname = 'gamerwiki';
$username = 'root';
$password = '';  // Mặc định WampServer không có password

try {
    // Tạo kết nối PDO với UTF-8 encoding
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die("Lỗi kết nối database: " . $e->getMessage());
}
?>
