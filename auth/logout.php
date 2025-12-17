<?php
// Include config để có hàm url()
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Khởi tạo session
khoi_tao_session();

// Xóa tất cả session
session_unset();
session_destroy();

// Redirect về trang đăng nhập
header('Location: ' . url('auth/login.php'));
exit();