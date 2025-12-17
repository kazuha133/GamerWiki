<?php
/**
 * Configuration file for base paths
 * Cấu hình đường dẫn gốc của website
 */

// Tự động phát hiện base path
// Lấy thư mục hiện tại và tính toán đường dẫn tương đối
$script_name = $_SERVER['SCRIPT_NAME'];
$script_dir = dirname($script_name);

// Nếu file ở root thì $script_dir = '/', nếu trong thư mục con thì = '/GamerWiki'
if ($script_dir === '/' || $script_dir === '\\') {
    define('BASE_PATH', '');
} else {
    // Loại bỏ các thư mục con của script để lấy base path
    // Ví dụ: /GamerWiki/admin/index.php -> /GamerWiki
    $path_parts = explode('/', trim($script_dir, '/'));
    define('BASE_PATH', '/' . $path_parts[0]);
}

// URL đầy đủ của website
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
define('SITE_URL', $protocol . '://' . $host . BASE_PATH);

/**
 * Helper function để tạo URL đầy đủ
 * @param string $path - Đường dẫn tương đối (vd: 'pages/doi_tuyen.php')
 * @return string - URL đầy đủ
 */
function url($path = '') {
    $path = ltrim($path, '/');
    if (empty($path)) {
        // Nếu không có path, trả về base path với trailing slash
        // Đảm bảo không trả về '//' khi BASE_PATH rỗng
        return BASE_PATH === '' ? '/' : BASE_PATH . '/';
    }
    // Kết hợp BASE_PATH với path, đảm bảo có đúng một '/' ở giữa
    return BASE_PATH === '' ? '/' . $path : BASE_PATH . '/' . $path;
}

/**
 * Helper function để tạo URL cho assets (css, js, images)
 * @param string $path - Đường dẫn asset (vd: 'assets/css/style.css')
 * @return string - URL đầy đủ
 */
function asset($path) {
    return url($path);
}
?>
