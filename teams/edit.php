<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireLogin();

$teamId = $_GET['id'] ?? 0;

// Lấy thông tin đội tuyển
$stmt = $pdo->prepare("SELECT * FROM teams WHERE id = ?");
$stmt->execute([$teamId]);
$team = $stmt->fetch();

if (!$team) {
    redirectWith('index.php', 'error', 'Không tìm thấy đội tuyển.');
}

// Kiểm tra quyền chỉnh sửa
if (!canEditTeam($team['created_by'])) {
    redirectWith('view.php?id=' . $teamId, 'error', 'Bạn không có quyền chỉnh sửa đội tuyển này.');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        // Xóa đội tuyển (chỉ admin)
        if (isAdmin()) {
            try {
                $stmt = $pdo->prepare("DELETE FROM teams WHERE id = ?");
                $stmt->execute([$teamId]);
                redirectWith('index.php', 'success', 'Đã xóa đội tuyển.');
            } catch (Exception $e) {
                $error = 'Không thể xóa đội tuyển. Có thể đội có dữ liệu liên quan.';
            }
        }
    } else {
        // Cập nhật đội tuyển
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            $error = 'Invalid request.';
        } else {
            $name = cleanInput($_POST['name'] ?? '');
            $game = cleanInput($_POST['game'] ?? '');
            $region = cleanInput($_POST['region'] ?? '');
            $founded_date = $_POST['founded_date'] ?? '';
            $logo_url = cleanInput($_POST['logo_url'] ?? '');
            $description = cleanInput($_POST['description'] ?? '');
            $website = cleanInput($_POST['website'] ?? '');
            
            if (empty($name) || empty($game)) {
                $error = 'Vui lòng nhập tên đội và game.';
            } else {
                try {
                    $stmt = $pdo->prepare("
                        UPDATE teams 
                        SET name = ?, game = ?, region = ?, founded_date = ?, 
                            logo_url = ?, description = ?, website = ?
                        WHERE id = ?
                    ");
                    
                    $stmt->execute([
                        $name,
                        $game,
                        $region,
                        $founded_date ?: null,
                        $logo_url ?: 'https://via.placeholder.com/150',
                        $description,
                        $website,
                        $teamId
                    ]);
                    
                    $success = 'Cập nhật thành công!';
                    
                    // Refresh team data
                    $stmt = $pdo->prepare("SELECT * FROM teams WHERE id = ?");
                    $stmt->execute([$teamId]);
                    $team = $stmt->fetch();
                } catch (Exception $e) {
                    $error = 'Đã xảy ra lỗi. Vui lòng thử lại.';
                }
            }
        }
    }
}

$pageTitle = 'Chỉnh sửa - ' . $team['name'];
include '../includes/header.php';
?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/GamerWiki/index.php">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="index.php">Đội tuyển</a></li>
            <li class="breadcrumb-item"><a href="view.php?id=<?php echo $team['id']; ?>"><?php echo e($team['name']); ?></a></li>
            <li class="breadcrumb-item active">Chỉnh sửa</li>
        </ol>
    </nav>
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0"><i class="fas fa-edit me-2"></i>Chỉnh sửa đội tuyển</h3>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <?php echo showError($error); ?>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <?php echo showSuccess($success); ?>
                    <?php endif; ?>
                    
                    <form method="POST" action="" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Tên đội <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo e($team['name']); ?>" required>
                                <div class="invalid-feedback">Vui lòng nhập tên đội.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="game" class="form-label">Game <span class="text-danger">*</span></label>
                                <select class="form-select" id="game" name="game" required>
                                    <option value="">Chọn game...</option>
                                    <?php 
                                    $games = ['League of Legends', 'Dota 2', 'Valorant', 'CS:GO', 'Overwatch', 'Mobile Legends', 'PUBG'];
                                    foreach ($games as $g): 
                                    ?>
                                    <option value="<?php echo $g; ?>" <?php echo $team['game'] === $g ? 'selected' : ''; ?>>
                                        <?php echo $g; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Vui lòng chọn game.</div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="region" class="form-label">Khu vực</label>
                                <select class="form-select" id="region" name="region">
                                    <option value="">Chọn khu vực...</option>
                                    <?php 
                                    $regions = ['North America', 'Europe', 'Korea', 'China', 'Southeast Asia', 'Vietnam', 'Brazil', 'Latin America'];
                                    foreach ($regions as $r): 
                                    ?>
                                    <option value="<?php echo $r; ?>" <?php echo $team['region'] === $r ? 'selected' : ''; ?>>
                                        <?php echo $r; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="founded_date" class="form-label">Ngày thành lập</label>
                                <input type="date" class="form-control" id="founded_date" name="founded_date"
                                       value="<?php echo e($team['founded_date']); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="logo_url" class="form-label">URL Logo</label>
                            <input type="url" class="form-control" id="logo_url" name="logo_url"
                                   value="<?php echo e($team['logo_url']); ?>"
                                   placeholder="https://example.com/logo.png">
                        </div>
                        
                        <div class="mb-3">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" class="form-control" id="website" name="website"
                                   value="<?php echo e($team['website']); ?>"
                                   placeholder="https://example.com">
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="5"><?php echo e($team['description']); ?></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Lưu thay đổi
                                </button>
                                <a href="view.php?id=<?php echo $team['id']; ?>" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Hủy
                                </a>
                            </div>
                            
                            <?php if (isAdmin()): ?>
                            <button type="submit" name="delete" class="btn btn-danger" 
                                    data-confirm-delete="Bạn có chắc muốn xóa đội tuyển này?">
                                <i class="fas fa-trash me-2"></i>Xóa đội tuyển
                            </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
