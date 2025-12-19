<?php
$page_title = 'Chi tiết đội tuyển';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/upload_handler.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: doi_tuyen.php');
    exit();
}

try {
    // Get team info
    $stmt = $conn->prepare("SELECT * FROM doi_tuyen WHERE id = ?");
    $stmt->execute([$id]);
    $doi_tuyen = $stmt->fetch();
    
    if (!$doi_tuyen) {
        header('Location: doi_tuyen.php');
        exit();
    }
    
    // Get players
    $stmt = $conn->prepare("SELECT * FROM tuyen_thu WHERE id_doi_tuyen = ? ORDER BY vai_tro ASC");
    $stmt->execute([$id]);
    $danh_sach_tuyen_thu = $stmt->fetchAll();
    
    // Get tournament results
    $stmt = $conn->prepare("
        SELECT dtg.*, gd.ten_giai, gd.game, gd.thoi_gian_bat_dau, gd.giai_thuong 
        FROM doi_tham_gia dtg 
        JOIN giai_dau gd ON dtg.id_giai_dau = gd.id 
        WHERE dtg.id_doi_tuyen = ? 
        ORDER BY gd.thoi_gian_bat_dau DESC
    ");
    $stmt->execute([$id]);
    $ket_qua_giai_dau = $stmt->fetchAll();
    
    $page_title = $doi_tuyen['ten_doi'];
    
} catch (PDOException $e) {
    $error = 'Lỗi khi tải dữ liệu.';
}
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container mt-4 mb-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo url('index.php'); ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?php echo url('pages/doi_tuyen.php'); ?>">Đội tuyển</a></li>
            <li class="breadcrumb-item active"><?php echo escape_html($doi_tuyen['ten_doi']); ?></li>
        </ol>
    </nav>
    
    <?php if (isset($error)): ?>
        <?php echo hien_thi_thong_bao($error, 'error'); ?>
    <?php endif; ?>
    
    <!-- Team Info -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    <?php if ($doi_tuyen['logo'] && image_exists($doi_tuyen['logo'], 'logo')): ?>
                        <img src="<?php echo get_image_url($doi_tuyen['logo'], 'logo'); ?>" 
                             alt="<?php echo escape_html($doi_tuyen['ten_doi']); ?>" 
                             class="img-fluid" style="max-width: 200px;">
                    <?php else: ?>
                        <i class="bi bi-shield-fill text-primary" style="font-size: 150px;"></i>
                    <?php endif; ?>
                </div>
                <div class="col-md-9">
                    <h1 class="mb-3"><?php echo escape_html($doi_tuyen['ten_doi']); ?></h1>
                    <p class="mb-2">
                        <i class="bi bi-flag-fill text-danger"></i> 
                        <strong>Quốc gia:</strong> <?php echo escape_html($doi_tuyen['quoc_gia']); ?>
                    </p>
                    <p class="mb-2">
                        <i class="bi bi-calendar-fill text-success"></i> 
                        <strong>Năm thành lập:</strong> <?php echo escape_html($doi_tuyen['nam_thanh_lap']); ?>
                    </p>
                    <p class="mb-3">
                        <i class="bi bi-trophy-fill text-warning"></i> 
                        <strong>Thành tích:</strong><br>
                        <?php echo nl2br(escape_html($doi_tuyen['thanh_tich'])); ?>
                    </p>
                    <?php if ($doi_tuyen['mo_ta']): ?>
                    <p class="mb-0">
                        <i class="bi bi-info-circle-fill text-info"></i> 
                        <strong>Mô tả:</strong><br>
                        <?php echo nl2br(escape_html($doi_tuyen['mo_ta'])); ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Players List -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="bi bi-people-fill"></i> Danh sách tuyển thủ</h4>
        </div>
        <div class="card-body">
            <?php if (count($danh_sach_tuyen_thu) > 0): ?>
            <div class="row">
                <?php foreach ($danh_sach_tuyen_thu as $tuyen_thu): ?>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <?php if ($tuyen_thu['anh_dai_dien'] && image_exists($tuyen_thu['anh_dai_dien'], 'avatar')): ?>
                                <img src="<?php echo get_image_url($tuyen_thu['anh_dai_dien'], 'avatar'); ?>" 
                                     alt="<?php echo escape_html($tuyen_thu['nickname']); ?>" 
                                     class="rounded-circle img-thumbnail mb-2" 
                                     style="width: 100px; height: 100px; object-fit: cover;">
                            <?php else: ?>
                                <i class="bi bi-person-circle text-secondary" style="font-size: 3rem;"></i>
                            <?php endif; ?>
                            <h5 class="mt-2 mb-1"><?php echo escape_html($tuyen_thu['nickname']); ?></h5>
                            <p class="text-muted mb-1"><?php echo escape_html($tuyen_thu['ten_that']); ?></p>
                            <p class="mb-2">
                                <span class="badge bg-primary"><?php echo escape_html($tuyen_thu['vai_tro']); ?></span>
                            </p>
                            <a href="<?php echo url('pages/chi_tiet_tt.php?id=' . $tuyen_thu['id']); ?>" class="btn btn-sm btn-outline-primary">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-muted mb-0">Chưa có tuyển thủ nào.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Tournament Results -->
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="bi bi-trophy-fill"></i> Kết quả giải đấu</h4>
        </div>
        <div class="card-body">
            <?php if (count($ket_qua_giai_dau) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Giải đấu</th>
                            <th>Game</th>
                            <th>Thời gian</th>
                            <th>Giải thưởng</th>
                            <th>Hạng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ket_qua_giai_dau as $ket_qua): ?>
                        <tr>
                            <td>
                                <a href="<?php echo url('pages/chi_tiet_gd.php?id=' . $ket_qua['id_giai_dau']); ?>">
                                    <?php echo escape_html($ket_qua['ten_giai']); ?>
                                </a>
                            </td>
                            <td><?php echo escape_html($ket_qua['game']); ?></td>
                            <td><?php echo format_ngay($ket_qua['thoi_gian_bat_dau']); ?></td>
                            <td><?php echo escape_html($ket_qua['giai_thuong']); ?></td>
                            <td>
                                <?php 
                                $rank_class = '';
                                if ($ket_qua['thu_hang'] == 1) $rank_class = 'tournament-badge first';
                                elseif ($ket_qua['thu_hang'] == 2) $rank_class = 'tournament-badge second';
                                elseif ($ket_qua['thu_hang'] == 3) $rank_class = 'tournament-badge third';
                                else $rank_class = 'badge bg-secondary';
                                ?>
                                <span class="<?php echo $rank_class; ?>">
                                    #<?php echo $ket_qua['thu_hang']; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="text-muted mb-0">Chưa có kết quả giải đấu nào.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
