<?php
$page_title = 'Dashboard Admin';
require_once __DIR__ . '/../includes/header.php';

// Require admin privileges
yeu_cau_admin();

try {
    // Get statistics
    $stmt = $conn->query("SELECT COUNT(*) as total FROM doi_tuyen");
    $tong_doi_tuyen = $stmt->fetch()['total'];
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM tuyen_thu");
    $tong_tuyen_thu = $stmt->fetch()['total'];
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM giai_dau");
    $tong_giai_dau = $stmt->fetch()['total'];
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM nguoi_dung");
    $tong_nguoi_dung = $stmt->fetch()['total'];
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM nguoi_dung WHERE vai_tro = 'admin'");
    $tong_admin = $stmt->fetch()['total'];
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM nguoi_dung WHERE trang_thai = 'active'");
    $tong_nguoi_dung_hoat_dong = $stmt->fetch()['total'];
    
    // Recent activities
    $stmt = $conn->query("SELECT * FROM doi_tuyen ORDER BY ngay_cap_nhat DESC LIMIT 5");
    $doi_tuyen_moi_nhat = $stmt->fetchAll();
    
    $stmt = $conn->query("SELECT * FROM tuyen_thu ORDER BY ngay_cap_nhat DESC LIMIT 5");
    $tuyen_thu_moi_nhat = $stmt->fetchAll();
    
    $stmt = $conn->query("SELECT * FROM giai_dau ORDER BY ngay_tao DESC LIMIT 5");
    $giai_dau_moi_nhat = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = 'Lỗi khi tải dữ liệu.';
}
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container-fluid mt-4 mb-5">
    <h1 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard Admin</h1>
    
    <?php if (isset($error)): ?>
        <?php echo hien_thi_thong_bao($error, 'error'); ?>
    <?php endif; ?>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Đội tuyển</h6>
                            <h2 class="mb-0"><?php echo $tong_doi_tuyen; ?></h2>
                        </div>
                        <div>
                            <i class="bi bi-people-fill text-primary" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    <a href="doi_tuyen.php" class="btn btn-sm btn-primary mt-2">Quản lý</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card success shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Tuyển thủ</h6>
                            <h2 class="mb-0"><?php echo $tong_tuyen_thu; ?></h2>
                        </div>
                        <div>
                            <i class="bi bi-person-fill text-success" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    <a href="tuyen_thu.php" class="btn btn-sm btn-success mt-2">Quản lý</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card warning shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Giải đấu</h6>
                            <h2 class="mb-0"><?php echo $tong_giai_dau; ?></h2>
                        </div>
                        <div>
                            <i class="bi bi-trophy-fill text-warning" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    <a href="giai_dau.php" class="btn btn-sm btn-warning mt-2">Quản lý</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card dashboard-card danger shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Người dùng</h6>
                            <h2 class="mb-0"><?php echo $tong_nguoi_dung; ?></h2>
                            <small class="text-muted"><?php echo $tong_admin; ?> Admin, <?php echo $tong_nguoi_dung_hoat_dong; ?> Hoạt động</small>
                        </div>
                        <div>
                            <i class="bi bi-person-circle text-info" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    <a href="tai_khoan.php" class="btn btn-sm btn-info mt-2">Quản lý</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Activities -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-people-fill"></i> Đội tuyển mới nhất</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach ($doi_tuyen_moi_nhat as $doi): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?php echo escape_html($doi['ten_doi']); ?></strong>
                                    <br>
                                    <small class="text-muted"><?php echo escape_html($doi['quoc_gia']); ?></small>
                                </div>
                                <a href="/pages/chi_tiet_dt.php?id=<?php echo $doi['id']; ?>" 
                                   class="btn btn-sm btn-outline-primary">Xem</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-person-fill"></i> Tuyển thủ mới nhất</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach ($tuyen_thu_moi_nhat as $tt): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?php echo escape_html($tt['nickname']); ?></strong>
                                    <br>
                                    <small class="text-muted"><?php echo escape_html($tt['vai_tro']); ?></small>
                                </div>
                                <a href="/pages/chi_tiet_tt.php?id=<?php echo $tt['id']; ?>" 
                                   class="btn btn-sm btn-outline-success">Xem</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="bi bi-trophy-fill"></i> Giải đấu mới nhất</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach ($giai_dau_moi_nhat as $gd): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?php echo escape_html($gd['ten_giai']); ?></strong>
                                    <br>
                                    <small class="text-muted"><?php echo escape_html($gd['game']); ?></small>
                                </div>
                                <a href="/pages/chi_tiet_gd.php?id=<?php echo $gd['id']; ?>" 
                                   class="btn btn-sm btn-outline-warning">Xem</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="bi bi-lightning-fill"></i> Thao tác nhanh</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <a href="doi_tuyen.php?action=add" class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle"></i> Thêm đội tuyển
                    </a>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="tuyen_thu.php?action=add" class="btn btn-success w-100">
                        <i class="bi bi-plus-circle"></i> Thêm tuyển thủ
                    </a>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="giai_dau.php?action=add" class="btn btn-warning w-100">
                        <i class="bi bi-plus-circle"></i> Thêm giải đấu
                    </a>
                </div>
                <div class="col-md-3 mb-2">
                    <a href="tai_khoan.php?action=add" class="btn btn-info w-100">
                        <i class="bi bi-plus-circle"></i> Thêm tài khoản
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
