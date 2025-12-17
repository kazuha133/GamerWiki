<?php
/**
 * Logout Page
 * Đăng xuất và xóa session
 */

session_start();

// Xóa tất cả session variables
$_SESSION = array();

// Xóa session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy session
session_destroy();

// Redirect về trang chủ
header('Location: /GamerWiki/index.php');
exit();
?>
