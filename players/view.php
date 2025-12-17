<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

$playerId = $_GET['id'] ?? 0;

// Lấy thông tin tuyển thủ
$stmt = $pdo->prepare("
    SELECT p.*, t.name as team_name, t.id as team_id, t.game as team_game
    FROM players p 
    LEFT JOIN teams t ON p.team_id = t.id 
    WHERE p.id = ?
");
$stmt->execute([$playerId]);
$player = $stmt->fetch();

if (!$player) {
    redirectWith('index.php', 'error', 'Không tìm thấy tuyển thủ.');
}

// Kiểm tra quyền chỉnh sửa (nếu player thuộc team của user)
$canEdit = false;
if (isAdmin()) {
    $canEdit = true;
} elseif (isLoggedIn() && $player['team_id']) {
    $stmtTeam = $pdo->prepare("SELECT created_by FROM teams WHERE id = ?");
    $stmtTeam->execute([$player['team_id']]);
    $team = $stmtTeam->fetch();
    if ($team && $team['created_by'] == $_SESSION['user_id']) {
        $canEdit = true;
    }
}

$pageTitle = $player['nickname'];
include '../includes/header.php';
?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/GamerWiki/index.php">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="index.php">Tuyển thủ</a></li>
            <li class="breadcrumb-item active"><?php echo e($player['nickname']); ?></li>
        </ol>
    </nav>
    
    <!-- Player Profile -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    <img src="<?php echo e($player['photo_url']); ?>" 
                         alt="<?php echo e($player['nickname']); ?>" 
                         class="img-fluid rounded" 
                         style="max-width: 250px;">
                </div>
                
                <div class="col-md-9">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h1 class="mb-2"><?php echo e($player['nickname']); ?></h1>
                            <h4 class="text-muted"><?php echo e($player['real_name']); ?></h4>
                        </div>
                        <?php if ($canEdit): ?>
                        <a href="edit.php?id=<?php echo $player['id']; ?>" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Chỉnh sửa
                        </a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">Thông tin cá nhân</h5>
                            <p class="mb-2">
                                <strong><i class="fas fa-briefcase me-2"></i>Vị trí:</strong> 
                                <?php echo e($player['role_position']); ?>
                            </p>
                            <p class="mb-2">
                                <strong><i class="fas fa-flag me-2"></i>Quốc tịch:</strong> 
                                <?php echo e($player['nationality']); ?>
                            </p>
                            <?php if ($player['birth_date']): ?>
                            <p class="mb-2">
                                <strong><i class="fas fa-birthday-cake me-2"></i>Ngày sinh:</strong> 
                                <?php echo formatDate($player['birth_date']); ?>
                            </p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="mb-3">Đội tuyển</h5>
                            <?php if ($player['team_name']): ?>
                            <p class="mb-2">
                                <strong><i class="fas fa-users me-2"></i>Đội:</strong> 
                                <a href="/GamerWiki/teams/view.php?id=<?php echo $player['team_id']; ?>" 
                                   class="text-primary-custom">
                                    <?php echo e($player['team_name']); ?>
                                </a>
                            </p>
                            <p class="mb-2">
                                <strong><i class="fas fa-gamepad me-2"></i>Game:</strong> 
                                <?php echo e($player['team_game']); ?>
                            </p>
                            <?php else: ?>
                            <p class="text-muted">
                                <i class="fas fa-info-circle me-2"></i>Free Agent
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if ($player['biography']): ?>
                    <div>
                        <h5 class="mb-3">Tiểu sử</h5>
                        <p class="text-justify"><?php echo nl2br(e($player['biography'])); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <p class="text-muted mb-0 mt-3">
                        <small>
                            <i class="fas fa-clock me-1"></i>Thêm vào: <?php echo formatDate($player['created_at']); ?>
                        </small>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Back Button -->
    <div class="mb-4">
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
