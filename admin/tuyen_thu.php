<?php
$page_title = 'Quản lý tuyển thủ';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/upload_handler.php';

yeu_cau_admin_hoac_customer();

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
        $ten_that = sanitize_input($_POST['ten_that'] ?? '');
        $nickname = sanitize_input($_POST['nickname'] ?? '');
        $vai_tro = sanitize_input($_POST['vai_tro'] ?? '');
        $quoc_tich = sanitize_input($_POST['quoc_tich'] ?? '');
        $ngay_sinh = sanitize_input($_POST['ngay_sinh'] ?? '');
        $id_doi_tuyen = isset($_POST['id_doi_tuyen']) && $_POST['id_doi_tuyen'] !== '' ? (int)$_POST['id_doi_tuyen'] : null;
        $mo_ta = sanitize_input($_POST['mo_ta'] ?? '');
        
        if (empty($ten_that) || empty($nickname) || empty($vai_tro)) {
            $error = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
        } else {
            // Handle avatar upload
            $avatar_filename = null;
            $old_avatar = null;
            
            // Get old avatar for edit action
            if ($action === 'edit' && $id > 0) {
                $stmt = $conn->prepare("SELECT anh_dai_dien FROM tuyen_thu WHERE id = ?");
                $stmt->execute([$id]);
                $old_data = $stmt->fetch();
                $old_avatar = ($old_data && isset($old_data['anh_dai_dien'])) ? $old_data['anh_dai_dien'] : null;
            }
            
            // Check if new file was uploaded
            if (isset($_FILES['anh_dai_dien']) && $_FILES['anh_dai_dien']['error'] !== UPLOAD_ERR_NO_FILE) {
                $upload_result = upload_image($_FILES['anh_dai_dien'], 'avatar', $old_avatar);
                
                if ($upload_result['success']) {
                    $avatar_filename = $upload_result['filename'];
                } else {
                    $error = $upload_result['error'];
                }
            } else {
                // Keep old avatar if no new file uploaded
                $avatar_filename = $old_avatar;
            }
            
            // Only proceed if no upload error
            if (empty($error)) {
                try {
                    if ($action === 'add') {
                        $stmt = $conn->prepare("INSERT INTO tuyen_thu (ten_that, nickname, anh_dai_dien, vai_tro, quoc_tich, ngay_sinh, id_doi_tuyen, mo_ta) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$ten_that, $nickname, $avatar_filename, $vai_tro, $quoc_tich, $ngay_sinh, $id_doi_tuyen, $mo_ta]);
                        $success = 'Thêm tuyển thủ thành công!';
                        $action = 'list';
                    } elseif ($action === 'edit' && $id > 0) {
                        // Get old team ID before update
                        $stmt = $conn->prepare("SELECT id_doi_tuyen FROM tuyen_thu WHERE id = ?");
                        $stmt->execute([$id]);
                        $old_player_data = $stmt->fetch();
                        $id_doi_cu = $old_player_data ? $old_player_data['id_doi_tuyen'] : null;
                        
                        // Update player
                        $stmt = $conn->prepare("UPDATE tuyen_thu SET ten_that = ?, nickname = ?, anh_dai_dien = ?, vai_tro = ?, quoc_tich = ?, ngay_sinh = ?, id_doi_tuyen = ?, mo_ta = ? WHERE id = ?");
                        $stmt->execute([$ten_that, $nickname, $avatar_filename, $vai_tro, $quoc_tich, $ngay_sinh, $id_doi_tuyen, $mo_ta, $id]);
                        
                        // Check if team changed and record transfer history
                        if ($id_doi_cu !== $id_doi_tuyen) {
                            try {
                                $ghi_chu = sanitize_input($_POST['ghi_chu_chuyen_doi'] ?? '');
                                
                                $stmt_history = $conn->prepare("
                                    INSERT INTO lich_su_chuyen_doi 
                                    (id_tuyen_thu, id_doi_cu, id_doi_moi, ngay_chuyen, ghi_chu) 
                                    VALUES (?, ?, ?, CURDATE(), ?)
                                ");
                                $stmt_history->execute([$id, $id_doi_cu, $id_doi_tuyen, $ghi_chu]);
                                
                                $success = 'Cập nhật tuyển thủ và ghi lịch sử chuyển đội thành công!';
                            } catch (PDOException $e_history) {
                                // Log error but don't fail the entire operation
                                $success = 'Cập nhật tuyển thủ thành công! (Lưu ý: Lịch sử chuyển đội không được ghi lại)';
                                error_log('Lỗi ghi lịch sử chuyển đội: ' . $e_history->getMessage());
                            }
                        } else {
                            $success = 'Cập nhật tuyển thủ thành công!';
                        }
                        
                        $action = 'list';
                    }
                } catch (PDOException $e) {
                    $error = 'Lỗi: ' . $e->getMessage();
                    // Delete uploaded file if database operation failed
                    if ($avatar_filename && $avatar_filename !== $old_avatar) {
                        delete_image($avatar_filename, 'avatar');
                    }
                }
            }
        }
    }
}

// Handle delete
if ($action === 'delete' && $id > 0) {
    try {
        // Get avatar filename before deleting
        $stmt = $conn->prepare("SELECT anh_dai_dien FROM tuyen_thu WHERE id = ?");
        $stmt->execute([$id]);
        $player_data = $stmt->fetch();
        
        // Delete from database
        $stmt = $conn->prepare("DELETE FROM tuyen_thu WHERE id = ?");
        $stmt->execute([$id]);
        
        // Delete avatar file if exists
        if ($player_data && $player_data['anh_dai_dien']) {
            delete_image($player_data['anh_dai_dien'], 'avatar');
        }
        
        $success = 'Xóa tuyển thủ thành công!';
        $action = 'list';
    } catch (PDOException $e) {
        $error = 'Không thể xóa tuyển thủ.';
        $action = 'list';
    }
}

// Get data for edit form
$tuyen_thu_data = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $conn->prepare("SELECT * FROM tuyen_thu WHERE id = ?");
    $stmt->execute([$id]);
    $tuyen_thu_data = $stmt->fetch();
    if (!$tuyen_thu_data) {
        $error = 'Không tìm thấy tuyển thủ.';
        $action = 'list';
    }
}

// Get teams for dropdown
$stmt = $conn->query("SELECT id, ten_doi FROM doi_tuyen ORDER BY ten_doi ASC");
$doi_tuyen_list_all = $stmt->fetchAll();

// Get list for display
if ($action === 'list') {
    $stmt = $conn->query("SELECT tt.*, dt.ten_doi FROM tuyen_thu tt LEFT JOIN doi_tuyen dt ON tt.id_doi_tuyen = dt.id ORDER BY tt.nickname ASC");
    $tuyen_thu_list = $stmt->fetchAll();
}
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container-fluid mt-4 mb-5">
    <h1 class="mb-4"><i class="bi bi-person-badge-fill text-success"></i> Quản lý tuyển thủ</h1>
    
    <?php if ($success): ?>
        <?php echo hien_thi_thong_bao($success, 'success'); ?>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <?php echo hien_thi_thong_bao($error, 'error'); ?>
    <?php endif; ?>
    
    <?php if ($action === 'list'): ?>
        <!-- List View -->
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Danh sách tuyển thủ</h5>
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
                                <th>Ảnh</th>
                                <th>Nickname</th>
                                <th>Tên thật</th>
                                <th>Vai trò</th>
                                <th>Quốc tịch</th>
                                <th>Đội tuyển</th>
                                <th>Ngày sinh</th>
                                <th width="150">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tuyen_thu_list as $tt): ?>
                            <tr>
                                <td><?php echo $tt['id']; ?></td>
                                <td>
                                    <?php if ($tt['anh_dai_dien'] && image_exists($tt['anh_dai_dien'], 'avatar')): ?>
                                        <img src="<?php echo get_image_url($tt['anh_dai_dien'], 'avatar'); ?>" 
                                             alt="Avatar" class="rounded-circle img-thumbnail" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <i class="bi bi-person-circle text-muted" style="font-size: 2rem;"></i>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?php echo escape_html($tt['nickname']); ?></strong></td>
                                <td><?php echo escape_html($tt['ten_that']); ?></td>
                                <td><span class="badge bg-primary"><?php echo escape_html($tt['vai_tro']); ?></span></td>
                                <td><?php echo escape_html($tt['quoc_tich']); ?></td>
                                <td><?php echo escape_html($tt['ten_doi'] ?? '-'); ?></td>
                                <td><?php echo format_ngay($tt['ngay_sinh']); ?></td>
                                <td class="action-buttons">
                                    <a href="?action=edit&id=<?php echo $tt['id']; ?>" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="?action=delete&id=<?php echo $tt['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirmDelete('<?php echo escape_html($tt['nickname']); ?>')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                    <a href="<?php echo url('pages/chi_tiet_tt.php?id=' . $tt['id']); ?>" 
                                       class="btn btn-sm btn-info" target="_blank">
                                        <i class="bi bi-eye"></i>
                                    </a>
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
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <?php echo $action === 'add' ? 'Thêm tuyển thủ mới' : 'Chỉnh sửa tuyển thủ'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo tao_csrf_token(); ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ten_that" class="form-label">Tên thật <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ten_that" name="ten_that" 
                                   value="<?php echo $tuyen_thu_data ? escape_html($tuyen_thu_data['ten_that']) : ''; ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="nickname" class="form-label">Nickname <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nickname" name="nickname" 
                                   value="<?php echo $tuyen_thu_data ? escape_html($tuyen_thu_data['nickname']) : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="anh_dai_dien" class="form-label">Ảnh đại diện</label>
                        <?php if ($tuyen_thu_data && $tuyen_thu_data['anh_dai_dien'] && image_exists($tuyen_thu_data['anh_dai_dien'], 'avatar')): ?>
                            <div class="mb-2">
                                <img src="<?php echo get_image_url($tuyen_thu_data['anh_dai_dien'], 'avatar'); ?>" 
                                     alt="Ảnh hiện tại" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                <p class="text-muted small mb-0">Ảnh hiện tại</p>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="anh_dai_dien" name="anh_dai_dien" accept="image/jpeg,image/jpg,image/png,image/gif">
                        <small class="form-text text-muted">Chấp nhận: JPG, JPEG, PNG, GIF. Tối đa 5MB.</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="vai_tro" class="form-label">Vai trò <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="vai_tro" name="vai_tro" 
                                   value="<?php echo $tuyen_thu_data ? escape_html($tuyen_thu_data['vai_tro']) : ''; ?>" 
                                   placeholder="VD: Top, Mid, ADC..." required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="quoc_tich" class="form-label">Quốc tịch</label>
                            <input type="text" class="form-control" id="quoc_tich" name="quoc_tich" 
                                   value="<?php echo $tuyen_thu_data ? escape_html($tuyen_thu_data['quoc_tich']) : ''; ?>">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="ngay_sinh" class="form-label">Ngày sinh</label>
                            <input type="date" class="form-control" id="ngay_sinh" name="ngay_sinh" 
                                   value="<?php echo $tuyen_thu_data ? $tuyen_thu_data['ngay_sinh'] : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="id_doi_tuyen" class="form-label">Đội tuyển</label>
                        <select class="form-select" id="id_doi_tuyen" name="id_doi_tuyen">
                            <option value="">-- Free Agent --</option>
                            <?php foreach ($doi_tuyen_list_all as $doi): ?>
                            <option value="<?php echo $doi['id']; ?>" 
                                    <?php echo ($tuyen_thu_data && $tuyen_thu_data['id_doi_tuyen'] == $doi['id']) ? 'selected' : ''; ?>>
                                <?php echo escape_html($doi['ten_doi']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($action === 'edit'): ?>
                        <small class="form-text text-muted">Nếu thay đổi đội, lịch sử chuyển đội sẽ tự động được ghi lại.</small>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($action === 'edit'): ?>
                    <div class="mb-3">
                        <label for="ghi_chu_chuyen_doi" class="form-label">Ghi chú chuyển đội (tùy chọn)</label>
                        <textarea class="form-control" id="ghi_chu_chuyen_doi" name="ghi_chu_chuyen_doi" rows="2" 
                                  placeholder="Ví dụ: Chuyển với mức phí chuyển nhượng cao, Tìm kiếm thử thách mới..."></textarea>
                        <small class="form-text text-muted">Chỉ dùng khi bạn thay đổi đội tuyển của tuyển thủ.</small>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="mo_ta" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="mo_ta" name="mo_ta" rows="4"><?php echo $tuyen_thu_data ? escape_html($tuyen_thu_data['mo_ta']) : ''; ?></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="?action=list" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> <?php echo $action === 'add' ? 'Thêm mới' : 'Cập nhật'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
