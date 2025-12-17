<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Lấy filter parameters
$game = $_GET['game'] ?? '';
$region = $_GET['region'] ?? '';
$search = $_GET['search'] ?? '';

// Build query với filters
$sql = "SELECT t.*, u.username as creator_name FROM teams t 
        LEFT JOIN users u ON t.created_by = u.id 
        WHERE 1=1";
$params = [];

if (!empty($game)) {
    $sql .= " AND t.game = ?";
    $params[] = $game;
}

if (!empty($region)) {
    $sql .= " AND t.region = ?";
    $params[] = $region;
}

if (!empty($search)) {
    $sql .= " AND (t.name LIKE ? OR t.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY t.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$teams = $stmt->fetchAll();

// Lấy danh sách games và regions cho filter
$games = $pdo->query("SELECT DISTINCT game FROM teams ORDER BY game")->fetchAll(PDO::FETCH_COLUMN);
$regions = $pdo->query("SELECT DISTINCT region FROM teams WHERE region IS NOT NULL ORDER BY region")->fetchAll(PDO::FETCH_COLUMN);

$pageTitle = 'Đội tuyển';
include '../includes/header.php';
?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/GamerWiki/index.php">Trang chủ</a></li>
            <li class="breadcrumb-item active">Đội tuyển</li>
        </ol>
    </nav>
    
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-users me-2"></i>Đội tuyển</h1>
        <?php if (isLoggedIn()): ?>
        <a href="create.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tạo đội tuyển
        </a>
        <?php endif; ?>
    </div>
    
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Game</label>
                    <select name="game" class="form-select">
                        <option value="">Tất cả</option>
                        <?php foreach ($games as $g): ?>
                        <option value="<?php echo e($g); ?>" <?php echo $game === $g ? 'selected' : ''; ?>>
                            <?php echo e($g); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Khu vực</label>
                    <select name="region" class="form-select">
                        <option value="">Tất cả</option>
                        <?php foreach ($regions as $r): ?>
                        <option value="<?php echo e($r); ?>" <?php echo $region === $r ? 'selected' : ''; ?>>
                            <?php echo e($r); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Tìm kiếm</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Tên đội..." value="<?php echo e($search); ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Teams Grid -->
    <?php if (empty($teams)): ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle me-2"></i>
            Không tìm thấy đội tuyển nào.
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($teams as $team): ?>
            <div class="col-md-4 col-sm-6">
                <div class="card team-card h-100">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <img src="<?php echo e($team['logo_url']); ?>" 
                                 alt="<?php echo e($team['name']); ?>" 
                                 class="team-logo">
                        </div>
                        
                        <h5 class="card-title text-center"><?php echo e($team['name']); ?></h5>
                        
                        <p class="card-text text-center text-muted">
                            <i class="fas fa-gamepad me-1"></i><?php echo e($team['game']); ?><br>
                            <i class="fas fa-map-marker-alt me-1"></i><?php echo e($team['region']); ?><br>
                            <i class="fas fa-calendar me-1"></i>Thành lập: <?php echo formatDate($team['founded_date']); ?>
                        </p>
                        
                        <?php if ($team['description']): ?>
                        <p class="card-text small">
                            <?php echo truncateText(e($team['description']), 100); ?>
                        </p>
                        <?php endif; ?>
                        
                        <div class="d-flex gap-2">
                            <a href="view.php?id=<?php echo $team['id']; ?>" 
                               class="btn btn-sm btn-primary flex-fill">
                                <i class="fas fa-eye me-1"></i>Chi tiết
                            </a>
                            
                            <?php if (canEditTeam($team['created_by'])): ?>
                            <a href="edit.php?id=<?php echo $team['id']; ?>" 
                               class="btn btn-sm btn-secondary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
