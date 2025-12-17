<?php
/**
 * Script tạo tài khoản admin và test password
 * Chạy file này 1 lần để tạo admin account
 * URL: http://localhost/GamerWiki/test_create_admin.php
 */

require_once __DIR__ . '/config/database.php';

echo "<h2>🔐 Test và tạo tài khoản Admin</h2>";

// 1. Tạo password hash mới
$password_plain = 'admin123';
$password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

echo "<h3>1. Password Hash Info:</h3>";
echo "<p><strong>Plain Password:</strong> $password_plain</p>";
echo "<p><strong>Hash:</strong> $password_hash</p>";
echo "<p><strong>Hash Length:</strong> " . strlen($password_hash) . "</p>";

// 2. Test verify
if (password_verify($password_plain, $password_hash)) {
    echo "<p style='color: green;'>✅ Password verify: SUCCESS</p>";
} else {
    echo "<p style='color: red;'>❌ Password verify: FAILED</p>";
}

// 3. Xóa admin cũ và tạo mới
try {
    // Xóa admin cũ
    $stmt = $conn->prepare("DELETE FROM nguoi_dung WHERE ten_dang_nhap = ?");
    $stmt->execute(['admin']);
    
    // Tạo admin mới
    $stmt = $conn->prepare("INSERT INTO nguoi_dung (ten_dang_nhap, mat_khau, email, vai_tro, trang_thai) VALUES (?, ?, ?, ?, ?)");
    $result = $stmt->execute(['admin', $password_hash, 'admin@gamerwiki.com', 'admin', 'active']);
    
    if ($result) {
        echo "<h3>2. Database Insert:</h3>";
        echo "<p style='color: green;'>✅ Tài khoản admin đã được tạo thành công!</p>";
        
        // Kiểm tra lại trong database
        $stmt = $conn->prepare("SELECT * FROM nguoi_dung WHERE ten_dang_nhap = ?");
        $stmt->execute(['admin']);
        $admin = $stmt->fetch();
        
        if ($admin) {
            echo "<h3>3. Verify trong Database:</h3>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><td>" . $admin['id'] . "</td></tr>";
            echo "<tr><th>Username</th><td>" . $admin['ten_dang_nhap'] . "</td></tr>";
            echo "<tr><th>Email</th><td>" . $admin['email'] . "</td></tr>";
            echo "<tr><th>Vai trò</th><td>" . $admin['vai_tro'] . "</td></tr>";
            echo "<tr><th>Trạng thái</th><td>" . $admin['trang_thai'] . "</td></tr>";
            echo "<tr><th>Hash in DB</th><td>" . substr($admin['mat_khau'], 0, 30) . "...</td></tr>";
            echo "</table>";
            
            // Test verify với hash từ DB
            if (password_verify($password_plain, $admin['mat_khau'])) {
                echo "<p style='color: green; font-weight: bold; font-size: 18px;'>✅✅✅ Password trong DB VERIFY THÀNH CÔNG!</p>";
                echo "<hr>";
                echo "<h3>🎉 Bây giờ bạn có thể đăng nhập với:</h3>";
                echo "<p><strong>Username:</strong> admin</p>";
                echo "<p><strong>Password:</strong> admin123</p>";
                echo "<p><a href='auth/login.php' style='background: #0d6efd; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Đi đến trang đăng nhập</a></p>";
            } else {
                echo "<p style='color: red;'>❌ Password trong DB KHÔNG VERIFY - CÓ VẤN ĐỀ!</p>";
            }
        }
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Lỗi Database: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>⚠️ LƯU Ý:</strong> Xóa file này sau khi tạo xong tài khoản admin!</p>";
?>
