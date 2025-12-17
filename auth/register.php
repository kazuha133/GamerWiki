<?php
$page_title = 'Đăng ký';
require_once __DIR__ . '/../includes/header.php';

// Nếu đã đăng nhập, redirect về trang chủ
if (kiem_tra_dang_nhap()) {
    header('Location: ' . url('index.php'));
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten_dang_nhap = sanitize_input($_POST['ten_dang_nhap'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $mat_khau = $_POST['mat_khau'] ?? '';
    $xac_nhan_mat_khau = $_POST['xac_nhan_mat_khau'] ?? '';
    
    // Validation
    if (empty($ten_dang_nhap) || empty($email) || empty($mat_khau) || empty($xac_nhan_mat_khau)) {
        $error = 'Vui lòng nhập đầy đủ thông tin.';
    } elseif (!validate_email($email)) {
        $error = 'Email không hợp lệ.';
    } elseif (strlen($mat_khau) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự.';
    } elseif ($mat_khau !== $xac_nhan_mat_khau) {
        $error = 'Mật khẩu xác nhận không khớp.';
    } else {
        try {
            // Kiểm tra tên đăng nhập đã tồn tại
            $stmt = $conn->prepare("SELECT id FROM nguoi_dung WHERE ten_dang_nhap = ?");
            $stmt->execute([$ten_dang_nhap]);
            if ($stmt->fetch()) {
                $error = 'Tên đăng nhập đã tồn tại.';
            } else {
                // Kiểm tra email đã tồn tại
                $stmt = $conn->prepare("SELECT id FROM nguoi_dung WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $error = 'Email đã được sử dụng.';
                } else {
                    // Tạo tài khoản mới
                    $mat_khau_hash = password_hash($mat_khau, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("INSERT INTO nguoi_dung (ten_dang_nhap, mat_khau, email, vai_tro, trang_thai) VALUES (?, ?, ?, 'user', 'active')");
                    $stmt->execute([$ten_dang_nhap, $mat_khau_hash, $email]);
                    
                    $success = 'Đăng ký thành công! Vui lòng đăng nhập.';
                    $ten_dang_nhap = $email = '';
                }
            }
        } catch (PDOException $e) {
            $error = 'Lỗi hệ thống. Vui lòng thử lại.';
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
                    <h2 class="text-center mb-4"><i class="bi bi-person-plus-fill"></i> Đăng ký</h2>
                    
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
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo isset($email) ? escape_html($email) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="mat_khau" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="mat_khau" name="mat_khau" required>
                            <div class="form-text">Tối thiểu 6 ký tự</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="xac_nhan_mat_khau" class="form-label">Xác nhận mật khẩu</label>
                            <input type="password" class="form-control" id="xac_nhan_mat_khau" name="xac_nhan_mat_khau" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Đăng ký</button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
