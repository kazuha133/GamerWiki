<?php
/**
 * Authentication Functions
 * Các hàm xác thực và phân quyền
 */

// Khởi động session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Kiểm tra user đã đăng nhập chưa
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Kiểm tra user có phải admin không
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Lấy thông tin user hiện tại
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'email' => $_SESSION['email'],
        'role' => $_SESSION['role']
    ];
}

/**
 * Yêu cầu đăng nhập - redirect nếu chưa login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: /GamerWiki/auth/login.php');
        exit();
    }
}

/**
 * Yêu cầu quyền admin - redirect nếu không phải admin
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        $_SESSION['error'] = 'Bạn không có quyền truy cập trang này.';
        header('Location: /GamerWiki/index.php');
        exit();
    }
}

/**
 * Kiểm tra user có quyền chỉnh sửa team không
 */
function canEditTeam($teamCreatorId) {
    if (!isLoggedIn()) {
        return false;
    }
    
    // Admin có thể edit mọi team
    if (isAdmin()) {
        return true;
    }
    
    // User chỉ có thể edit team do mình tạo
    return $_SESSION['user_id'] == $teamCreatorId;
}

/**
 * Đăng xuất
 */
function logout() {
    session_destroy();
    header('Location: /GamerWiki/index.php');
    exit();
}

/**
 * Tạo CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Xác thực CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Làm sạch input để tránh XSS
 */
function cleanInput($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}
?>
