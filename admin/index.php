<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin();

// Lấy thống kê
$stats = [
    'total_users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'total_teams' => $pdo->query("SELECT COUNT(*) FROM teams")->fetchColumn(),
    'total_players' => $pdo->query("SELECT COUNT(*) FROM players")->fetchColumn(),
    'total_tournaments' => $pdo->query("SELECT COUNT(*) FROM tournaments")->fetchColumn(),
    'admin_users' => $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn(),
    'regular_users' => $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn(),
];

// Recent activity
$recentTeams = $pdo->query("SELECT t.*, u.username FROM teams t LEFT JOIN users u ON t.created_by = u.id ORDER BY t.created_at DESC LIMIT 5")->fetchAll();
$recentPlayers = $pdo->query("SELECT p.*, t.name as team_name FROM players p LEFT JOIN teams t ON p.team_id = t.id ORDER BY p.created_at DESC LIMIT 5")->fetchAll();
$recentUsers = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();

$pageTitle = 'Admin Dashboard';
include '../includes/header.php';
?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/GamerWiki/index.php">Trang chủ</a></li>
            <li class="breadcrumb-item active">Admin Dashboard</li>
        </ol>
    </nav>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-cog me-2"></i>Admin Dashboard</h1>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="stat-card">
                <i class="fas fa-users fa-3x mb-3 text-primary-custom"></i>
                <h3><?php echo $stats['total_users']; ?></h3>
                <p>Tổng Users</p>
                <a href="users.php" class="btn btn-sm btn-outline-primary">Quản lý</a>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card">
                <i class="fas fa-shield-alt fa-3x mb-3 text-secondary-custom"></i>
                <h3><?php echo $stats['total_teams']; ?></h3>
                <p>Đội tuyển</p>
                <a href="/GamerWiki/teams/index.php" class="btn btn-sm btn-outline-primary">Xem</a>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card">
                <i class="fas fa-user-astronaut fa-3x mb-3 text-primary-custom"></i>
                <h3><?php echo $stats['total_players']; ?></h3>
                <p>Tuyển thủ</p>
                <a href="/GamerWiki/players/index.php" class="btn btn-sm btn-outline-primary">Xem</a>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card">
                <i class="fas fa-trophy fa-3x mb-3 text-secondary-custom"></i>
                <h3><?php echo $stats['total_tournaments']; ?></h3>
                <p>Giải đấu</p>
                <a href="/GamerWiki/tournaments/index.php" class="btn btn-sm btn-outline-primary">Xem</a>
            </div>
        </div>
    </div>
    
    <!-- User Statistics -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5><i class="fas fa-user-shield me-2"></i>Phân loại Users</h5>
                    <div class="d-flex justify-content-around mt-3">
                        <div class="text-center">
                            <h3 class="text-warning"><?php echo $stats['admin_users']; ?></h3>
                            <p class="mb-0">Admins</p>
                        </div>
                        <div class="text-center">
                            <h3 class="text-info"><?php echo $stats['regular_users']; ?></h3>
                            <p class="mb-0">Regular Users</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                    <div class="d-grid gap-2 mt-3">
                        <a href="/GamerWiki/teams/create.php" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i>Tạo đội tuyển mới
                        </a>
                        <a href="/GamerWiki/players/create.php" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i>Thêm tuyển thủ mới
                        </a>
                        <a href="/GamerWiki/tournaments/create.php" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i>Tạo giải đấu mới
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Recent Users -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Users mới nhất</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <?php foreach ($recentUsers as $user): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0"><?php echo e($user['username']); ?></h6>
                                    <small class="text-muted">
                                        <?php echo e(ucfirst($user['role'])); ?> - <?php echo formatDate($user['created_at']); ?>
                                    </small>
                                </div>
                                <a href="users.php" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="users.php" class="btn btn-sm btn-outline-primary w-100 mt-3">
                        Xem tất cả users
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Recent Teams -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Đội tuyển mới</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <?php foreach ($recentTeams as $team): ?>
                        <a href="/GamerWiki/teams/view.php?id=<?php echo $team['id']; ?>" 
                           class="list-group-item list-group-item-action">
                            <h6 class="mb-0"><?php echo e($team['name']); ?></h6>
                            <small class="text-muted">
                                <?php echo e($team['game']); ?> - by <?php echo e($team['username']); ?>
                            </small>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <a href="/GamerWiki/teams/index.php" class="btn btn-sm btn-outline-primary w-100 mt-3">
                        Xem tất cả đội tuyển
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Recent Players -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user-astronaut me-2"></i>Tuyển thủ mới</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <?php foreach ($recentPlayers as $player): ?>
                        <a href="/GamerWiki/players/view.php?id=<?php echo $player['id']; ?>" 
                           class="list-group-item list-group-item-action">
                            <h6 class="mb-0"><?php echo e($player['nickname']); ?></h6>
                            <small class="text-muted">
                                <?php echo e($player['team_name'] ?? 'Free Agent'); ?>
                            </small>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <a href="/GamerWiki/players/index.php" class="btn btn-sm btn-outline-primary w-100 mt-3">
                        Xem tất cả tuyển thủ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
