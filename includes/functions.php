<?php
/**
 * Common Functions
 * Các hàm dùng chung cho GamerWiki
 */

// Bắt đầu session nếu chưa có
function khoi_tao_session() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Kiểm tra đăng nhập
function kiem_tra_dang_nhap() {
    khoi_tao_session();
    return isset($_SESSION['nguoi_dung_id']) && isset($_SESSION['ten_dang_nhap']);
}

// Kiểm tra quyền admin
function kiem_tra_admin() {
    khoi_tao_session();
    return isset($_SESSION['vai_tro']) && $_SESSION['vai_tro'] === 'admin';
}

// Redirect về trang login nếu chưa đăng nhập
function yeu_cau_dang_nhap() {
    if (!kiem_tra_dang_nhap()) {
        header('Location: ' . url('auth/login.php'));
        exit();
    }
}

// Redirect về trang chủ nếu không phải admin
function yeu_cau_admin() {
    yeu_cau_dang_nhap();
    if (!kiem_tra_admin()) {
        header('Location: ' . url('index.php'));
        exit();
    }
}

// Kiểm tra quyền customer
function kiem_tra_customer() {
    khoi_tao_session();
    return isset($_SESSION['vai_tro']) && $_SESSION['vai_tro'] === 'customer';
}

// Kiểm tra có phải admin hoặc customer
function kiem_tra_admin_hoac_customer() {
    return kiem_tra_admin() || kiem_tra_customer();
}

// Yêu cầu quyền admin hoặc customer
function yeu_cau_admin_hoac_customer() {
    yeu_cau_dang_nhap();
    if (!kiem_tra_admin_hoac_customer()) {
        header('Location: ' . url('index.php'));
        exit();
    }
}

// Kiểm tra có quyền quản lý CRUD không (admin hoặc customer)
function co_quyen_quan_ly() {
    return kiem_tra_admin() || kiem_tra_customer();
}

// Escape HTML để tránh XSS
function escape_html($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Format date
function format_ngay($date) {
    if (!$date) return '';
    $timestamp = strtotime($date);
    return date('d/m/Y', $timestamp);
}

// Format datetime
function format_ngay_gio($datetime) {
    if (!$datetime) return '';
    $timestamp = strtotime($datetime);
    return date('d/m/Y H:i', $timestamp);
}

// Upload file
function upload_file($file, $thu_muc_dich, $allowed_types = ['jpg', 'jpeg', 'png', 'gif']) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Không có file được upload hoặc có lỗi.'];
    }
    
    $file_name = $file['name'];
    $file_size = $file['size'];
    $file_tmp = $file['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    // Kiểm tra extension
    if (!in_array($file_ext, $allowed_types)) {
        return ['success' => false, 'message' => 'Chỉ chấp nhận file: ' . implode(', ', $allowed_types)];
    }
    
    // Kiểm tra kích thước (max 5MB)
    if ($file_size > 5242880) {
        return ['success' => false, 'message' => 'File không được vượt quá 5MB.'];
    }
    
    // Tạo tên file unique
    $new_file_name = uniqid() . '_' . time() . '.' . $file_ext;
    $upload_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $thu_muc_dich . '/' . $new_file_name;
    
    // Tạo thư mục nếu chưa tồn tại
    $dir = $_SERVER['DOCUMENT_ROOT'] . '/' . $thu_muc_dich;
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    
    // Upload file
    if (move_uploaded_file($file_tmp, $upload_path)) {
        return ['success' => true, 'file_name' => $new_file_name];
    } else {
        return ['success' => false, 'message' => 'Không thể upload file.'];
    }
}

// Xóa file
function xoa_file($file_path) {
    $full_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $file_path;
    if (file_exists($full_path)) {
        return unlink($full_path);
    }
    return false;
}

// Generate CSRF token
function tao_csrf_token() {
    khoi_tao_session();
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function kiem_tra_csrf_token($token) {
    khoi_tao_session();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Pagination
function tao_phan_trang($tong_ban_ghi, $ban_ghi_moi_trang, $trang_hien_tai) {
    $tong_trang = ceil($tong_ban_ghi / $ban_ghi_moi_trang);
    
    // Validate and sanitize current page
    $trang_hien_tai = max(1, min((int)$trang_hien_tai, $tong_trang));
    
    // Preserve existing GET parameters except 'page'
    $params = $_GET;
    unset($params['page']);
    
    $html = '<nav><ul class="pagination justify-content-center">';
    
    // Previous
    if ($trang_hien_tai > 1) {
        $params['page'] = $trang_hien_tai - 1;
        $query_string = http_build_query($params);
        $html .= '<li class="page-item"><a class="page-link" href="?' . escape_html($query_string) . '">« Trước</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">« Trước</span></li>';
    }
    
    // Page numbers
    for ($i = 1; $i <= $tong_trang; $i++) {
        if ($i == $trang_hien_tai) {
            $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $params['page'] = $i;
            $query_string = http_build_query($params);
            $html .= '<li class="page-item"><a class="page-link" href="?' . escape_html($query_string) . '">' . $i . '</a></li>';
        }
    }
    
    // Next
    if ($trang_hien_tai < $tong_trang) {
        $params['page'] = $trang_hien_tai + 1;
        $query_string = http_build_query($params);
        $html .= '<li class="page-item"><a class="page-link" href="?' . escape_html($query_string) . '">Sau »</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Sau »</span></li>';
    }
    
    $html .= '</ul></nav>';
    return $html;
}

// Show alert message
function hien_thi_thong_bao($message, $type = 'success') {
    $alert_types = [
        'success' => 'alert-success',
        'error' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info'
    ];
    
    $class = isset($alert_types[$type]) ? $alert_types[$type] : 'alert-info';
    
    return '<div class="alert ' . $class . ' alert-dismissible fade show" role="alert">' .
           escape_html($message) .
           '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
}

// Validate email
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}
?>
