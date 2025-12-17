<?php
$page_title = 'Danh sách đội tuyển';
require_once __DIR__ . '/../includes/header.php';

// Pagination
$ban_ghi_moi_trang = 9;
$trang_hien_tai = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($trang_hien_tai - 1) * $ban_ghi_moi_trang;

// Search
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';

try {
    // Count total records
    if ($search) {
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM doi_tuyen WHERE ten_doi LIKE ? OR quoc_gia LIKE ?");
        $stmt->execute(["%$search%", "%$search%"]);
    } else {
        $stmt = $conn->query("SELECT COUNT(*) as total FROM doi_tuyen");
    }
    $tong_ban_ghi = $stmt->fetch()['total'];
    
    // Get records
    if ($search) {
        $stmt = $conn->prepare("SELECT * FROM doi_tuyen WHERE ten_doi LIKE ? OR quoc_gia LIKE ? ORDER BY ten_doi ASC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(2, "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(3, $ban_ghi_moi_trang, PDO::PARAM_INT);
        $stmt->bindValue(4, $offset, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("SELECT * FROM doi_tuyen ORDER BY ten_doi ASC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $ban_ghi_moi_trang, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
    }
    $doi_tuyen_list = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = 'Lỗi khi tải dữ liệu.';
}
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container mt-4 mb-5">
    <h1 class="mb-4"><i class="bi bi-people-fill text-primary"></i> Danh sách đội tuyển</h1>
    
    <!-- Search Box -->
    <div class="row mb-4">
        <div class="col-md-6 mx-auto">
            <form method="GET" action="" class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Tìm kiếm đội tuyển..." 
                       value="<?php echo escape_html($search); ?>">
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-search"></i> Tìm kiếm
                </button>
                <?php if ($search): ?>
                <a href="doi_tuyen.php" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Xóa
                </a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <?php if (isset($error)): ?>
        <?php echo hien_thi_thong_bao($error, 'error'); ?>
    <?php endif; ?>
    
    <!-- Teams Grid -->
    <div class="row">
        <?php if (count($doi_tuyen_list) > 0): ?>
            <?php foreach ($doi_tuyen_list as $doi): ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="bi bi-shield-fill text-primary" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="card-title text-center"><?php echo escape_html($doi['ten_doi']); ?></h5>
                        <hr>
                        <p class="mb-2">
                            <i class="bi bi-flag-fill text-danger"></i> 
                            <strong>Quốc gia:</strong> <?php echo escape_html($doi['quoc_gia']); ?>
                        </p>
                        <p class="mb-2">
                            <i class="bi bi-calendar-fill text-success"></i> 
                            <strong>Thành lập:</strong> <?php echo escape_html($doi['nam_thanh_lap']); ?>
                        </p>
                        <p class="card-text">
                            <strong>Thành tích:</strong> 
                            <?php echo escape_html(substr($doi['thanh_tich'], 0, 80)) . (strlen($doi['thanh_tich']) > 80 ? '...' : ''); ?>
                        </p>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="chi_tiet_dt.php?id=<?php echo $doi['id']; ?>" class="btn btn-primary w-100">
                            <i class="bi bi-eye"></i> Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Không tìm thấy đội tuyển nào.
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
