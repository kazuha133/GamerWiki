<?php
$page_title = 'Quản lý tài khoản';
require_once __DIR__ . '/../includes/header.php';

yeu_cau_admin();

$success = '';
$error = '';
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!kiem_tra_csrf_token($csrf_token)) {
        $error = 'Token CSRF không hợp lệ.';
    } else {
        $ten_dang_nhap = sanitize_input($_POST['ten_dang_nhap'] ?? '');
        $email = sanitize_input($_POST['email'] ?? '');
        $vai_tro = sanitize_input($_POST['vai_tro'] ?? '');
        $trang_thai = sanitize_input($_POST['trang_thai'] ?? '');
        $mat_khau = $_POST['mat_khau'] ?? '';
        
        if (empty($ten_dang_nhap) || empty($email) || empty($vai_tro)) {
            $error = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
        } elseif (!validate_email($email)) {
            $error = 'Email không hợp lệ.';
        } else {
            try {
                if ($action === 'add') {
                    if (empty($mat_khau)) {
                        $error = 'Vui lòng nhập mật khẩu.';
                    } else {
                        // Check if username exists
                        $stmt = $conn->prepare("SELECT id FROM nguoi_dung WHERE ten_dang_nhap = ?");
                        $stmt->execute([$ten_dang_nhap]);
                        if ($stmt->fetch()) {
                            $error = 'Tên đăng nhập đã tồn tại.';
                        } else {
                            $mat_khau_hash = password_hash($mat_khau, PASSWORD_DEFAULT);
                            $stmt = $conn->prepare("INSERT INTO nguoi_dung (ten_dang_nhap, mat_khau, email, vai_tro, trang_thai) VALUES (?, ?, ?, ?, ?)");
                            $stmt->execute([$ten_dang_nhap, $mat_khau_hash, $email, $vai_tro, $trang_thai]);
                            $success = 'Thêm tài khoản thành công!';
                            $action = 'list';
                        }
                    }
                } elseif ($action === 'edit' && $id > 0) {
                    if (!empty($mat_khau)) {
                        $mat_khau_hash = password_hash($mat_khau, PASSWORD_DEFAULT);
                        $stmt = $conn->prepare("UPDATE nguoi_dung SET ten_dang_nhap = ?, mat_khau = ?, email = ?, vai_tro = ?, trang_thai = ? WHERE id = ?");
                        $stmt->execute([$ten_dang_nhap, $mat_khau_hash, $email, $vai_tro, $trang_thai, $id]);
                    } else {
                        $stmt = $conn->prepare("UPDATE nguoi_dung SET ten_dang_nhap = ?, email = ?, vai_tro = ?, trang_thai = ? WHERE id = ?");
                        $stmt->execute([$ten_dang_nhap, $email, $vai_tro, $trang_thai, $id]);
                    }
                    $success = 'Cập nhật tài khoản thành công!';
                    $action = 'list';
                }
            } catch (PDOException $e) {
                $error = 'Lỗi: ' . $e->getMessage();
            }
        }
    }
}

// Handle delete
if ($action === 'delete' && $id > 0) {
    // Don't allow deleting current user
    if ($id == $_SESSION['nguoi_dung_id']) {
        $error = 'Không thể xóa tài khoản đang đăng nhập.';
        $action = 'list';
    } else {
        try {
            $stmt = $conn->prepare("DELETE FROM nguoi_dung WHERE id = ?");
            $stmt->execute([$id]);
            $success = 'Xóa tài khoản thành công!';
            $action = 'list';
        } catch (PDOException $e) {
            $error = 'Không thể xóa tài khoản.';
            $action = 'list';
        }
    }
}

// Get data for edit form
$nguoi_dung_data = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $conn->prepare("SELECT * FROM nguoi_dung WHERE id = ?");
    $stmt->execute([$id]);
    $nguoi_dung_data = $stmt->fetch();
    if (!$nguoi_dung_data) {
        $error = 'Không tìm thấy tài khoản.';
        $action = 'list';
    }
}

// Get list for display
if ($action === 'list') {
    $stmt = $conn->query("SELECT * FROM nguoi_dung ORDER BY ngay_tao DESC");
    $nguoi_dung_list = $stmt->fetchAll();
}
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container-fluid mt-4 mb-5">
    <h1 class="mb-4"><i class="bi bi-person-circle text-info"></i> Quản lý tài khoản</h1>
    
    <?php if ($success): ?>
        <?php echo hien_thi_thong_bao($success, 'success'); ?>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <?php echo hien_thi_thong_bao($error, 'error'); ?>
    <?php endif; ?>
    
    <?php if ($action === 'list'): ?>
        <!-- List View -->
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Danh sách tài khoản</h5>
                <a href="?action=add" class="btn btn-light btn-sm">
                    <i class="bi bi-plus-circle"></i> Thêm mới
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên đăng nhập</th>
                                <th>Email</th>
                                <th>Vai trò</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th width="120">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($nguoi_dung_list as $nd): ?>
                            <tr>
                                <td><?php echo $nd['id']; ?></td>
                                <td><strong><?php echo escape_html($nd['ten_dang_nhap']); ?></strong></td>
                                <td><?php echo escape_html($nd['email']); ?></td>
                                <td>
                                    <?php if ($nd['vai_tro'] === 'admin'): ?>
                                        <span class="badge bg-danger">Admin</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">User</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($nd['trang_thai'] === 'active'): ?>
                                        <span class="badge bg-success">Hoạt động</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Khóa</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo format_ngay_gio($nd['ngay_tao']); ?></td>
                                <td class="action-buttons">
                                    <a href="?action=edit&id=<?php echo $nd['id']; ?>" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php if ($nd['id'] != $_SESSION['nguoi_dung_id']): ?>
                                    <a href="?action=delete&id=<?php echo $nd['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirmDelete('<?php echo escape_html($nd['ten_dang_nhap']); ?>')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    <?php elseif ($action === 'add' || $action === 'edit'): ?>
        <!-- Add/Edit Form -->
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <?php echo $action === 'add' ? 'Thêm tài khoản mới' : 'Chỉnh sửa tài khoản'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo tao_csrf_token(); ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ten_dang_nhap" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ten_dang_nhap" name="ten_dang_nhap" 
                                   value="<?php echo $nguoi_dung_data ? escape_html($nguoi_dung_data['ten_dang_nhap']) : ''; ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo $nguoi_dung_data ? escape_html($nguoi_dung_data['email']) : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="mat_khau" class="form-label">
                                Mật khẩu 
                                <?php if ($action === 'add'): ?>
                                    <span class="text-danger">*</span>
                                <?php else: ?>
                                    <small class="text-muted">(Để trống nếu không đổi)</small>
                                <?php endif; ?>
                            </label>
                            <input type="password" class="form-control" id="mat_khau" name="mat_khau" 
                                   <?php echo $action === 'add' ? 'required' : ''; ?>>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="vai_tro" class="form-label">Vai trò <span class="text-danger">*</span></label>
                            <select class="form-select" id="vai_tro" name="vai_tro" required>
                                <option value="user" <?php echo ($nguoi_dung_data && $nguoi_dung_data['vai_tro'] === 'user') ? 'selected' : ''; ?>>User</option>
                                <option value="admin" <?php echo ($nguoi_dung_data && $nguoi_dung_data['vai_tro'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="trang_thai" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select class="form-select" id="trang_thai" name="trang_thai" required>
                                <option value="active" <?php echo ($nguoi_dung_data && $nguoi_dung_data['trang_thai'] === 'active') ? 'selected' : ''; ?>>Hoạt động</option>
                                <option value="inactive" <?php echo ($nguoi_dung_data && $nguoi_dung_data['trang_thai'] === 'inactive') ? 'selected' : ''; ?>>Khóa</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="?action=list" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-info">
                            <i class="bi bi-save"></i> <?php echo $action === 'add' ? 'Thêm mới' : 'Cập nhật'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
