<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireLogin();

$error = '';
$preselectedTeamId = $_GET['team_id'] ?? '';

// Lấy danh sách teams
$teams = [];
if (isAdmin()) {
    // Admin có thể thêm player vào bất kỳ team nào
    $teams = $pdo->query("SELECT id, name FROM teams ORDER BY name")->fetchAll();
} else {
    // User chỉ thêm vào team của mình
    $stmt = $pdo->prepare("SELECT id, name FROM teams WHERE created_by = ? ORDER BY name");
    $stmt->execute([$_SESSION['user_id']]);
    $teams = $stmt->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request.';
    } else {
        $team_id = $_POST['team_id'] ?? '';
        $nickname = cleanInput($_POST['nickname'] ?? '');
        $real_name = cleanInput($_POST['real_name'] ?? '');
        $role_position = cleanInput($_POST['role_position'] ?? '');
        $nationality = cleanInput($_POST['nationality'] ?? '');
        $birth_date = $_POST['birth_date'] ?? '';
        $photo_url = cleanInput($_POST['photo_url'] ?? '');
        $biography = cleanInput($_POST['biography'] ?? '');
        
        if (empty($nickname)) {
            $error = 'Vui lòng nhập nickname.';
        } else {
            // Kiểm tra quyền thêm vào team
            if ($team_id && !isAdmin()) {
                $stmt = $pdo->prepare("SELECT created_by FROM teams WHERE id = ?");
                $stmt->execute([$team_id]);
                $team = $stmt->fetch();
                if (!$team || $team['created_by'] != $_SESSION['user_id']) {
                    $error = 'Bạn không có quyền thêm tuyển thủ vào đội này.';
                }
            }
            
            if (!$error) {
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO players (team_id, nickname, real_name, role_position, nationality, birth_date, photo_url, biography)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    
                    $stmt->execute([
                        $team_id ?: null,
                        $nickname,
                        $real_name,
                        $role_position,
                        $nationality,
                        $birth_date ?: null,
                        $photo_url ?: 'https://via.placeholder.com/200',
                        $biography
                    ]);
                    
                    $playerId = $pdo->lastInsertId();
                    redirectWith("view.php?id=$playerId", 'success', 'Thêm tuyển thủ thành công!');
                } catch (Exception $e) {
                    $error = 'Đã xảy ra lỗi. Vui lòng thử lại.';
                }
            }
        }
    }
}

$pageTitle = 'Thêm tuyển thủ mới';
include '../includes/header.php';
?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/GamerWiki/index.php">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="index.php">Tuyển thủ</a></li>
            <li class="breadcrumb-item active">Thêm mới</li>
        </ol>
    </nav>
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0"><i class="fas fa-plus me-2"></i>Thêm tuyển thủ mới</h3>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <?php echo showError($error); ?>
                    <?php endif; ?>
                    
                    <?php if (empty($teams)): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Bạn cần tạo đội tuyển trước khi thêm tuyển thủ.
                            <a href="/GamerWiki/teams/create.php" class="alert-link">Tạo đội tuyển</a>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nickname" class="form-label">Nickname <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nickname" name="nickname" 
                                       value="<?php echo isset($_POST['nickname']) ? e($_POST['nickname']) : ''; ?>" 
                                       required>
                                <div class="invalid-feedback">Vui lòng nhập nickname.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="real_name" class="form-label">Tên thật</label>
                                <input type="text" class="form-control" id="real_name" name="real_name"
                                       value="<?php echo isset($_POST['real_name']) ? e($_POST['real_name']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="team_id" class="form-label">Đội tuyển</label>
                            <select class="form-select" id="team_id" name="team_id">
                                <option value="">Free Agent</option>
                                <?php foreach ($teams as $team): ?>
                                <option value="<?php echo $team['id']; ?>" 
                                    <?php echo ($preselectedTeamId == $team['id'] || (isset($_POST['team_id']) && $_POST['team_id'] == $team['id'])) ? 'selected' : ''; ?>>
                                    <?php echo e($team['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="role_position" class="form-label">Vị trí</label>
                                <input type="text" class="form-control" id="role_position" name="role_position"
                                       value="<?php echo isset($_POST['role_position']) ? e($_POST['role_position']) : ''; ?>"
                                       placeholder="Mid Lane, ADC, Support...">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="nationality" class="form-label">Quốc tịch</label>
                                <input type="text" class="form-control" id="nationality" name="nationality"
                                       value="<?php echo isset($_POST['nationality']) ? e($_POST['nationality']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="birth_date" class="form-label">Ngày sinh</label>
                            <input type="date" class="form-control" id="birth_date" name="birth_date"
                                   value="<?php echo isset($_POST['birth_date']) ? e($_POST['birth_date']) : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="photo_url" class="form-label">URL Ảnh</label>
                            <input type="url" class="form-control" id="photo_url" name="photo_url"
                                   value="<?php echo isset($_POST['photo_url']) ? e($_POST['photo_url']) : ''; ?>"
                                   placeholder="https://example.com/photo.jpg">
                            <div class="form-text">Để trống sẽ sử dụng ảnh mặc định.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="biography" class="form-label">Tiểu sử</label>
                            <textarea class="form-control" id="biography" name="biography" 
                                      rows="5"><?php echo isset($_POST['biography']) ? e($_POST['biography']) : ''; ?></textarea>
                            <div class="form-text">Thông tin về tuyển thủ, thành tích...</div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Thêm tuyển thủ
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
