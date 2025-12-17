<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Yêu cầu đăng nhập
requireLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
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
        
        // Validation
        if (empty($name) || empty($game)) {
            $error = 'Vui lòng nhập tên đội và game.';
        } else {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO teams (name, game, region, founded_date, logo_url, description, website, created_by)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $name,
                    $game,
                    $region,
                    $founded_date ?: null,
                    $logo_url ?: 'https://via.placeholder.com/150',
                    $description,
                    $website,
                    $_SESSION['user_id']
                ]);
                
                $teamId = $pdo->lastInsertId();
                redirectWith("view.php?id=$teamId", 'success', 'Tạo đội tuyển thành công!');
            } catch (Exception $e) {
                $error = 'Đã xảy ra lỗi. Vui lòng thử lại.';
            }
        }
    }
}

$pageTitle = 'Tạo đội tuyển mới';
include '../includes/header.php';
?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/GamerWiki/index.php">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="index.php">Đội tuyển</a></li>
            <li class="breadcrumb-item active">Tạo mới</li>
        </ol>
    </nav>
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0"><i class="fas fa-plus me-2"></i>Tạo đội tuyển mới</h3>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <?php echo showError($error); ?>
                    <?php endif; ?>
                    
                    <form method="POST" action="" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Tên đội <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo isset($_POST['name']) ? e($_POST['name']) : ''; ?>" 
                                       required>
                                <div class="invalid-feedback">Vui lòng nhập tên đội.</div>
                            </div>
                            
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
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="region" class="form-label">Khu vực</label>
                                <select class="form-select" id="region" name="region">
                                    <option value="">Chọn khu vực...</option>
                                    <option value="North America">North America</option>
                                    <option value="Europe">Europe</option>
                                    <option value="Korea">Korea</option>
                                    <option value="China">China</option>
                                    <option value="Southeast Asia">Southeast Asia</option>
                                    <option value="Vietnam">Vietnam</option>
                                    <option value="Brazil">Brazil</option>
                                    <option value="Latin America">Latin America</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="founded_date" class="form-label">Ngày thành lập</label>
                                <input type="date" class="form-control" id="founded_date" name="founded_date"
                                       value="<?php echo isset($_POST['founded_date']) ? e($_POST['founded_date']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="logo_url" class="form-label">URL Logo</label>
                            <input type="url" class="form-control" id="logo_url" name="logo_url"
                                   value="<?php echo isset($_POST['logo_url']) ? e($_POST['logo_url']) : ''; ?>"
                                   placeholder="https://example.com/logo.png">
                            <div class="form-text">Để trống sẽ sử dụng logo mặc định.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" class="form-control" id="website" name="website"
                                   value="<?php echo isset($_POST['website']) ? e($_POST['website']) : ''; ?>"
                                   placeholder="https://example.com">
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="5"><?php echo isset($_POST['description']) ? e($_POST['description']) : ''; ?></textarea>
                            <div class="form-text">Thông tin về đội tuyển, lịch sử, thành tích...</div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Tạo đội tuyển
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
