<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireLogin();

$playerId = $_GET['id'] ?? 0;

// Lấy thông tin player
$stmt = $pdo->prepare("SELECT * FROM players WHERE id = ?");
$stmt->execute([$playerId]);
$player = $stmt->fetch();

if (!$player) {
    redirectWith('index.php', 'error', 'Không tìm thấy tuyển thủ.');
}

// Kiểm tra quyền chỉnh sửa
$canEdit = false;
if (isAdmin()) {
    $canEdit = true;
} elseif ($player['team_id']) {
    $stmtTeam = $pdo->prepare("SELECT created_by FROM teams WHERE id = ?");
    $stmtTeam->execute([$player['team_id']]);
    $team = $stmtTeam->fetch();
    if ($team && $team['created_by'] == $_SESSION['user_id']) {
        $canEdit = true;
    }
}

if (!$canEdit) {
    redirectWith('view.php?id=' . $playerId, 'error', 'Bạn không có quyền chỉnh sửa tuyển thủ này.');
}

// Lấy danh sách teams
$teams = [];
if (isAdmin()) {
    $teams = $pdo->query("SELECT id, name FROM teams ORDER BY name")->fetchAll();
} else {
    $stmt = $pdo->prepare("SELECT id, name FROM teams WHERE created_by = ? ORDER BY name");
    $stmt->execute([$_SESSION['user_id']]);
    $teams = $stmt->fetchAll();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        // Xóa player (chỉ admin hoặc owner)
        try {
            $stmt = $pdo->prepare("DELETE FROM players WHERE id = ?");
            $stmt->execute([$playerId]);
            redirectWith('index.php', 'success', 'Đã xóa tuyển thủ.');
        } catch (Exception $e) {
            $error = 'Không thể xóa tuyển thủ.';
        }
    } else {
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
                try {
                    $stmt = $pdo->prepare("
                        UPDATE players 
                        SET team_id = ?, nickname = ?, real_name = ?, role_position = ?, 
                            nationality = ?, birth_date = ?, photo_url = ?, biography = ?
                        WHERE id = ?
                    ");
                    
                    $stmt->execute([
                        $team_id ?: null,
                        $nickname,
                        $real_name,
                        $role_position,
                        $nationality,
                        $birth_date ?: null,
                        $photo_url ?: 'https://via.placeholder.com/200',
                        $biography,
                        $playerId
                    ]);
                    
                    $success = 'Cập nhật thành công!';
                    
                    // Refresh player data
                    $stmt = $pdo->prepare("SELECT * FROM players WHERE id = ?");
                    $stmt->execute([$playerId]);
                    $player = $stmt->fetch();
                } catch (Exception $e) {
                    $error = 'Đã xảy ra lỗi. Vui lòng thử lại.';
                }
            }
        }
    }
}

$pageTitle = 'Chỉnh sửa - ' . $player['nickname'];
include '../includes/header.php';
?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/GamerWiki/index.php">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="index.php">Tuyển thủ</a></li>
            <li class="breadcrumb-item"><a href="view.php?id=<?php echo $player['id']; ?>"><?php echo e($player['nickname']); ?></a></li>
            <li class="breadcrumb-item active">Chỉnh sửa</li>
        </ol>
    </nav>
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0"><i class="fas fa-edit me-2"></i>Chỉnh sửa tuyển thủ</h3>
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
                                <label for="nickname" class="form-label">Nickname <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nickname" name="nickname" 
                                       value="<?php echo e($player['nickname']); ?>" required>
                                <div class="invalid-feedback">Vui lòng nhập nickname.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="real_name" class="form-label">Tên thật</label>
                                <input type="text" class="form-control" id="real_name" name="real_name"
                                       value="<?php echo e($player['real_name']); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="team_id" class="form-label">Đội tuyển</label>
                            <select class="form-select" id="team_id" name="team_id">
                                <option value="">Free Agent</option>
                                <?php foreach ($teams as $team): ?>
                                <option value="<?php echo $team['id']; ?>" <?php echo $player['team_id'] == $team['id'] ? 'selected' : ''; ?>>
                                    <?php echo e($team['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="role_position" class="form-label">Vị trí</label>
                                <input type="text" class="form-control" id="role_position" name="role_position"
                                       value="<?php echo e($player['role_position']); ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="nationality" class="form-label">Quốc tịch</label>
                                <input type="text" class="form-control" id="nationality" name="nationality"
                                       value="<?php echo e($player['nationality']); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="birth_date" class="form-label">Ngày sinh</label>
                            <input type="date" class="form-control" id="birth_date" name="birth_date"
                                   value="<?php echo e($player['birth_date']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="photo_url" class="form-label">URL Ảnh</label>
                            <input type="url" class="form-control" id="photo_url" name="photo_url"
                                   value="<?php echo e($player['photo_url']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="biography" class="form-label">Tiểu sử</label>
                            <textarea class="form-control" id="biography" name="biography" 
                                      rows="5"><?php echo e($player['biography']); ?></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Lưu thay đổi
                                </button>
                                <a href="view.php?id=<?php echo $player['id']; ?>" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Hủy
                                </a>
                            </div>
                            
                            <button type="submit" name="delete" class="btn btn-danger" 
                                    data-confirm-delete="Bạn có chắc muốn xóa tuyển thủ này?">
                                <i class="fas fa-trash me-2"></i>Xóa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
