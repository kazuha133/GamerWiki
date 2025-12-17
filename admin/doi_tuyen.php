<?php
$page_title = 'Quản lý đội tuyển';
require_once __DIR__ . '/../includes/header.php';

// Require admin or customer privileges
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
        $ten_doi = sanitize_input($_POST['ten_doi'] ?? '');
        $quoc_gia = sanitize_input($_POST['quoc_gia'] ?? '');
        $nam_thanh_lap = sanitize_input($_POST['nam_thanh_lap'] ?? '');
        $thanh_tich = sanitize_input($_POST['thanh_tich'] ?? '');
        $mo_ta = sanitize_input($_POST['mo_ta'] ?? '');
        
        if (empty($ten_doi) || empty($quoc_gia)) {
            $error = 'Vui lòng nhập đầy đủ thông tin bắt buộc.';
        } else {
            try {
                if ($action === 'add') {
                    $stmt = $conn->prepare("INSERT INTO doi_tuyen (ten_doi, quoc_gia, nam_thanh_lap, thanh_tich, mo_ta) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$ten_doi, $quoc_gia, $nam_thanh_lap, $thanh_tich, $mo_ta]);
                    $success = 'Thêm đội tuyển thành công!';
                    $action = 'list';
                } elseif ($action === 'edit' && $id > 0) {
                    $stmt = $conn->prepare("UPDATE doi_tuyen SET ten_doi = ?, quoc_gia = ?, nam_thanh_lap = ?, thanh_tich = ?, mo_ta = ? WHERE id = ?");
                    $stmt->execute([$ten_doi, $quoc_gia, $nam_thanh_lap, $thanh_tich, $mo_ta, $id]);
                    $success = 'Cập nhật đội tuyển thành công!';
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
        $stmt = $conn->prepare("DELETE FROM doi_tuyen WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Xóa đội tuyển thành công!';
        $action = 'list';
    } catch (PDOException $e) {
        $error = 'Không thể xóa đội tuyển. Có thể đang có dữ liệu liên quan.';
        $action = 'list';
    }
}

// Get data for edit form
$doi_tuyen_data = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $conn->prepare("SELECT * FROM doi_tuyen WHERE id = ?");
    $stmt->execute([$id]);
    $doi_tuyen_data = $stmt->fetch();
    if (!$doi_tuyen_data) {
        $error = 'Không tìm thấy đội tuyển.';
        $action = 'list';
    }
}

// Get list for display
if ($action === 'list') {
    $stmt = $conn->query("SELECT * FROM doi_tuyen ORDER BY ten_doi ASC");
    $doi_tuyen_list = $stmt->fetchAll();
}
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container-fluid mt-4 mb-5">
    <h1 class="mb-4"><i class="bi bi-people-fill text-primary"></i> Quản lý đội tuyển</h1>
    
    <?php if ($success): ?>
        <?php echo hien_thi_thong_bao($success, 'success'); ?>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <?php echo hien_thi_thong_bao($error, 'error'); ?>
    <?php endif; ?>
    
    <?php if ($action === 'list'): ?>
        <!-- List View -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Danh sách đội tuyển</h5>
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
                                <th>Tên đội</th>
                                <th>Quốc gia</th>
                                <th>Năm TL</th>
                                <th>Thành tích</th>
                                <th>Cập nhật</th>
                                <th width="150">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($doi_tuyen_list as $doi): ?>
                            <tr>
                                <td><?php echo $doi['id']; ?></td>
                                <td><strong><?php echo escape_html($doi['ten_doi']); ?></strong></td>
                                <td><?php echo escape_html($doi['quoc_gia']); ?></td>
                                <td><?php echo escape_html($doi['nam_thanh_lap']); ?></td>
                                <td><?php echo escape_html(substr($doi['thanh_tich'], 0, 50)) . '...'; ?></td>
                                <td><?php echo format_ngay_gio($doi['ngay_cap_nhat']); ?></td>
                                <td class="action-buttons">
                                    <a href="?action=edit&id=<?php echo $doi['id']; ?>" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="?action=delete&id=<?php echo $doi['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirmDelete('<?php echo escape_html($doi['ten_doi']); ?>')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                    <a href="<?php echo url('pages/chi_tiet_dt.php?id=' . $doi['id']); ?>" 
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
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <?php echo $action === 'add' ? 'Thêm đội tuyển mới' : 'Chỉnh sửa đội tuyển'; ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo tao_csrf_token(); ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ten_doi" class="form-label">Tên đội <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ten_doi" name="ten_doi" 
                                   value="<?php echo $doi_tuyen_data ? escape_html($doi_tuyen_data['ten_doi']) : ''; ?>" required>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="quoc_gia" class="form-label">Quốc gia <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="quoc_gia" name="quoc_gia" 
                                   value="<?php echo $doi_tuyen_data ? escape_html($doi_tuyen_data['quoc_gia']) : ''; ?>" required>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="nam_thanh_lap" class="form-label">Năm thành lập</label>
                            <input type="number" class="form-control" id="nam_thanh_lap" name="nam_thanh_lap" 
                                   min="1900" max="<?php echo date('Y'); ?>"
                                   value="<?php echo $doi_tuyen_data ? escape_html($doi_tuyen_data['nam_thanh_lap']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="thanh_tich" class="form-label">Thành tích</label>
                        <textarea class="form-control" id="thanh_tich" name="thanh_tich" rows="3"><?php echo $doi_tuyen_data ? escape_html($doi_tuyen_data['thanh_tich']) : ''; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="mo_ta" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="mo_ta" name="mo_ta" rows="4"><?php echo $doi_tuyen_data ? escape_html($doi_tuyen_data['mo_ta']) : ''; ?></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="?action=list" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> <?php echo $action === 'add' ? 'Thêm mới' : 'Cập nhật'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
