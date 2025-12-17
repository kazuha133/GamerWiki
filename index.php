<?php
$page_title = 'Trang chủ';
require_once __DIR__ . '/includes/header.php';

// Lấy thống kê
try {
    $stmt = $conn->query("SELECT COUNT(*) as total FROM doi_tuyen");
    $tong_doi_tuyen = $stmt->fetch()['total'];
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM tuyen_thu");
    $tong_tuyen_thu = $stmt->fetch()['total'];
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM giai_dau");
    $tong_giai_dau = $stmt->fetch()['total'];
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM nguoi_dung");
    $tong_nguoi_dung = $stmt->fetch()['total'];
    
    // Lấy đội tuyển nổi bật (top 5)
    $stmt = $conn->query("SELECT * FROM doi_tuyen ORDER BY ngay_cap_nhat DESC LIMIT 5");
    $doi_tuyen_noi_bat = $stmt->fetchAll();
    
    // Lấy giải đấu sắp diễn ra
    $stmt = $conn->query("SELECT * FROM giai_dau WHERE thoi_gian_bat_dau >= CURDATE() ORDER BY thoi_gian_bat_dau ASC LIMIT 3");
    $giai_dau_sap_dien_ra = $stmt->fetchAll();
    
    // Lấy tuyển thủ nổi bật
    $stmt = $conn->query("SELECT tt.*, dt.ten_doi FROM tuyen_thu tt LEFT JOIN doi_tuyen dt ON tt.id_doi_tuyen = dt.id ORDER BY tt.ngay_cap_nhat DESC LIMIT 6");
    $tuyen_thu_noi_bat = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = 'Lỗi khi tải dữ liệu.';
}
?>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<!-- Hero Section -->
<div class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-4 fw-bold"><i class="bi bi-trophy-fill"></i> Chào mừng đến với GamerWiki</h1>
                <p class="lead">Hệ thống quản lý đội tuyển Esport chuyên nghiệp - Nơi cập nhật thông tin đầy đủ về các đội tuyển, tuyển thủ và giải đấu hàng đầu thế giới.</p>
                <a href="<?php echo url('pages/doi_tuyen.php'); ?>" class="btn btn-light btn-lg mt-3">Khám phá ngay</a>
            </div>
            <div class="col-md-4 text-center">
                <i class="bi bi-controller" style="font-size: 150px;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Section -->
<div class="container mt-5">
    <div class="row text-center">
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <i class="bi bi-people-fill text-primary" style="font-size: 3rem;"></i>
                    <h3 class="mt-3"><?php echo $tong_doi_tuyen; ?></h3>
                    <p class="text-muted">Đội tuyển</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <i class="bi bi-person-fill text-success" style="font-size: 3rem;"></i>
                    <h3 class="mt-3"><?php echo $tong_tuyen_thu; ?></h3>
                    <p class="text-muted">Tuyển thủ</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <i class="bi bi-trophy-fill text-warning" style="font-size: 3rem;"></i>
                    <h3 class="mt-3"><?php echo $tong_giai_dau; ?></h3>
                    <p class="text-muted">Giải đấu</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <i class="bi bi-person-circle text-info" style="font-size: 3rem;"></i>
                    <h3 class="mt-3"><?php echo $tong_nguoi_dung; ?></h3>
                    <p class="text-muted">Người dùng</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Featured Teams -->
<div class="container mt-5">
    <h2 class="mb-4"><i class="bi bi-star-fill text-warning"></i> Đội tuyển nổi bật</h2>
    <div class="row">
        <?php foreach ($doi_tuyen_noi_bat as $doi): ?>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title"><?php echo escape_html($doi['ten_doi']); ?></h5>
                    <p class="text-muted mb-2">
                        <i class="bi bi-flag-fill"></i> <?php echo escape_html($doi['quoc_gia']); ?> | 
                        <i class="bi bi-calendar"></i> <?php echo escape_html($doi['nam_thanh_lap']); ?>
                    </p>
                    <p class="card-text"><?php echo escape_html(substr($doi['thanh_tich'], 0, 100)) . '...'; ?></p>
                    <a href="<?php echo url('pages/chi_tiet_dt.php?id=' . $doi['id']); ?>" class="btn btn-primary btn-sm">Xem chi tiết</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Upcoming Tournaments -->
<?php if (count($giai_dau_sap_dien_ra) > 0): ?>
<div class="container mt-5">
    <h2 class="mb-4"><i class="bi bi-calendar-event-fill text-danger"></i> Giải đấu sắp diễn ra</h2>
    <div class="row">
        <?php foreach ($giai_dau_sap_dien_ra as $giai): ?>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title"><?php echo escape_html($giai['ten_giai']); ?></h5>
                    <p class="text-muted mb-2">
                        <i class="bi bi-joystick"></i> <?php echo escape_html($giai['game']); ?>
                    </p>
                    <p class="mb-1"><i class="bi bi-calendar"></i> <?php echo format_ngay($giai['thoi_gian_bat_dau']); ?></p>
                    <p class="mb-1"><i class="bi bi-geo-alt-fill"></i> <?php echo escape_html($giai['dia_diem']); ?></p>
                    <p class="mb-3"><i class="bi bi-currency-dollar"></i> <?php echo escape_html($giai['giai_thuong']); ?></p>
                    <a href="<?php echo url('pages/chi_tiet_gd.php?id=' . $giai['id']); ?>" class="btn btn-primary btn-sm">Xem chi tiết</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Featured Players -->
<div class="container mt-5 mb-5">
    <h2 class="mb-4"><i class="bi bi-person-badge-fill text-success"></i> Tuyển thủ nổi bật</h2>
    <div class="row">
        <?php foreach ($tuyen_thu_noi_bat as $tuyen_thu): ?>
        <div class="col-md-2 mb-4">
            <div class="card shadow-sm h-100 text-center">
                <div class="card-body">
                    <i class="bi bi-person-circle" style="font-size: 3rem;"></i>
                    <h6 class="card-title mt-2"><?php echo escape_html($tuyen_thu['nickname']); ?></h6>
                    <p class="text-muted small mb-1"><?php echo escape_html($tuyen_thu['vai_tro']); ?></p>
                    <p class="text-muted small"><?php echo escape_html($tuyen_thu['ten_doi'] ?? 'Free Agent'); ?></p>
                    <a href="<?php echo url('pages/chi_tiet_tt.php?id=' . $tuyen_thu['id']); ?>" class="btn btn-primary btn-sm">Chi tiết</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
