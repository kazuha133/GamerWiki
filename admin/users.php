<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin();

$error = '';
$success = '';

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request.';
    } else {
        $userId = $_POST['user_id'] ?? 0;
        
        if (isset($_POST['change_role'])) {
            $newRole = $_POST['role'] ?? '';
            
            if (in_array($newRole, ['admin', 'user'])) {
                // Không cho phép tự hạ cấp mình
                if ($userId == $_SESSION['user_id']) {
                    $error = 'Bạn không thể thay đổi vai trò của chính mình.';
                } else {
                    try {
                        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
                        $stmt->execute([$newRole, $userId]);
                        $success = 'Đã thay đổi vai trò user.';
                    } catch (Exception $e) {
                        $error = 'Đã xảy ra lỗi.';
                    }
                }
            }
        } elseif (isset($_POST['delete_user'])) {
            // Không cho phép tự xóa mình
            if ($userId == $_SESSION['user_id']) {
                $error = 'Bạn không thể xóa tài khoản của chính mình.';
            } else {
                try {
                    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                    $stmt->execute([$userId]);
                    $success = 'Đã xóa user.';
                } catch (Exception $e) {
                    $error = 'Không thể xóa user. Có thể user có dữ liệu liên quan.';
                }
            }
        }
    }
}

// Lấy danh sách users
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();

$pageTitle = 'Quản lý Users';
include '../includes/header.php';
?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/GamerWiki/index.php">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="index.php">Admin</a></li>
            <li class="breadcrumb-item active">Users</li>
        </ol>
    </nav>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-users me-2"></i>Quản lý Users</h1>
    </div>
    
    <?php if ($error): ?>
        <?php echo showError($error); ?>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <?php echo showSuccess($success); ?>
    <?php endif; ?>
    
    <!-- Users Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Vai trò</th>
                            <th>Ngày tạo</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td>
                                <strong><?php echo e($user['username']); ?></strong>
                                <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                <span class="badge bg-info ms-2">You</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($user['email']); ?></td>
                            <td>
                                <?php if ($user['role'] === 'admin'): ?>
                                <span class="badge bg-warning">Admin</span>
                                <?php else: ?>
                                <span class="badge bg-secondary">User</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo formatDate($user['created_at']); ?></td>
                            <td>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <div class="btn-group" role="group">
                                    <!-- Change Role -->
                                    <form method="POST" action="" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <input type="hidden" name="role" value="<?php echo $user['role'] === 'admin' ? 'user' : 'admin'; ?>">
                                        <button type="submit" name="change_role" class="btn btn-sm btn-warning" 
                                                title="Thay đổi vai trò">
                                            <i class="fas fa-exchange-alt"></i>
                                        </button>
                                    </form>
                                    
                                    <!-- Delete -->
                                    <form method="POST" action="" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="delete_user" class="btn btn-sm btn-danger" 
                                                data-confirm-delete="Bạn có chắc muốn xóa user này?"
                                                title="Xóa user">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Info Box -->
    <div class="alert alert-info mt-4">
        <h5><i class="fas fa-info-circle me-2"></i>Thông tin</h5>
        <ul class="mb-0">
            <li>Admins có quyền quản lý toàn bộ hệ thống</li>
            <li>Regular users chỉ có thể tạo và quản lý đội tuyển của riêng mình</li>
            <li>Bạn không thể thay đổi vai trò hoặc xóa tài khoản của chính mình</li>
        </ul>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
