<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$userId = $_SESSION['user_id'];
$error = '';
$success = '';

// Lấy thông tin user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Lấy teams của user
$stmtTeams = $pdo->prepare("SELECT * FROM teams WHERE created_by = ? ORDER BY created_at DESC");
$stmtTeams->execute([$userId]);
$userTeams = $stmtTeams->fetchAll();

// Xử lý update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            $error = 'Invalid request.';
        } else {
            $email = cleanInput($_POST['email'] ?? '');
            
            if (!validateEmail($email)) {
                $error = 'Email không hợp lệ.';
            } else {
                try {
                    $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
                    $stmt->execute([$email, $userId]);
                    $success = 'Cập nhật thông tin thành công!';
                    $user['email'] = $email;
                } catch (Exception $e) {
                    $error = 'Đã xảy ra lỗi. Vui lòng thử lại.';
                }
            }
        }
    } elseif (isset($_POST['change_password'])) {
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            $error = 'Invalid request.';
        } else {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            if (!password_verify($currentPassword, $user['password'])) {
                $error = 'Mật khẩu hiện tại không đúng.';
            } elseif (!validatePassword($newPassword)) {
                $error = 'Mật khẩu mới phải có ít nhất 8 ký tự.';
            } elseif ($newPassword !== $confirmPassword) {
                $error = 'Mật khẩu xác nhận không khớp.';
            } else {
                try {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->execute([$hashedPassword, $userId]);
                    $success = 'Đổi mật khẩu thành công!';
                } catch (Exception $e) {
                    $error = 'Đã xảy ra lỗi. Vui lòng thử lại.';
                }
            }
        }
    }
}

$pageTitle = 'Hồ sơ cá nhân';
include 'includes/header.php';
?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/GamerWiki/index.php">Trang chủ</a></li>
            <li class="breadcrumb-item active">Hồ sơ</li>
        </ol>
    </nav>
    
    <h1 class="mb-4"><i class="fas fa-user-circle me-2"></i>Hồ sơ cá nhân</h1>
    
    <?php if ($error): ?>
        <?php echo showError($error); ?>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <?php echo showSuccess($success); ?>
    <?php endif; ?>
    
    <div class="row">
        <!-- User Info -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Thông tin tài khoản</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" value="<?php echo e($user['username']); ?>" disabled>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Vai trò</label>
                            <input type="text" class="form-control" value="<?php echo e(ucfirst($user['role'])); ?>" disabled>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo e($user['email']); ?>" required>
                        </div>
                        
                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Cập nhật thông tin
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Change Password -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Đổi mật khẩu</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                            <input type="password" class="form-control" id="current_password" 
                                   name="current_password" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Mật khẩu mới</label>
                            <input type="password" class="form-control" id="new_password" 
                                   name="new_password" minlength="8" required>
                            <div class="form-text">Tối thiểu 8 ký tự.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" class="form-control" id="confirm_password" 
                                   name="confirm_password" minlength="8" required>
                        </div>
                        
                        <button type="submit" name="change_password" class="btn btn-warning">
                            <i class="fas fa-key me-2"></i>Đổi mật khẩu
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- User Teams -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Đội tuyển của bạn</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($userTeams)): ?>
                    <p class="text-muted">Bạn chưa tạo đội tuyển nào.</p>
                    <a href="teams/create.php" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-2"></i>Tạo đội tuyển
                    </a>
                    <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($userTeams as $team): ?>
                        <a href="teams/view.php?id=<?php echo $team['id']; ?>" 
                           class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0"><?php echo e($team['name']); ?></h6>
                                    <small class="text-muted"><?php echo e($team['game']); ?></small>
                                </div>
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <a href="teams/create.php" class="btn btn-sm btn-primary mt-3">
                        <i class="fas fa-plus me-2"></i>Tạo đội mới
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
