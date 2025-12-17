<?php
/**
 * Script tạo tài khoản customer
 * URL: http://localhost/GamerWiki/test_create_customer. php
 */

require_once __DIR__ . '/config/database.php';

echo "<h2>🔐 Tạo tài khoản Customer</h2>";

// Tạo password hash mới
$password_plain = 'customer123';
$password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

echo "<p><strong>Password: </strong> $password_plain</p>";
echo "<p><strong>Hash:</strong> $password_hash</p>";

try {
    // Xóa customer cũ
    $stmt = $conn->prepare("DELETE FROM nguoi_dung WHERE ten_dang_nhap = ? ");
    $stmt->execute(['customer']);
    
    // Tạo customer mới
    $stmt = $conn->prepare("INSERT INTO nguoi_dung (ten_dang_nhap, mat_khau, email, vai_tro, trang_thai) VALUES (?, ?, ?, ?, ?)");
    $result = $stmt->execute(['customer', $password_hash, 'customer@gamerwiki.com', 'customer', 'active']);
    
    if ($result) {
        echo "<p style='color: green; font-weight: bold;'>✅ Tài khoản customer đã được tạo thành công!</p>";
        
        // Verify lại
        $stmt = $conn->prepare("SELECT * FROM nguoi_dung WHERE ten_dang_nhap = ?");
        $stmt->execute(['customer']);
        $customer = $stmt->fetch();
        
        if ($customer && password_verify($password_plain, $customer['mat_khau'])) {
            echo "<p style='color: green; font-size: 18px;'>✅✅✅ Password VERIFY THÀNH CÔNG!</p>";
            echo "<hr>";
            echo "<h3>🎉 Đăng nhập với:</h3>";
            echo "<p><strong>Username:</strong> customer</p>";
            echo "<p><strong>Password:</strong> customer123</p>";
            echo "<p><a href='auth/login.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Đi đến trang đăng nhập</a></p>";
        }
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Lỗi: " . $e->getMessage() . "</p>";
}

echo "<hr><p><strong>⚠️ Xóa file này sau khi xong!</strong></p>";
?>