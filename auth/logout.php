<?php
require_once __DIR__ . '/../includes/functions.php';

khoi_tao_session();

// Xóa tất cả session variables
$_SESSION = array();

// Hủy session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Redirect về trang đăng nhập
header('Location: /auth/login.php');
exit();
?>
