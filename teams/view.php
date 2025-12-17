<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

$teamId = $_GET['id'] ?? 0;

// Lấy thông tin đội tuyển
$stmt = $pdo->prepare("SELECT t.*, u.username as creator_name FROM teams t 
                       LEFT JOIN users u ON t.created_by = u.id 
                       WHERE t.id = ?");
$stmt->execute([$teamId]);
$team = $stmt->fetch();

if (!$team) {
    $_SESSION['error'] = 'Không tìm thấy đội tuyển.';
    header('Location: index.php');
    exit();
}

// Lấy danh sách tuyển thủ
$stmtPlayers = $pdo->prepare("SELECT * FROM players WHERE team_id = ? ORDER BY created_at DESC");
$stmtPlayers->execute([$teamId]);
$players = $stmtPlayers->fetchAll();

// Lấy lịch sử giải đấu
$stmtTournaments = $pdo->prepare("
    SELECT t.*, tt.placement, tt.prize_money
    FROM team_tournaments tt
    JOIN tournaments t ON tt.tournament_id = t.id
    WHERE tt.team_id = ?
    ORDER BY t.start_date DESC
");
$stmtTournaments->execute([$teamId]);
$tournaments = $stmtTournaments->fetchAll();

$pageTitle = $team['name'];
include '../includes/header.php';
?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/GamerWiki/index.php">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="index.php">Đội tuyển</a></li>
            <li class="breadcrumb-item active"><?php echo e($team['name']); ?></li>
        </ol>
    </nav>
    
    <!-- Team Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    <img src="<?php echo e($team['logo_url']); ?>" 
                         alt="<?php echo e($team['name']); ?>" 
                         class="img-fluid rounded" 
                         style="max-width: 200px;">
                </div>
                
                <div class="col-md-9">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h1><?php echo e($team['name']); ?></h1>
                        <div>
                            <?php if (canEditTeam($team['created_by'])): ?>
                            <a href="edit.php?id=<?php echo $team['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-edit me-2"></i>Chỉnh sửa
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <span class="badge bg-primary me-2">
                            <i class="fas fa-gamepad me-1"></i><?php echo e($team['game']); ?>
                        </span>
                        <span class="badge bg-secondary me-2">
                            <i class="fas fa-map-marker-alt me-1"></i><?php echo e($team['region']); ?>
                        </span>
                        <?php if ($team['founded_date']): ?>
                        <span class="badge bg-info">
                            <i class="fas fa-calendar me-1"></i>Thành lập: <?php echo formatDate($team['founded_date']); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($team['description']): ?>
                    <p class="mb-3"><?php echo nl2br(e($team['description'])); ?></p>
                    <?php endif; ?>
                    
                    <?php if ($team['website']): ?>
                    <p>
                        <i class="fas fa-globe me-2"></i>
                        <a href="<?php echo e($team['website']); ?>" target="_blank" class="text-primary-custom">
                            <?php echo e($team['website']); ?>
                        </a>
                    </p>
                    <?php endif; ?>
                    
                    <p class="text-muted mb-0">
                        <small>
                            <i class="fas fa-user me-1"></i>Tạo bởi: <?php echo e($team['creator_name'] ?? 'N/A'); ?> | 
                            <i class="fas fa-clock me-1"></i><?php echo formatDate($team['created_at']); ?>
                        </small>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Players Section -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3><i class="fas fa-users me-2"></i>Danh sách tuyển thủ</h3>
            <?php if (canEditTeam($team['created_by'])): ?>
            <a href="/GamerWiki/players/create.php?team_id=<?php echo $team['id']; ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-2"></i>Thêm tuyển thủ
            </a>
            <?php endif; ?>
        </div>
        
        <?php if (empty($players)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>Chưa có tuyển thủ nào.
        </div>
        <?php else: ?>
        <div class="row g-3">
            <?php foreach ($players as $player): ?>
            <div class="col-md-3 col-sm-6">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <img src="<?php echo e($player['photo_url']); ?>" 
                             alt="<?php echo e($player['nickname']); ?>" 
                             class="player-photo mb-2">
                        <h6 class="card-title"><?php echo e($player['nickname']); ?></h6>
                        <p class="card-text small text-muted mb-0">
                            <?php echo e($player['role_position']); ?>
                        </p>
                        <a href="/GamerWiki/players/view.php?id=<?php echo $player['id']; ?>" 
                           class="btn btn-sm btn-primary mt-2">
                            Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Tournament History -->
    <div class="mb-4">
        <h3 class="mb-3"><i class="fas fa-trophy me-2"></i>Lịch sử giải đấu</h3>
        
        <?php if (empty($tournaments)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>Chưa tham gia giải đấu nào.
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>Giải đấu</th>
                        <th>Game</th>
                        <th>Ngày</th>
                        <th>Vị trí</th>
                        <th>Tiền thưởng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tournaments as $tournament): ?>
                    <tr>
                        <td>
                            <a href="/GamerWiki/tournaments/view.php?id=<?php echo $tournament['id']; ?>" 
                               class="text-primary-custom text-decoration-none">
                                <?php echo e($tournament['name']); ?>
                            </a>
                        </td>
                        <td><?php echo e($tournament['game']); ?></td>
                        <td><?php echo formatDate($tournament['start_date']); ?></td>
                        <td>
                            <?php if ($tournament['placement'] == 1): ?>
                                <span class="badge bg-warning">🥇 1st</span>
                            <?php elseif ($tournament['placement'] == 2): ?>
                                <span class="badge bg-secondary">🥈 2nd</span>
                            <?php elseif ($tournament['placement'] == 3): ?>
                                <span class="badge bg-info">🥉 3rd</span>
                            <?php else: ?>
                                #<?php echo $tournament['placement']; ?>
                            <?php endif; ?>
                        </td>
                        <td class="text-secondary-custom fw-bold">
                            <?php echo formatMoney($tournament['prize_money']); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
