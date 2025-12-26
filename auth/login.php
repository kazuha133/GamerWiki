<?php
$page_title = 'Đăng nhập';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/google_config.php';

// Nếu đã đăng nhập, redirect về trang chủ
if (kiem_tra_dang_nhap()) {
    header('Location: ' . url('index.php'));
    exit();
}

// Khởi tạo Google Client và lấy URL đăng nhập
$google_login_url = '#';
try {
    if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
        $google_client = get_google_client();
        $google_login_url = $google_client->createAuthUrl();
    }
} catch (Exception $e) {
    // Nếu có lỗi, giữ URL mặc định
}

$error = '';
$success = '';

// Xử lý thông báo lỗi từ Google OAuth callback
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'no_code':
            $error = 'Không nhận được mã xác thực từ Google.';
            break;
        case 'token_error':
            $error = 'Không thể lấy access token từ Google.';
            break;
        case 'invalid_email':
            $error = 'Email từ Google không hợp lệ.';
            break;
        case 'account_inactive':
            $error = 'Tài khoản của bạn đã bị khóa.';
            break;
        case 'oauth_failed':
            $error = 'Đăng nhập Google thất bại. Vui lòng thử lại.';
            break;
        case 'composer_not_installed':
            $error = 'Hệ thống chưa được cấu hình đầy đủ. Vui lòng liên hệ quản trị viên.';
            break;
        case 'username_generation_failed':
            $error = 'Không thể tạo tên đăng nhập. Vui lòng thử lại sau.';
            break;
        default:
            $error = 'Có lỗi xảy ra. Vui lòng thử lại.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten_dang_nhap = sanitize_input($_POST['ten_dang_nhap'] ?? '');
    $mat_khau = $_POST['mat_khau'] ?? '';
    
    $debug_mode = false; // Đổi thành false sau khi fix xong
    
    if (empty($ten_dang_nhap) || empty($mat_khau)) {
        $error = 'Vui lòng nhập đầy đủ thông tin.';
    } else {
        try {
            $stmt = $conn->prepare("SELECT id, ten_dang_nhap, mat_khau, vai_tro, trang_thai FROM nguoi_dung WHERE ten_dang_nhap = ?");
            $stmt->execute([$ten_dang_nhap]);
            $nguoi_dung = $stmt->fetch();
            
            if ($nguoi_dung && password_verify($mat_khau, $nguoi_dung['mat_khau'])) {
                if ($nguoi_dung['trang_thai'] === 'inactive') {
                    $error = 'Tài khoản của bạn đã bị khóa.';
                } else {
                    $_SESSION['nguoi_dung_id'] = $nguoi_dung['id'];
                    $_SESSION['ten_dang_nhap'] = $nguoi_dung['ten_dang_nhap'];
                    $_SESSION['vai_tro'] = $nguoi_dung['vai_tro'];
                    
                    header('Location: ' . url('index.php'));
                    exit();
                }
            } else {
                $error = 'Tên đăng nhập hoặc mật khẩu không đúng.';
            }
        } catch (PDOException $e) {
            $error = 'Lỗi hệ thống. Vui lòng thử lại.';
            if ($debug_mode) {
                echo "<p style='color: red;'>Database Error: " . $e->getMessage() . "</p>";
            }
        }
    }
}
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4"><i class="bi bi-box-arrow-in-right"></i> Đăng nhập</h2>
                    
                    <?php if ($error): ?>
                        <?php echo hien_thi_thong_bao($error, 'error'); ?>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <?php echo hien_thi_thong_bao($success, 'success'); ?>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="ten_dang_nhap" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="ten_dang_nhap" name="ten_dang_nhap" 
                                   value="<?php echo isset($ten_dang_nhap) ? escape_html($ten_dang_nhap) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="mat_khau" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="mat_khau" name="mat_khau" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
                    </form>
                    
                    <div class="text-center my-3">
                        <div class="d-flex align-items-center">
                            <hr class="flex-grow-1">
                            <span class="mx-3 text-muted">hoặc</span>
                            <hr class="flex-grow-1">
                        </div>
                    </div>
                    
                    <a href="<?php echo escape_html($google_login_url); ?>" class="btn btn-outline-danger w-100">
                        <i class="bi bi-google"></i> Đăng nhập bằng Google
                    </a>
                    
                    <div class="text-center mt-3">
                        <p>Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
