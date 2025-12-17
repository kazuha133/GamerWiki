<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Lấy filter parameters
$game = $_GET['game'] ?? '';
$status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

// Build query với filters
$sql = "SELECT * FROM tournaments WHERE 1=1";
$params = [];

if (!empty($game)) {
    $sql .= " AND game = ?";
    $params[] = $game;
}

if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Filter by status
if ($status === 'ongoing') {
    $sql .= " AND start_date <= CURDATE() AND end_date >= CURDATE()";
} elseif ($status === 'upcoming') {
    $sql .= " AND start_date > CURDATE()";
} elseif ($status === 'past') {
    $sql .= " AND end_date < CURDATE()";
}

$sql .= " ORDER BY start_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tournaments = $stmt->fetchAll();

// Lấy danh sách games cho filter
$games = $pdo->query("SELECT DISTINCT game FROM tournaments ORDER BY game")->fetchAll(PDO::FETCH_COLUMN);

$pageTitle = 'Giải đấu';
include '../includes/header.php';
?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/GamerWiki/index.php">Trang chủ</a></li>
            <li class="breadcrumb-item active">Giải đấu</li>
        </ol>
    </nav>
    
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-trophy me-2"></i>Giải đấu</h1>
        <?php if (isLoggedIn()): ?>
        <a href="create.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tạo giải đấu
        </a>
        <?php endif; ?>
    </div>
    
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-3">
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
                
                <div class="col-md-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="ongoing" <?php echo $status === 'ongoing' ? 'selected' : ''; ?>>Đang diễn ra</option>
                        <option value="upcoming" <?php echo $status === 'upcoming' ? 'selected' : ''; ?>>Sắp diễn ra</option>
                        <option value="past" <?php echo $status === 'past' ? 'selected' : ''; ?>>Đã kết thúc</option>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Tìm kiếm</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Tên giải đấu..." value="<?php echo e($search); ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Tournaments List -->
    <?php if (empty($tournaments)): ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle me-2"></i>
            Không tìm thấy giải đấu nào.
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($tournaments as $tournament): ?>
            <div class="col-md-4 col-sm-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-trophy me-2 text-warning"></i>
                            <?php echo e($tournament['name']); ?>
                        </h5>
                        
                        <p class="card-text">
                            <i class="fas fa-gamepad me-2"></i><?php echo e($tournament['game']); ?><br>
                            <i class="fas fa-calendar me-2"></i>
                            <?php echo formatDate($tournament['start_date']); ?> - <?php echo formatDate($tournament['end_date']); ?><br>
                            <i class="fas fa-map-marker-alt me-2"></i><?php echo e($tournament['location']); ?>
                        </p>
                        
                        <p class="tournament-prize mb-3">
                            <?php echo formatMoney($tournament['prize_pool']); ?>
                        </p>
                        
                        <?php if ($tournament['description']): ?>
                        <p class="card-text small text-muted">
                            <?php echo truncateText(e($tournament['description']), 100); ?>
                        </p>
                        <?php endif; ?>
                        
                        <div class="d-flex gap-2">
                            <a href="view.php?id=<?php echo $tournament['id']; ?>" 
                               class="btn btn-sm btn-primary flex-fill">
                                <i class="fas fa-eye me-1"></i>Chi tiết
                            </a>
                            
                            <?php if (isAdmin()): ?>
                            <a href="edit.php?id=<?php echo $tournament['id']; ?>" 
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
