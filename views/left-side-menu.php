<div class="d-flex flex-column flex-shrink-0 p-3 bg-light" style="width: 260px; min-height: 100vh;">
    <a href="homepage.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
        <span class="fs-4 fw-bold text-primary">Babe Nuboli</span>
    </a>
    <hr>
    
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="homepage.php" class="nav-link link-dark d-flex align-items-center gap-2">
                <i class="bi bi-house"></i> Bảng tin / Trang chủ
            </a>
        </li>
        <li class="nav-item">
            <a href="profile.php" class="nav-link link-dark d-flex align-items-center gap-2">
                <i class="bi bi-person-circle"></i> Trang cá nhân
            </a>
        </li>
        <li class="nav-item">
            <a href="tinnhan.php" class="nav-link link-dark d-flex align-items-center gap-2">
                <i class="bi bi-chat-dots"></i> Nhắn tin
            </a>
        </li>

        <?php if (isset($_SESSION['maQuyen']) && $_SESSION['maQuyen'] == 2): ?>
            <li class="nav-item">
                <a href="giohang.php" class="nav-link link-dark d-flex align-items-center gap-2">
                    <i class="bi bi-cart"></i> Giỏ hàng của mẹ
                </a>
            </li>
        <?php endif; ?>

        <?php if (isset($_SESSION['maQuyen']) && $_SESSION['maQuyen'] == 1): ?>
            <hr>
            <div class="text-muted small fw-bold px-3 mb-2 text-uppercase">Quản trị hệ thống</div>
            
            <li class="nav-item">
                <a href="quanly_nguoidung.php" class="nav-link link-dark d-flex align-items-center gap-2">
                    <i class="bi bi-people"></i> Quản lý thành viên
                </a>
            </li>
            <li class="nav-item">
                <a href="quanly_baidang.php" class="nav-link link-dark d-flex align-items-center gap-2">
                    <i class="bi bi-shield-check"></i> Kiểm duyệt bài đăng
                </a>
            </li>
        <?php endif; ?>
    </ul>
    
    <hr>
    <div class="px-2">
        <span>Chào, <strong><?php echo isset($_SESSION['tenDangNhap']) ? htmlspecialchars($_SESSION['tenDangNhap']) : 'Khách'; ?></strong></span>
        <?php if(isset($_SESSION['maQuyen']) && $_SESSION['maQuyen'] == 1): ?>
            <span class="badge bg-danger d-block mt-1 text-center">Tài khoản Admin</span>
        <?php endif; ?>
    </div>
</div>