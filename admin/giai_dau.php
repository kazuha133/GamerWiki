<?php
$page_title = 'Quản lý giải đấu';
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
        $ten_giai = sanitize_input($_POST['ten_giai'] ?? '');
        $game = sanitize_input($_POST['game'] ?? '');
        $thoi_gian_bat_dau = sanitize_input($_POST['thoi_gian_bat_dau'] ?? '');
        $thoi_gian_ket_thuc = sanitize_input($_POST['thoi_gian_ket_thuc'] ?? '');
        $dia_diem = sanitize_input($_POST['dia_diem'] ?? '');
        $giai_thuong = sanitize_input($_POST['giai_thuong'] ?? '');
        $mo_ta = sanitize_input($_POST['mo_ta'] ?? '');
        
        if (empty($ten_giai) || empty($game)) {
            $error = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
        } else {
            try {
                if ($action === 'add') {
                    $stmt = $conn->prepare("INSERT INTO giai_dau (ten_giai, game, thoi_gian_bat_dau, thoi_gian_ket_thuc, dia_diem, giai_thuong, mo_ta) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$ten_giai, $game, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $dia_diem, $giai_thuong, $mo_ta]);
                    $success = 'Thêm giải đấu thành công!';
                    $action = 'list';
                } elseif ($action === 'edit' && $id > 0) {
                    $stmt = $conn->prepare("UPDATE giai_dau SET ten_giai = ?, game = ?, thoi_gian_bat_dau = ?, thoi_gian_ket_thuc = ?, dia_diem = ?, giai_thuong = ?, mo_ta = ? WHERE id = ?");
                    $stmt->execute([$ten_giai, $game, $thoi_gian_bat_dau, $thoi_gian_ket_thuc, $dia_diem, $giai_thuong, $mo_ta, $id]);
                    $success = 'Cập nhật giải đấu thành công!';
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
    try {
        $stmt = $conn->prepare("DELETE FROM giai_dau WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Xóa giải đấu thành công!';
        $action = 'list';
    } catch (PDOException $e) {
        $error = 'Không thể xóa giải đấu.';
        $action = 'list';
    }
}

// Get data for edit form
$giai_dau_data = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $conn->prepare("SELECT * FROM giai_dau WHERE id = ?");
    $stmt->execute([$id]);
    $giai_dau_data = $stmt->fetch();
    if (!$giai_dau_data) {
        $error = 'Không tìm thấy giải đấu.';
        $action = 'list';
    }
}

// Get list for display
if ($action === 'list') {
    $stmt = $conn->query("SELECT * FROM giai_dau ORDER BY thoi_gian_bat_dau DESC");
    $giai_dau_list = $stmt->fetchAll();
}
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container-fluid mt-4 mb-5">
    <h1 class="mb-4"><i class="bi bi-trophy-fill text-warning"></i> Quản lý giải đấu</h1>
    
    <?php if ($success): ?>
        <?php echo hien_thi_thong_bao($success, 'success'); ?>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <?php echo hien_thi_thong_bao($error, 'error'); ?>
    <?php endif; ?>
    
    <?php if ($action === 'list'): ?>
        <!-- List View -->
        <div class="card shadow-sm">
            <div class="card-header bg-warning d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Danh sách giải đấu</h5>
                <a href="?action=add" class="btn btn-dark btn-sm">
                    <i class="bi bi-plus-circle"></i> Thêm mới
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên giải</th>
                                <th>Game</th>
                                <th>Bắt đầu</th>
                                <th>Kết thúc</th>
                                <th>Địa điểm</th>
                                <th>Giải thưởng</th>
                                <th width="150">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($giai_dau_list as $gd): ?>
                            <tr>
                                <td><?php echo $gd['id']; ?></td>
                                <td><strong><?php echo escape_html($gd['ten_giai']); ?></strong></td>
                                <td><span class="badge bg-info"><?php echo escape_html($gd['game']); ?></span></td>
                                <td><?php echo format_ngay($gd['thoi_gian_bat_dau']); ?></td>
                                <td><?php echo format_ngay($gd['thoi_gian_ket_thuc']); ?></td>
                                <td><?php echo escape_html($gd['dia_diem']); ?></td>
                                <td><?php echo escape_html($gd['giai_thuong']); ?></td>
                                <td class="action-buttons">
                                    <a href="?action=edit&id=<?php echo $gd['id']; ?>" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="?action=delete&id=<?php echo $gd['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirmDelete('<?php echo escape_html($gd['ten_giai']); ?>')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                    <a href="<?php echo url('pages/chi_tiet_gd.php?id=' . $gd['id']); ?>" 
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
            <div class="card-header bg-warning">
                <h5 class="mb-0">
                    <?php echo $action === 'add' ? 'Thêm giải đấu mới' : 'Chỉnh sửa giải đấu'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo tao_csrf_token(); ?>">
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="ten_giai" class="form-label">Tên giải <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ten_giai" name="ten_giai" 
                                   value="<?php echo $giai_dau_data ? escape_html($giai_dau_data['ten_giai']) : ''; ?>" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="game" class="form-label">Game <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="game" name="game" 
                                   value="<?php echo $giai_dau_data ? escape_html($giai_dau_data['game']) : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="thoi_gian_bat_dau" class="form-label">Thời gian bắt đầu</label>
                            <input type="date" class="form-control" id="thoi_gian_bat_dau" name="thoi_gian_bat_dau" 
                                   value="<?php echo $giai_dau_data ? $giai_dau_data['thoi_gian_bat_dau'] : ''; ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="thoi_gian_ket_thuc" class="form-label">Thời gian kết thúc</label>
                            <input type="date" class="form-control" id="thoi_gian_ket_thuc" name="thoi_gian_ket_thuc" 
                                   value="<?php echo $giai_dau_data ? $giai_dau_data['thoi_gian_ket_thuc'] : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="dia_diem" class="form-label">Địa điểm</label>
                            <input type="text" class="form-control" id="dia_diem" name="dia_diem" 
                                   value="<?php echo $giai_dau_data ? escape_html($giai_dau_data['dia_diem']) : ''; ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="giai_thuong" class="form-label">Giải thưởng</label>
                            <input type="text" class="form-control" id="giai_thuong" name="giai_thuong" 
                                   value="<?php echo $giai_dau_data ? escape_html($giai_dau_data['giai_thuong']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="mo_ta" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="mo_ta" name="mo_ta" rows="4"><?php echo $giai_dau_data ? escape_html($giai_dau_data['mo_ta']) : ''; ?></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="?action=list" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save"></i> <?php echo $action === 'add' ? 'Thêm mới' : 'Cập nhật'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
