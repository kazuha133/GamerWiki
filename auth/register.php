<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Redirect nếu đã đăng nhập
if (isLoggedIn()) {
    header('Location: /GamerWiki/index.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = cleanInput($_POST['username'] ?? '');
    $email = cleanInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = 'Vui lòng nhập đầy đủ thông tin.';
    } elseif (!validateEmail($email)) {
        $error = 'Email không hợp lệ.';
    } elseif (!validatePassword($password)) {
        $error = 'Mật khẩu phải có ít nhất 8 ký tự.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Mật khẩu xác nhận không khớp.';
    } else {
        // Kiểm tra username đã tồn tại
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->fetch()) {
            $error = 'Tên đăng nhập đã tồn tại.';
        } else {
            // Kiểm tra email đã tồn tại
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $error = 'Email đã được sử dụng.';
            } else {
                // Tạo tài khoản mới
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'user')");
                
                if ($stmt->execute([$username, $hashedPassword, $email])) {
                    $success = 'Đăng ký thành công! Vui lòng đăng nhập.';
                    // Clear form
                    $_POST = [];
                } else {
                    $error = 'Đã xảy ra lỗi. Vui lòng thử lại.';
                }
            }
        }
    }
}

$pageTitle = 'Đăng ký';
include '../includes/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">
                        <i class="fas fa-user-plus me-2"></i>Đăng ký tài khoản
                    </h2>
                    
                    <?php if ($error): ?>
                        <?php echo showError($error); ?>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <?php echo showSuccess($success); ?>
                        <div class="text-center mt-3">
                            <a href="login.php" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập ngay
                            </a>
                        </div>
                    <?php else: ?>
                    
                    <form method="POST" action="" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-user me-2"></i>Tên đăng nhập
                            </label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo isset($_POST['username']) ? e($_POST['username']) : ''; ?>" 
                                   required autofocus>
                            <div class="invalid-feedback">
                                Vui lòng nhập tên đăng nhập.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>Email
                            </label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo isset($_POST['email']) ? e($_POST['email']) : ''; ?>" 
                                   required>
                            <div class="invalid-feedback">
                                Vui lòng nhập email hợp lệ.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Mật khẩu
                            </label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   minlength="8" required>
                            <div class="form-text">Mật khẩu phải có ít nhất 8 ký tự.</div>
                            <div class="invalid-feedback">
                                Mật khẩu phải có ít nhất 8 ký tự.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Xác nhận mật khẩu
                            </label>
                            <input type="password" class="form-control" id="confirm_password" 
                                   name="confirm_password" minlength="8" required>
                            <div class="invalid-feedback">
                                Vui lòng xác nhận mật khẩu.
                            </div>
                        </div>
                        
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Đăng ký
                            </button>
                        </div>
                        
                        <div class="text-center">
                            <p class="mb-0">
                                Đã có tài khoản? 
                                <a href="login.php" class="text-primary-custom text-decoration-none">
                                    Đăng nhập
                                </a>
                            </p>
                        </div>
                    </form>
                    
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
