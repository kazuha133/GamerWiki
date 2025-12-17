<?php
$page_title = 'Danh sách giải đấu';
require_once __DIR__ . '/../includes/header.php';

// Pagination
$ban_ghi_moi_trang = 10;
$trang_hien_tai = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($trang_hien_tai - 1) * $ban_ghi_moi_trang;

// Search
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';

try {
    // Count total records
    if ($search) {
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM giai_dau WHERE ten_giai LIKE ? OR game LIKE ?");
        $stmt->execute(["%$search%", "%$search%"]);
    } else {
        $stmt = $conn->query("SELECT COUNT(*) as total FROM giai_dau");
    }
    $tong_ban_ghi = $stmt->fetch()['total'];
    
    // Get records
    if ($search) {
        $stmt = $conn->prepare("SELECT * FROM giai_dau WHERE ten_giai LIKE ? OR game LIKE ? ORDER BY thoi_gian_bat_dau DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(2, "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(3, $ban_ghi_moi_trang, PDO::PARAM_INT);
        $stmt->bindValue(4, $offset, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("SELECT * FROM giai_dau ORDER BY thoi_gian_bat_dau DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $ban_ghi_moi_trang, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
    }
    $giai_dau_list = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = 'Lỗi khi tải dữ liệu.';
}
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container mt-4 mb-5">
    <h1 class="mb-4"><i class="bi bi-trophy-fill text-warning"></i> Danh sách giải đấu</h1>
    
    <!-- Search Box -->
    <div class="row mb-4">
        <div class="col-md-6 mx-auto">
            <form method="GET" action="" class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Tìm kiếm giải đấu..." 
                       value="<?php echo escape_html($search); ?>">
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-search"></i> Tìm kiếm
                </button>
                <?php if ($search): ?>
                <a href="giai_dau.php" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Xóa
                </a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <?php if (isset($error)): ?>
        <?php echo hien_thi_thong_bao($error, 'error'); ?>
    <?php endif; ?>
    
    <!-- Tournaments List -->
    <div class="row">
        <?php if (count($giai_dau_list) > 0): ?>
            <?php foreach ($giai_dau_list as $giai): ?>
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0"><?php echo escape_html($giai['ten_giai']); ?></h5>
                            <span class="badge bg-info"><?php echo escape_html($giai['game']); ?></span>
                        </div>
                        <hr>
                        <p class="mb-2">
                            <i class="bi bi-calendar-fill text-success"></i> 
                            <strong>Bắt đầu:</strong> <?php echo format_ngay($giai['thoi_gian_bat_dau']); ?>
                        </p>
                        <p class="mb-2">
                            <i class="bi bi-calendar-check-fill text-danger"></i> 
                            <strong>Kết thúc:</strong> <?php echo format_ngay($giai['thoi_gian_ket_thuc']); ?>
                        </p>
                        <p class="mb-2">
                            <i class="bi bi-geo-alt-fill text-primary"></i> 
                            <strong>Địa điểm:</strong> <?php echo escape_html($giai['dia_diem']); ?>
                        </p>
                        <p class="mb-3">
                            <i class="bi bi-currency-dollar text-warning"></i> 
                            <strong>Giải thưởng:</strong> <?php echo escape_html($giai['giai_thuong']); ?>
                        </p>
                        <?php if ($giai['mo_ta']): ?>
                        <p class="card-text text-muted">
                            <?php echo escape_html(substr($giai['mo_ta'], 0, 100)) . (strlen($giai['mo_ta']) > 100 ? '...' : ''); ?>
                        </p>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="chi_tiet_gd.php?id=<?php echo $giai['id']; ?>" class="btn btn-primary w-100">
                            <i class="bi bi-eye"></i> Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Không tìm thấy giải đấu nào.
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
