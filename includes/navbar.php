<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo url('index.php'); ?>">
            <i class="bi bi-trophy-fill"></i> GamerWiki
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo url('index.php'); ?>">Trang chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo url('pages/doi_tuyen.php'); ?>">Đội tuyển</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo url('pages/tuyen_thu.php'); ?>">Tuyển thủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo url('pages/giai_dau.php'); ?>">Giải đấu</a>
                </li>
                <?php if (kiem_tra_admin() || kiem_tra_customer()): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-gear-fill"></i> 
                        <?php echo kiem_tra_admin() ? 'Quản trị' : 'Quản lý'; ?>
                    </a>
                    <ul class="dropdown-menu">
                        <?php if (kiem_tra_admin()): ?>
                            <li><a class="dropdown-item" href="<?php echo url('admin/index.php'); ?>">Dashboard</a></li>
                            <li><hr class="dropdown-divider"></li>
                        <?php endif; ?>
                        <li><a class="dropdown-item" href="<?php echo url('admin/doi_tuyen.php'); ?>">Quản lý đội tuyển</a></li>
                        <li><a class="dropdown-item" href="<?php echo url('admin/tuyen_thu.php'); ?>">Quản lý tuyển thủ</a></li>
                        <li><a class="dropdown-item" href="<?php echo url('admin/giai_dau.php'); ?>">Quản lý giải đấu</a></li>
                        <?php if (kiem_tra_admin()): ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo url('admin/tai_khoan.php'); ?>">Quản lý tài khoản</a></li>
                        <?php endif; ?>
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
                        <li><a class="dropdown-item" href="<?php echo url('auth/logout.php'); ?>">Đăng xuất</a></li>
                    </ul>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo url('auth/login.php'); ?>">Đăng nhập</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo url('auth/register.php'); ?>">Đăng ký</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
