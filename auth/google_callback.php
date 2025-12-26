<?php
/**
 * Google OAuth Callback Handler
 * Xử lý callback từ Google OAuth 2.0
 */

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/google_config.php';

// Kiểm tra nếu đã đăng nhập thì redirect về trang chủ
if (kiem_tra_dang_nhap()) {
    header('Location: ' . url('index.php'));
    exit();
}

try {
    // Kiểm tra có authorization code không
    if (!isset($_GET['code'])) {
        header('Location: ' . url('auth/login.php?error=no_code'));
        exit();
    }
    
    // Kiểm tra vendor autoload có tồn tại không
    if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
        header('Location: ' . url('auth/login.php?error=composer_not_installed'));
        exit();
    }
    
    // Khởi tạo Google Client
    $google_client = get_google_client();
    
    // Đổi authorization code lấy access token
    $token = $google_client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    // Kiểm tra lỗi khi lấy token
    if (isset($token['error'])) {
        header('Location: ' . url('auth/login.php?error=token_error'));
        exit();
    }
    
    // Set access token
    $google_client->setAccessToken($token);
    
    // Lấy thông tin user từ Google
    $google_oauth = new Google_Service_Oauth2($google_client);
    $google_account_info = $google_oauth->userinfo->get();
    
    // Lấy thông tin cần thiết
    $google_id = $google_account_info->id;
    $email = $google_account_info->email;
    $name = $google_account_info->name;
    
    // Validate email
    if (!validate_email($email)) {
        header('Location: ' . url('auth/login.php?error=invalid_email'));
        exit();
    }
    
    // Kiểm tra user đã tồn tại chưa (theo email hoặc google_id)
    $stmt = $conn->prepare("SELECT id, ten_dang_nhap, vai_tro, trang_thai, google_id FROM nguoi_dung WHERE email = ? OR google_id = ?");
    $stmt->execute([$email, $google_id]);
    $nguoi_dung = $stmt->fetch();
    
    if ($nguoi_dung) {
        // User đã tồn tại
        // Kiểm tra trạng thái tài khoản
        if ($nguoi_dung['trang_thai'] === 'inactive') {
            header('Location: ' . url('auth/login.php?error=account_inactive'));
            exit();
        }
        
        // Cập nhật google_id nếu chưa có
        if (empty($nguoi_dung['google_id'])) {
            $update_stmt = $conn->prepare("UPDATE nguoi_dung SET google_id = ? WHERE id = ?");
            $update_stmt->execute([$google_id, $nguoi_dung['id']]);
        }
        
        // Đăng nhập
        session_regenerate_id(true);
        $_SESSION['nguoi_dung_id'] = $nguoi_dung['id'];
        $_SESSION['ten_dang_nhap'] = $nguoi_dung['ten_dang_nhap'];
        $_SESSION['vai_tro'] = $nguoi_dung['vai_tro'];
        
        header('Location: ' . url('index.php'));
        exit();
        
    } else {
        // User chưa tồn tại - tạo mới
        
        // Tạo username từ email (phần trước @)
        $email_parts = explode('@', $email);
        $base_username = sanitize_input($email_parts[0]);
        $username = $base_username;
        
        // Kiểm tra và tránh trùng username (với giới hạn tối đa 100 lần thử)
        $counter = 1;
        $max_attempts = 100;
        while ($counter <= $max_attempts) {
            $check_stmt = $conn->prepare("SELECT id FROM nguoi_dung WHERE ten_dang_nhap = ?");
            $check_stmt->execute([$username]);
            if (!$check_stmt->fetch()) {
                break; // Username available
            }
            $username = $base_username . $counter;
            $counter++;
        }
        
        // Nếu vượt quá số lần thử, báo lỗi
        if ($counter > $max_attempts) {
            header('Location: ' . url('auth/login.php?error=username_generation_failed'));
            exit();
        }
        
        // Tạo random password (user không cần biết vì đăng nhập bằng Google)
        $random_password = bin2hex(random_bytes(16));
        $hashed_password = password_hash($random_password, PASSWORD_DEFAULT);
        
        // Insert user mới
        $insert_stmt = $conn->prepare(
            "INSERT INTO nguoi_dung (ten_dang_nhap, mat_khau, email, vai_tro, trang_thai, google_id) 
             VALUES (?, ?, ?, 'user', 'active', ?)"
        );
        $insert_stmt->execute([$username, $hashed_password, $email, $google_id]);
        
        $new_user_id = $conn->lastInsertId();
        
        // Đăng nhập với user mới
        session_regenerate_id(true);
        $_SESSION['nguoi_dung_id'] = $new_user_id;
        $_SESSION['ten_dang_nhap'] = $username;
        $_SESSION['vai_tro'] = 'user';
        
        header('Location: ' . url('index.php?welcome=1'));
        exit();
    }
    
} catch (Exception $e) {
    // HIỂN THỊ LỖI CHI TIẾT
    echo "<! DOCTYPE html>";
    echo "<html><head><meta charset='UTF-8'><title>Google Login Error</title></head><body>";
    echo "<h2 style='color: red;'>🔴 Google Login Error Debug</h2>";
    echo "<h3>Error Message: </h3>";
    echo "<p style='background:  #ffebee; padding: 15px; border: 2px solid red;'>";
    echo htmlspecialchars($e->getMessage());
    echo "</p>";
    echo "<h3>Error Location:</h3>";
    echo "<p>File: " . $e->getFile() . "<br>";
    echo "Line:  " . $e->getLine() . "</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre style='background: #f5f5f5; padding: 15px; overflow:  auto;'>";
    echo htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
    echo "<br><a href='" . url('auth/login. php') . "'>← Back to Login</a>";
    echo "</body></html>";
    exit();
}
?>
