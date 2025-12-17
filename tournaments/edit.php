<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin(); // Chỉ admin mới có thể edit tournaments

$tournamentId = $_GET['id'] ?? 0;

// Lấy thông tin giải đấu
$stmt = $pdo->prepare("SELECT * FROM tournaments WHERE id = ?");
$stmt->execute([$tournamentId]);
$tournament = $stmt->fetch();

if (!$tournament) {
    redirectWith('index.php', 'error', 'Không tìm thấy giải đấu.');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        // Xóa giải đấu
        try {
            $stmt = $pdo->prepare("DELETE FROM tournaments WHERE id = ?");
            $stmt->execute([$tournamentId]);
            redirectWith('index.php', 'success', 'Đã xóa giải đấu.');
        } catch (Exception $e) {
            $error = 'Không thể xóa giải đấu. Có thể có dữ liệu liên quan.';
        }
    } else {
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            $error = 'Invalid request.';
        } else {
            $name = cleanInput($_POST['name'] ?? '');
            $game = cleanInput($_POST['game'] ?? '');
            $start_date = $_POST['start_date'] ?? '';
            $end_date = $_POST['end_date'] ?? '';
            $prize_pool = $_POST['prize_pool'] ?? '';
            $location = cleanInput($_POST['location'] ?? '');
            $description = cleanInput($_POST['description'] ?? '');
            
            if (empty($name) || empty($game)) {
                $error = 'Vui lòng nhập tên giải đấu và game.';
            } else {
                try {
                    $stmt = $pdo->prepare("
                        UPDATE tournaments 
                        SET name = ?, game = ?, start_date = ?, end_date = ?, 
                            prize_pool = ?, location = ?, description = ?
                        WHERE id = ?
                    ");
                    
                    $stmt->execute([
                        $name,
                        $game,
                        $start_date ?: null,
                        $end_date ?: null,
                        $prize_pool ?: null,
                        $location,
                        $description,
                        $tournamentId
                    ]);
                    
                    $success = 'Cập nhật thành công!';
                    
                    // Refresh data
                    $stmt = $pdo->prepare("SELECT * FROM tournaments WHERE id = ?");
                    $stmt->execute([$tournamentId]);
                    $tournament = $stmt->fetch();
                } catch (Exception $e) {
                    $error = 'Đã xảy ra lỗi. Vui lòng thử lại.';
                }
            }
        }
    }
}

$pageTitle = 'Chỉnh sửa - ' . $tournament['name'];
include '../includes/header.php';
?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/GamerWiki/index.php">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="index.php">Giải đấu</a></li>
            <li class="breadcrumb-item"><a href="view.php?id=<?php echo $tournament['id']; ?>"><?php echo e($tournament['name']); ?></a></li>
            <li class="breadcrumb-item active">Chỉnh sửa</li>
        </ol>
    </nav>
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0"><i class="fas fa-edit me-2"></i>Chỉnh sửa giải đấu</h3>
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
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên giải đấu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo e($tournament['name']); ?>" required>
                            <div class="invalid-feedback">Vui lòng nhập tên giải đấu.</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="game" class="form-label">Game <span class="text-danger">*</span></label>
                                <select class="form-select" id="game" name="game" required>
                                    <option value="">Chọn game...</option>
                                    <?php 
                                    $games = ['League of Legends', 'Dota 2', 'Valorant', 'CS:GO', 'Overwatch', 'Mobile Legends', 'PUBG'];
                                    foreach ($games as $g): 
                                    ?>
                                    <option value="<?php echo $g; ?>" <?php echo $tournament['game'] === $g ? 'selected' : ''; ?>>
                                        <?php echo $g; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Vui lòng chọn game.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label">Địa điểm</label>
                                <input type="text" class="form-control" id="location" name="location"
                                       value="<?php echo e($tournament['location']); ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="start_date" class="form-label">Ngày bắt đầu</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                       value="<?php echo e($tournament['start_date']); ?>">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="end_date" class="form-label">Ngày kết thúc</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                       value="<?php echo e($tournament['end_date']); ?>">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="prize_pool" class="form-label">Giải thưởng ($)</label>
                                <input type="number" class="form-control" id="prize_pool" name="prize_pool"
                                       value="<?php echo e($tournament['prize_pool']); ?>" step="0.01">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="5"><?php echo e($tournament['description']); ?></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Lưu thay đổi
                                </button>
                                <a href="view.php?id=<?php echo $tournament['id']; ?>" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Hủy
                                </a>
                            </div>
                            
                            <button type="submit" name="delete" class="btn btn-danger" 
                                    data-confirm-delete="Bạn có chắc muốn xóa giải đấu này?">
                                <i class="fas fa-trash me-2"></i>Xóa giải đấu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
