<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

$tournamentId = $_GET['id'] ?? 0;

// Lấy thông tin giải đấu
$stmt = $pdo->prepare("SELECT * FROM tournaments WHERE id = ?");
$stmt->execute([$tournamentId]);
$tournament = $stmt->fetch();

if (!$tournament) {
    redirectWith('index.php', 'error', 'Không tìm thấy giải đấu.');
}

// Lấy danh sách đội tham gia và kết quả
$stmtTeams = $pdo->prepare("
    SELECT t.*, tt.placement, tt.prize_money
    FROM team_tournaments tt
    JOIN teams t ON tt.team_id = t.id
    WHERE tt.tournament_id = ?
    ORDER BY tt.placement ASC
");
$stmtTeams->execute([$tournamentId]);
$teams = $stmtTeams->fetchAll();

$pageTitle = $tournament['name'];
include '../includes/header.php';
?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/GamerWiki/index.php">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="index.php">Giải đấu</a></li>
            <li class="breadcrumb-item active"><?php echo e($tournament['name']); ?></li>
        </ol>
    </nav>
    
    <!-- Tournament Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h1 class="mb-3">
                        <i class="fas fa-trophy me-2 text-warning"></i>
                        <?php echo e($tournament['name']); ?>
                    </h1>
                </div>
                <?php if (isAdmin()): ?>
                <a href="edit.php?id=<?php echo $tournament['id']; ?>" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Chỉnh sửa
                </a>
                <?php endif; ?>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="mb-3">Thông tin giải đấu</h5>
                    <p class="mb-2">
                        <strong><i class="fas fa-gamepad me-2"></i>Game:</strong> 
                        <?php echo e($tournament['game']); ?>
                    </p>
                    <p class="mb-2">
                        <strong><i class="fas fa-calendar me-2"></i>Thời gian:</strong> 
                        <?php echo formatDate($tournament['start_date']); ?> - <?php echo formatDate($tournament['end_date']); ?>
                    </p>
                    <p class="mb-2">
                        <strong><i class="fas fa-map-marker-alt me-2"></i>Địa điểm:</strong> 
                        <?php echo e($tournament['location']); ?>
                    </p>
                </div>
                
                <div class="col-md-6">
                    <h5 class="mb-3">Giải thưởng</h5>
                    <p class="tournament-prize mb-0">
                        <?php echo formatMoney($tournament['prize_pool']); ?>
                    </p>
                </div>
            </div>
            
            <?php if ($tournament['description']): ?>
            <div>
                <h5 class="mb-3">Mô tả</h5>
                <p><?php echo nl2br(e($tournament['description'])); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Teams and Results -->
    <div class="mb-4">
        <h3 class="mb-3"><i class="fas fa-users me-2"></i>Đội tham gia và kết quả</h3>
        
        <?php if (empty($teams)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>Chưa có đội nào tham gia.
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>Vị trí</th>
                        <th>Đội tuyển</th>
                        <th>Khu vực</th>
                        <th>Tiền thưởng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teams as $team): ?>
                    <tr>
                        <td>
                            <?php if ($team['placement'] == 1): ?>
                                <span class="badge bg-warning">🥇 1st</span>
                            <?php elseif ($team['placement'] == 2): ?>
                                <span class="badge bg-secondary">🥈 2nd</span>
                            <?php elseif ($team['placement'] == 3): ?>
                                <span class="badge bg-info">🥉 3rd</span>
                            <?php else: ?>
                                #<?php echo $team['placement']; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/GamerWiki/teams/view.php?id=<?php echo $team['id']; ?>" 
                               class="text-primary-custom text-decoration-none">
                                <?php echo e($team['name']); ?>
                            </a>
                        </td>
                        <td><?php echo e($team['region']); ?></td>
                        <td class="text-secondary-custom fw-bold">
                            <?php echo formatMoney($team['prize_money']); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Back Button -->
    <div class="mb-4">
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
