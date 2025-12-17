<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="/index.php">
            <i class="bi bi-trophy-fill"></i> GamerWiki
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/index.php">Trang chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/doi_tuyen.php">Đội tuyển</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/tuyen_thu.php">Tuyển thủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/giai_dau.php">Giải đấu</a>
                </li>
                <?php if (kiem_tra_admin()): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-gear-fill"></i> Quản trị
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/admin/index.php">Dashboard</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/admin/doi_tuyen.php">Quản lý đội tuyển</a></li>
                        <li><a class="dropdown-item" href="/admin/tuyen_thu.php">Quản lý tuyển thủ</a></li>
                        <li><a class="dropdown-item" href="/admin/giai_dau.php">Quản lý giải đấu</a></li>
                        <li><a class="dropdown-item" href="/admin/tai_khoan.php">Quản lý tài khoản</a></li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if (kiem_tra_dang_nhap()): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> <?php echo escape_html($_SESSION['ten_dang_nhap']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="/auth/logout.php">Đăng xuất</a></li>
                    </ul>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="/auth/login.php">Đăng nhập</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/auth/register.php">Đăng ký</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
