<?php
/**
 * Helper Functions
 * Các hàm tiện ích dùng chung
 */

/**
 * Hiển thị thông báo thành công
 */
function showSuccess($message) {
    return '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>' . htmlspecialchars($message) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
}

/**
 * Hiển thị thông báo lỗi
 */
function showError($message) {
    return '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>' . htmlspecialchars($message) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
}

/**
 * Hiển thị thông báo cảnh báo
 */
function showWarning($message) {
    return '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>' . htmlspecialchars($message) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
}

/**
 * Format ngày tháng
 */
function formatDate($date) {
    if (!$date) return 'N/A';
    return date('d/m/Y', strtotime($date));
}

/**
 * Format tiền tệ
 */
function formatMoney($amount) {
    if (!$amount) return '$0';
    return '$' . number_format($amount, 0, ',', ',');
}

/**
 * Tạo slug từ string
 */
function createSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    return trim($string, '-');
}

/**
 * Truncate text
 */
function truncateText($text, $length = 150) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

/**
 * Lấy icon game
 */
function getGameIcon($game) {
    $icons = [
        'League of Legends' => 'fa-trophy',
        'Dota 2' => 'fa-shield-alt',
        'Valorant' => 'fa-crosshairs',
        'CS:GO' => 'fa-bullseye',
        'Overwatch' => 'fa-fire'
    ];
    
    return $icons[$game] ?? 'fa-gamepad';
}

/**
 * Pagination helper
 */
function getPagination($currentPage, $totalPages, $baseUrl) {
    $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
    
    // Previous button
    if ($currentPage > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '&page=' . ($currentPage - 1) . '">Trước</a></li>';
    }
    
    // Page numbers
    for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++) {
        $active = ($i == $currentPage) ? 'active' : '';
        $html .= '<li class="page-item ' . $active . '"><a class="page-link" href="' . $baseUrl . '&page=' . $i . '">' . $i . '</a></li>';
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '&page=' . ($currentPage + 1) . '">Sau</a></li>';
    }
    
    $html .= '</ul></nav>';
    return $html;
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate password strength
 */
function validatePassword($password) {
    return strlen($password) >= 8;
}

/**
 * Sanitize output
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect với message
 */
function redirectWith($url, $type, $message) {
    $_SESSION[$type] = $message;
    header('Location: ' . $url);
    exit();
}
?>
