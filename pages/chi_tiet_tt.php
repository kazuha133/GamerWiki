<?php
$page_title = 'Chi tiết tuyển thủ';
require_once __DIR__ . '/../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: tuyen_thu.php');
    exit();
}

try {
    // Get player info with team name
    $stmt = $conn->prepare("
        SELECT tt.*, dt.ten_doi, dt.id as doi_tuyen_id 
        FROM tuyen_thu tt 
        LEFT JOIN doi_tuyen dt ON tt.id_doi_tuyen = dt.id 
        WHERE tt.id = ?
    ");
    $stmt->execute([$id]);
    $tuyen_thu = $stmt->fetch();
    
    if (!$tuyen_thu) {
        header('Location: tuyen_thu.php');
        exit();
    }
    
    // Get transfer history
    $stmt = $conn->prepare("
        SELECT lscd.*, 
               dt_cu.ten_doi as ten_doi_cu, 
               dt_moi.ten_doi as ten_doi_moi
        FROM lich_su_chuyen_doi lscd
        LEFT JOIN doi_tuyen dt_cu ON lscd.id_doi_cu = dt_cu.id
        LEFT JOIN doi_tuyen dt_moi ON lscd.id_doi_moi = dt_moi.id
        WHERE lscd.id_tuyen_thu = ?
        ORDER BY lscd.ngay_chuyen DESC
    ");
    $stmt->execute([$id]);
    $lich_su_chuyen_doi = $stmt->fetchAll();
    
    $page_title = $tuyen_thu['nickname'];
    
} catch (PDOException $e) {
    $error = 'Lỗi khi tải dữ liệu.';
}
?>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container mt-4 mb-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo url('index.php'); ?>">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?php echo url('pages/tuyen_thu.php'); ?>">Tuyển thủ</a></li>
            <li class="breadcrumb-item active"><?php echo escape_html($tuyen_thu['nickname']); ?></li>
        </ol>
    </nav>
    
    <?php if (isset($error)): ?>
        <?php echo hien_thi_thong_bao($error, 'error'); ?>
    <?php endif; ?>
    
    <!-- Player Info -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    <i class="bi bi-person-circle text-secondary" style="font-size: 200px;"></i>
                </div>
                <div class="col-md-9">
                    <h1 class="mb-3"><?php echo escape_html($tuyen_thu['nickname']); ?></h1>
                    <p class="mb-2">
                        <i class="bi bi-person-fill text-info"></i> 
                        <strong>Tên thật:</strong> <?php echo escape_html($tuyen_thu['ten_that']); ?>
                    </p>
                    <p class="mb-2">
                        <i class="bi bi-controller text-primary"></i> 
                        <strong>Vai trò:</strong> 
                        <span class="badge bg-primary"><?php echo escape_html($tuyen_thu['vai_tro']); ?></span>
                    </p>
                    <p class="mb-2">
                        <i class="bi bi-flag-fill text-danger"></i> 
                        <strong>Quốc tịch:</strong> <?php echo escape_html($tuyen_thu['quoc_tich']); ?>
                    </p>
                    <p class="mb-2">
                        <i class="bi bi-calendar-fill text-success"></i> 
                        <strong>Ngày sinh:</strong> <?php echo format_ngay($tuyen_thu['ngay_sinh']); ?>
                        <?php 
                        if ($tuyen_thu['ngay_sinh']) {
                            $tuoi = date('Y') - date('Y', strtotime($tuyen_thu['ngay_sinh']));
                            echo "($tuoi tuổi)";
                        }
                        ?>
                    </p>
                    <p class="mb-3">
                        <i class="bi bi-shield-fill text-primary"></i> 
                        <strong>Đội tuyển hiện tại:</strong> 
                        <?php if ($tuyen_thu['ten_doi']): ?>
                            <a href="<?php echo url('pages/chi_tiet_dt.php?id=' . $tuyen_thu['doi_tuyen_id']); ?>">
                                <?php echo escape_html($tuyen_thu['ten_doi']); ?>
                            </a>
                        <?php else: ?>
                            <span class="text-muted">Free Agent</span>
                        <?php endif; ?>
                    </p>
                    <?php if ($tuyen_thu['mo_ta']): ?>
                    <p class="mb-0">
                        <i class="bi bi-info-circle-fill text-info"></i> 
                        <strong>Mô tả:</strong><br>
                        <?php echo nl2br(escape_html($tuyen_thu['mo_ta'])); ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Transfer History -->
    <?php if (count($lich_su_chuyen_doi) > 0): ?>
    <div class="card shadow-sm">
        <div class="card-header bg-warning">
            <h4 class="mb-0"><i class="bi bi-arrow-left-right"></i> Lịch sử chuyển đội</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Ngày chuyển</th>
                            <th>Từ đội</th>
                            <th></th>
                            <th>Đến đội</th>
                            <th>Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lich_su_chuyen_doi as $ls): ?>
                        <tr>
                            <td><?php echo format_ngay($ls['ngay_chuyen']); ?></td>
                            <td><?php echo escape_html($ls['ten_doi_cu'] ?? 'N/A'); ?></td>
                            <td class="text-center">
                                <i class="bi bi-arrow-right text-primary"></i>
                            </td>
                            <td><?php echo escape_html($ls['ten_doi_moi'] ?? 'N/A'); ?></td>
                            <td><?php echo escape_html($ls['ghi_chu'] ?? '-'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
