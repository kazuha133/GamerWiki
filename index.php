<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Lấy dữ liệu cho trang chủ
try {
    // Lấy 6 đội tuyển nổi bật (mới nhất)
    $stmtTeams = $pdo->query("SELECT * FROM teams ORDER BY created_at DESC LIMIT 6");
    $featuredTeams = $stmtTeams->fetchAll();
    
    // Lấy 6 giải đấu gần đây
    $stmtTournaments = $pdo->query("SELECT * FROM tournaments ORDER BY start_date DESC LIMIT 6");
    $latestTournaments = $stmtTournaments->fetchAll();
    
    // Lấy 8 tuyển thủ nổi bật
    $stmtPlayers = $pdo->query("
        SELECT p.*, t.name as team_name 
        FROM players p 
        LEFT JOIN teams t ON p.team_id = t.id 
        ORDER BY p.created_at DESC 
        LIMIT 8
    ");
    $topPlayers = $stmtPlayers->fetchAll();
    
    // Thống kê
    $stats = [
        'teams' => $pdo->query("SELECT COUNT(*) FROM teams")->fetchColumn(),
        'players' => $pdo->query("SELECT COUNT(*) FROM players")->fetchColumn(),
        'tournaments' => $pdo->query("SELECT COUNT(*) FROM tournaments")->fetchColumn(),
        'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    ];
} catch (Exception $e) {
    $featuredTeams = [];
    $latestTournaments = [];
    $topPlayers = [];
    $stats = ['teams' => 0, 'players' => 0, 'tournaments' => 0, 'users' => 0];
}

$pageTitle = 'Trang chủ';
include 'includes/header.php';
?>

<!-- Hero Section -->
<div class="container">
    <div class="hero-section text-center">
        <h1 class="display-4 fw-bold">
            <i class="fas fa-gamepad me-3"></i>Chào mừng đến với GamerWiki
        </h1>
        <p class="lead mb-4">
            Hệ thống quản lý đội tuyển esport hàng đầu. Theo dõi các đội tuyển, tuyển thủ và giải đấu chuyên nghiệp.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="teams/index.php" class="btn btn-primary btn-lg">
                <i class="fas fa-users me-2"></i>Xem Đội tuyển
            </a>
            <a href="tournaments/index.php" class="btn btn-secondary btn-lg">
                <i class="fas fa-trophy me-2"></i>Giải đấu
            </a>
        </div>
    </div>
    
    <!-- Stats Section -->
    <div class="row g-4 mb-5">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card text-center">
                <i class="fas fa-users fa-3x mb-3 text-primary-custom"></i>
                <h3><?php echo $stats['teams']; ?></h3>
                <p>Đội tuyển</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card text-center">
                <i class="fas fa-user-astronaut fa-3x mb-3 text-secondary-custom"></i>
                <h3><?php echo $stats['players']; ?></h3>
                <p>Tuyển thủ</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card text-center">
                <i class="fas fa-trophy fa-3x mb-3 text-primary-custom"></i>
                <h3><?php echo $stats['tournaments']; ?></h3>
                <p>Giải đấu</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card text-center">
                <i class="fas fa-user-circle fa-3x mb-3 text-secondary-custom"></i>
                <h3><?php echo $stats['users']; ?></h3>
                <p>Thành viên</p>
            </div>
        </div>
    </div>
    
    <!-- Featured Teams -->
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-users me-2"></i>Đội tuyển nổi bật</h2>
            <a href="teams/index.php" class="btn btn-outline-primary">
                Xem tất cả <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
        
        <div class="row g-4">
            <?php foreach ($featuredTeams as $team): ?>
            <div class="col-md-4 col-sm-6">
                <div class="card team-card h-100">
                    <div class="card-body text-center">
                        <img src="<?php echo e($team['logo_url']); ?>" 
                             alt="<?php echo e($team['name']); ?>" 
                             class="team-logo mb-3">
                        <h5 class="card-title"><?php echo e($team['name']); ?></h5>
                        <p class="card-text text-muted">
                            <i class="fas fa-gamepad me-1"></i><?php echo e($team['game']); ?><br>
                            <i class="fas fa-map-marker-alt me-1"></i><?php echo e($team['region']); ?>
                        </p>
                        <a href="teams/view.php?id=<?php echo $team['id']; ?>" 
                           class="btn btn-sm btn-primary">
                            Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <!-- Latest Tournaments -->
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-trophy me-2"></i>Giải đấu gần đây</h2>
            <a href="tournaments/index.php" class="btn btn-outline-primary">
                Xem tất cả <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
        
        <div class="row g-4">
            <?php foreach ($latestTournaments as $tournament): ?>
            <div class="col-md-4 col-sm-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-trophy me-2 text-warning"></i>
                            <?php echo e($tournament['name']); ?>
                        </h5>
                        <p class="card-text">
                            <i class="fas fa-gamepad me-2"></i><?php echo e($tournament['game']); ?><br>
                            <i class="fas fa-calendar me-2"></i><?php echo formatDate($tournament['start_date']); ?><br>
                            <i class="fas fa-map-marker-alt me-2"></i><?php echo e($tournament['location']); ?>
                        </p>
                        <p class="tournament-prize">
                            <?php echo formatMoney($tournament['prize_pool']); ?>
                        </p>
                        <a href="tournaments/view.php?id=<?php echo $tournament['id']; ?>" 
                           class="btn btn-sm btn-primary">
                            Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <!-- Top Players -->
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-user-astronaut me-2"></i>Tuyển thủ nổi bật</h2>
            <a href="players/index.php" class="btn btn-outline-primary">
                Xem tất cả <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
        
        <div class="row g-4">
            <?php foreach ($topPlayers as $player): ?>
            <div class="col-md-3 col-sm-6">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <img src="<?php echo e($player['photo_url']); ?>" 
                             alt="<?php echo e($player['nickname']); ?>" 
                             class="player-photo mb-3">
                        <h5 class="card-title"><?php echo e($player['nickname']); ?></h5>
                        <p class="card-text text-muted">
                            <?php echo e($player['real_name']); ?><br>
                            <span class="badge bg-primary mt-2">
                                <?php echo e($player['team_name'] ?? 'Free Agent'); ?>
                            </span>
                        </p>
                        <a href="players/view.php?id=<?php echo $player['id']; ?>" 
                           class="btn btn-sm btn-primary">
                            Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>
