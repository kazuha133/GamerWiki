<?php
$page_title = 'Danh sách tuyển thủ';
require_once __DIR__ . '/../includes/header.php';

// Pagination
$ban_ghi_moi_trang = 12;
$trang_hien_tai = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($trang_hien_tai - 1) * $ban_ghi_moi_trang;

// Search
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';

try {
    // Count total records
    if ($search) {
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM tuyen_thu WHERE nickname LIKE ? OR ten_that LIKE ? OR vai_tro LIKE ?");
        $stmt->execute(["%$search%", "%$search%", "%$search%"]);
    } else {
        $stmt = $conn->query("SELECT COUNT(*) as total FROM tuyen_thu");
    }
    $tong_ban_ghi = $stmt->fetch()['total'];
    
    // Get records with team names
    if ($search) {
        $stmt = $conn->prepare("
            SELECT tt.*, dt.ten_doi 
            FROM tuyen_thu tt 
            LEFT JOIN doi_tuyen dt ON tt.id_doi_tuyen = dt.id 
            WHERE tt.nickname LIKE ? OR tt.ten_that LIKE ? OR tt.vai_tro LIKE ?
            ORDER BY tt.nickname ASC 
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(2, "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(3, "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(4, $ban_ghi_moi_trang, PDO::PARAM_INT);
        $stmt->bindValue(5, $offset, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("
            SELECT tt.*, dt.ten_doi 
            FROM tuyen_thu tt 
            LEFT JOIN doi_tuyen dt ON tt.id_doi_tuyen = dt.id 
            ORDER BY tt.nickname ASC 
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, $ban_ghi_moi_trang, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
    }
    $tuyen_thu_list = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = 'Lỗi khi tải dữ liệu.';
}
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container mt-4 mb-5">
    <h1 class="mb-4"><i class="bi bi-person-badge-fill text-success"></i> Danh sách tuyển thủ</h1>
    
    <!-- Search Box -->
    <div class="row mb-4">
        <div class="col-md-6 mx-auto">
            <form method="GET" action="" class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Tìm kiếm tuyển thủ..." 
                       value="<?php echo escape_html($search); ?>">
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-search"></i> Tìm kiếm
                </button>
                <?php if ($search): ?>
                <a href="tuyen_thu.php" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Xóa
                </a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <?php if (isset($error)): ?>
        <?php echo hien_thi_thong_bao($error, 'error'); ?>
    <?php endif; ?>
    
    <!-- Players Grid -->
    <div class="row">
        <?php if (count($tuyen_thu_list) > 0): ?>
            <?php foreach ($tuyen_thu_list as $tuyen_thu): ?>
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm h-100 text-center">
                    <div class="card-body">
                        <i class="bi bi-person-circle text-secondary" style="font-size: 5rem;"></i>
                        <h5 class="card-title mt-2"><?php echo escape_html($tuyen_thu['nickname']); ?></h5>
                        <p class="text-muted small mb-1"><?php echo escape_html($tuyen_thu['ten_that']); ?></p>
                        <p class="mb-2">
                            <span class="badge bg-primary"><?php echo escape_html($tuyen_thu['vai_tro']); ?></span>
                        </p>
                        <p class="mb-2">
                            <i class="bi bi-flag-fill text-danger"></i> 
                            <?php echo escape_html($tuyen_thu['quoc_tich']); ?>
                        </p>
                        <p class="mb-3">
                            <i class="bi bi-shield-fill text-primary"></i> 
                            <?php echo escape_html($tuyen_thu['ten_doi'] ?? 'Free Agent'); ?>
                        </p>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="chi_tiet_tt.php?id=<?php echo $tuyen_thu['id']; ?>" class="btn btn-primary w-100">
                            <i class="bi bi-eye"></i> Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Không tìm thấy tuyển thủ nào.
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Pagination -->
    <?php if ($tong_ban_ghi > $ban_ghi_moi_trang): ?>
        <?php echo tao_phan_trang($tong_ban_ghi, $ban_ghi_moi_trang, $trang_hien_tai); ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
