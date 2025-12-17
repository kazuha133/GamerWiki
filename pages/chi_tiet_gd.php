<?php
$page_title = 'Chi tiết giải đấu';
require_once __DIR__ . '/../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: giai_dau.php');
    exit();
}

try {
    // Get tournament info
    $stmt = $conn->prepare("SELECT * FROM giai_dau WHERE id = ?");
    $stmt->execute([$id]);
    $giai_dau = $stmt->fetch();
    
    if (!$giai_dau) {
        header('Location: giai_dau.php');
        exit();
    }
    
    // Get participating teams with rankings
    $stmt = $conn->prepare("
        SELECT dtg.*, dt.ten_doi, dt.quoc_gia 
        FROM doi_tham_gia dtg 
        JOIN doi_tuyen dt ON dtg.id_doi_tuyen = dt.id 
        WHERE dtg.id_giai_dau = ? 
        ORDER BY dtg.thu_hang ASC
    ");
    $stmt->execute([$id]);
    $doi_tham_gia = $stmt->fetchAll();
    
    $page_title = $giai_dau['ten_giai'];
    
} catch (PDOException $e) {
    $error = 'Lỗi khi tải dữ liệu.';
}
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container mt-4 mb-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo url('index.php'); ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?php echo url('pages/giai_dau.php'); ?>">Giải đấu</a></li>
            <li class="breadcrumb-item active"><?php echo escape_html($giai_dau['ten_giai']); ?></li>
        </ol>
    </nav>
    
    <?php if (isset($error)): ?>
        <?php echo hien_thi_thong_bao($error, 'error'); ?>
    <?php endif; ?>
    
    <!-- Tournament Info -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    <i class="bi bi-trophy-fill text-warning" style="font-size: 150px;"></i>
                </div>
                <div class="col-md-9">
                    <h1 class="mb-3"><?php echo escape_html($giai_dau['ten_giai']); ?></h1>
                    <p class="mb-2">
                        <i class="bi bi-joystick text-info"></i> 
                        <strong>Game:</strong> 
                        <span class="badge bg-info"><?php echo escape_html($giai_dau['game']); ?></span>
                    </p>
                    <p class="mb-2">
                        <i class="bi bi-calendar-fill text-success"></i> 
                        <strong>Thời gian bắt đầu:</strong> <?php echo format_ngay($giai_dau['thoi_gian_bat_dau']); ?>
                    </p>
                    <p class="mb-2">
                        <i class="bi bi-calendar-check-fill text-danger"></i> 
                        <strong>Thời gian kết thúc:</strong> <?php echo format_ngay($giai_dau['thoi_gian_ket_thuc']); ?>
                    </p>
                    <p class="mb-2">
                        <i class="bi bi-geo-alt-fill text-primary"></i> 
                        <strong>Địa điểm:</strong> <?php echo escape_html($giai_dau['dia_diem']); ?>
                    </p>
                    <p class="mb-3">
                        <i class="bi bi-currency-dollar text-warning"></i> 
                        <strong>Giải thưởng:</strong> <?php echo escape_html($giai_dau['giai_thuong']); ?>
                    </p>
                    <?php if ($giai_dau['mo_ta']): ?>
                    <p class="mb-0">
                        <i class="bi bi-info-circle-fill text-info"></i> 
                        <strong>Mô tả:</strong><br>
                        <?php echo nl2br(escape_html($giai_dau['mo_ta'])); ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Participating Teams & Results -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="bi bi-people-fill"></i> Đội tham gia & Kết quả</h4>
        </div>
        <div class="card-body">
            <?php if (count($doi_tham_gia) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="80">Hạng</th>
                            <th>Đội tuyển</th>
                            <th>Quốc gia</th>
                            <th>Ngày tham gia</th>
                            <th width="120">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($doi_tham_gia as $doi): ?>
                        <tr>
                            <td>
                                <?php 
                                $rank_class = '';
                                if ($doi['thu_hang'] == 1) $rank_class = 'tournament-badge first';
                                elseif ($doi['thu_hang'] == 2) $rank_class = 'tournament-badge second';
                                elseif ($doi['thu_hang'] == 3) $rank_class = 'tournament-badge third';
                                else $rank_class = 'badge bg-secondary';
                                ?>
                                <span class="<?php echo $rank_class; ?>">
                                    <?php if ($doi['thu_hang']): ?>
                                        #<?php echo $doi['thu_hang']; ?>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td>
                                <strong><?php echo escape_html($doi['ten_doi']); ?></strong>
                            </td>
                            <td>
                                <i class="bi bi-flag-fill text-danger"></i> 
                                <?php echo escape_html($doi['quoc_gia']); ?>
                            </td>
                            <td><?php echo format_ngay_gio($doi['ngay_tham_gia']); ?></td>
                            <td>
                                <a href="<?php echo url('pages/chi_tiet_dt.php?id=' . $doi['id_doi_tuyen']); ?>" 
                                   class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> Chi tiết
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="text-muted mb-0">Chưa có đội tuyển nào tham gia.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
