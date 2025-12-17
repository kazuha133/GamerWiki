<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireLogin();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                    INSERT INTO tournaments (name, game, start_date, end_date, prize_pool, location, description)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $name,
                    $game,
                    $start_date ?: null,
                    $end_date ?: null,
                    $prize_pool ?: null,
                    $location,
                    $description
                ]);
                
                $tournamentId = $pdo->lastInsertId();
                redirectWith("view.php?id=$tournamentId", 'success', 'Tạo giải đấu thành công!');
            } catch (Exception $e) {
                $error = 'Đã xảy ra lỗi. Vui lòng thử lại.';
            }
        }
    }
}

$pageTitle = 'Tạo giải đấu mới';
include '../includes/header.php';
?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/GamerWiki/index.php">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="index.php">Giải đấu</a></li>
            <li class="breadcrumb-item active">Tạo mới</li>
        </ol>
    </nav>
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0"><i class="fas fa-plus me-2"></i>Tạo giải đấu mới</h3>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <?php echo showError($error); ?>
                    <?php endif; ?>
                    
                    <form method="POST" action="" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên giải đấu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo isset($_POST['name']) ? e($_POST['name']) : ''; ?>" 
                                   required>
                            <div class="invalid-feedback">Vui lòng nhập tên giải đấu.</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="game" class="form-label">Game <span class="text-danger">*</span></label>
                                <select class="form-select" id="game" name="game" required>
                                    <option value="">Chọn game...</option>
                                    <option value="League of Legends">League of Legends</option>
                                    <option value="Dota 2">Dota 2</option>
                                    <option value="Valorant">Valorant</option>
                                    <option value="CS:GO">CS:GO</option>
                                    <option value="Overwatch">Overwatch</option>
                                    <option value="Mobile Legends">Mobile Legends</option>
                                    <option value="PUBG">PUBG</option>
                                </select>
                                <div class="invalid-feedback">Vui lòng chọn game.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label">Địa điểm</label>
                                <input type="text" class="form-control" id="location" name="location"
                                       value="<?php echo isset($_POST['location']) ? e($_POST['location']) : ''; ?>"
                                       placeholder="Seoul, Korea">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="start_date" class="form-label">Ngày bắt đầu</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                       value="<?php echo isset($_POST['start_date']) ? e($_POST['start_date']) : ''; ?>">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="end_date" class="form-label">Ngày kết thúc</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                       value="<?php echo isset($_POST['end_date']) ? e($_POST['end_date']) : ''; ?>">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="prize_pool" class="form-label">Giải thưởng ($)</label>
                                <input type="number" class="form-control" id="prize_pool" name="prize_pool"
                                       value="<?php echo isset($_POST['prize_pool']) ? e($_POST['prize_pool']) : ''; ?>"
                                       placeholder="1000000" step="0.01">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="5"><?php echo isset($_POST['description']) ? e($_POST['description']) : ''; ?></textarea>
                            <div class="form-text">Thông tin về giải đấu, format, số đội tham gia...</div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Tạo giải đấu
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
