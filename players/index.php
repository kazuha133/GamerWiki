<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Lấy filter parameters
$team_id = $_GET['team_id'] ?? '';
$game = $_GET['game'] ?? '';
$nationality = $_GET['nationality'] ?? '';
$search = $_GET['search'] ?? '';

// Build query với filters
$sql = "SELECT p.*, t.name as team_name, t.game as team_game 
        FROM players p 
        LEFT JOIN teams t ON p.team_id = t.id 
        WHERE 1=1";
$params = [];

if (!empty($team_id)) {
    $sql .= " AND p.team_id = ?";
    $params[] = $team_id;
}

if (!empty($game)) {
    $sql .= " AND t.game = ?";
    $params[] = $game;
}

if (!empty($nationality)) {
    $sql .= " AND p.nationality = ?";
    $params[] = $nationality;
}

if (!empty($search)) {
    $sql .= " AND (p.nickname LIKE ? OR p.real_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$players = $stmt->fetchAll();

// Lấy danh sách teams, games và nationalities cho filter
$teams = $pdo->query("SELECT id, name FROM teams ORDER BY name")->fetchAll();
$games = $pdo->query("SELECT DISTINCT game FROM teams ORDER BY game")->fetchAll(PDO::FETCH_COLUMN);
$nationalities = $pdo->query("SELECT DISTINCT nationality FROM players WHERE nationality IS NOT NULL ORDER BY nationality")->fetchAll(PDO::FETCH_COLUMN);

$pageTitle = 'Tuyển thủ';
include '../includes/header.php';
?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/GamerWiki/index.php">Trang chủ</a></li>
            <li class="breadcrumb-item active">Tuyển thủ</li>
        </ol>
    </nav>
    
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-user-astronaut me-2"></i>Tuyển thủ</h1>
        <?php if (isLoggedIn()): ?>
        <a href="create.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Thêm tuyển thủ
        </a>
        <?php endif; ?>
    </div>
    
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Đội tuyển</label>
                    <select name="team_id" class="form-select">
                        <option value="">Tất cả</option>
                        <?php foreach ($teams as $team): ?>
                        <option value="<?php echo $team['id']; ?>" <?php echo $team_id == $team['id'] ? 'selected' : ''; ?>>
                            <?php echo e($team['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
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
                    <label class="form-label">Quốc tịch</label>
                    <select name="nationality" class="form-select">
                        <option value="">Tất cả</option>
                        <?php foreach ($nationalities as $n): ?>
                        <option value="<?php echo e($n); ?>" <?php echo $nationality === $n ? 'selected' : ''; ?>>
                            <?php echo e($n); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Tìm kiếm</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Nickname..." value="<?php echo e($search); ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Players Grid -->
    <?php if (empty($players)): ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle me-2"></i>
            Không tìm thấy tuyển thủ nào.
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($players as $player): ?>
            <div class="col-md-3 col-sm-6">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <img src="<?php echo e($player['photo_url']); ?>" 
                             alt="<?php echo e($player['nickname']); ?>" 
                             class="player-photo mb-3">
                        
                        <h5 class="card-title"><?php echo e($player['nickname']); ?></h5>
                        <p class="card-text text-muted small">
                            <?php echo e($player['real_name']); ?>
                        </p>
                        
                        <?php if ($player['team_name']): ?>
                        <p class="mb-2">
                            <span class="badge bg-primary">
                                <?php echo e($player['team_name']); ?>
                            </span>
                        </p>
                        <?php endif; ?>
                        
                        <p class="card-text text-muted small mb-0">
                            <i class="fas fa-briefcase me-1"></i><?php echo e($player['role_position']); ?><br>
                            <i class="fas fa-flag me-1"></i><?php echo e($player['nationality']); ?>
                        </p>
                        
                        <a href="view.php?id=<?php echo $player['id']; ?>" 
                           class="btn btn-sm btn-primary mt-3">
                            Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
