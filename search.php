<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$query = $_GET['q'] ?? '';
$results = ['teams' => [], 'players' => [], 'tournaments' => []];

if (!empty($query)) {
    // Search teams
    $stmt = $pdo->prepare("SELECT * FROM teams WHERE name LIKE ? OR description LIKE ? LIMIT 10");
    $stmt->execute(["%$query%", "%$query%"]);
    $results['teams'] = $stmt->fetchAll();
    
    // Search players
    $stmt = $pdo->prepare("SELECT p.*, t.name as team_name FROM players p LEFT JOIN teams t ON p.team_id = t.id WHERE p.nickname LIKE ? OR p.real_name LIKE ? LIMIT 10");
    $stmt->execute(["%$query%", "%$query%"]);
    $results['players'] = $stmt->fetchAll();
    
    // Search tournaments
    $stmt = $pdo->prepare("SELECT * FROM tournaments WHERE name LIKE ? OR description LIKE ? LIMIT 10");
    $stmt->execute(["%$query%", "%$query%"]);
    $results['tournaments'] = $stmt->fetchAll();
}

$totalResults = count($results['teams']) + count($results['players']) + count($results['tournaments']);
$pageTitle = 'Tìm kiếm: ' . ($query ?: 'Tất cả');
include 'includes/header.php';
?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/GamerWiki/index.php">Trang chủ</a></li>
            <li class="breadcrumb-item active">Tìm kiếm</li>
        </ol>
    </nav>
    
    <!-- Search Header -->
    <div class="mb-4">
        <h1><i class="fas fa-search me-2"></i>Kết quả tìm kiếm</h1>
        <?php if (!empty($query)): ?>
        <p class="text-muted">
            Tìm kiếm cho: "<strong><?php echo e($query); ?></strong>" - Tìm thấy <?php echo $totalResults; ?> kết quả
        </p>
        <?php endif; ?>
    </div>
    
    <!-- Search Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="">
                <div class="input-group">
                    <input type="text" name="q" class="form-control form-control-lg" 
                           placeholder="Tìm kiếm đội tuyển, tuyển thủ, giải đấu..." 
                           value="<?php echo e($query); ?>" autofocus>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Tìm kiếm
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <?php if (empty($query)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Nhập từ khóa để tìm kiếm.
        </div>
    <?php elseif ($totalResults === 0): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Không tìm thấy kết quả nào cho "<strong><?php echo e($query); ?></strong>"
        </div>
    <?php else: ?>
        
        <!-- Teams Results -->
        <?php if (!empty($results['teams'])): ?>
        <div class="mb-5">
            <h3 class="mb-3"><i class="fas fa-users me-2"></i>Đội tuyển (<?php echo count($results['teams']); ?>)</h3>
            <div class="row g-3">
                <?php foreach ($results['teams'] as $team): ?>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title"><?php echo e($team['name']); ?></h6>
                            <p class="card-text small text-muted mb-2">
                                <?php echo e($team['game']); ?> - <?php echo e($team['region']); ?>
                            </p>
                            <a href="teams/view.php?id=<?php echo $team['id']; ?>" class="btn btn-sm btn-primary">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Players Results -->
        <?php if (!empty($results['players'])): ?>
        <div class="mb-5">
            <h3 class="mb-3"><i class="fas fa-user-astronaut me-2"></i>Tuyển thủ (<?php echo count($results['players']); ?>)</h3>
            <div class="row g-3">
                <?php foreach ($results['players'] as $player): ?>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h6 class="card-title"><?php echo e($player['nickname']); ?></h6>
                            <p class="card-text small text-muted mb-2">
                                <?php echo e($player['real_name']); ?>
                            </p>
                            <?php if ($player['team_name']): ?>
                            <span class="badge bg-primary mb-2"><?php echo e($player['team_name']); ?></span>
                            <?php endif; ?>
                            <br>
                            <a href="players/view.php?id=<?php echo $player['id']; ?>" class="btn btn-sm btn-primary">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Tournaments Results -->
        <?php if (!empty($results['tournaments'])): ?>
        <div class="mb-5">
            <h3 class="mb-3"><i class="fas fa-trophy me-2"></i>Giải đấu (<?php echo count($results['tournaments']); ?>)</h3>
            <div class="row g-3">
                <?php foreach ($results['tournaments'] as $tournament): ?>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title"><?php echo e($tournament['name']); ?></h6>
                            <p class="card-text small text-muted mb-2">
                                <?php echo e($tournament['game']); ?> - <?php echo formatDate($tournament['start_date']); ?>
                            </p>
                            <p class="text-secondary-custom fw-bold mb-2">
                                <?php echo formatMoney($tournament['prize_pool']); ?>
                            </p>
                            <a href="tournaments/view.php?id=<?php echo $tournament['id']; ?>" class="btn btn-sm btn-primary">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
