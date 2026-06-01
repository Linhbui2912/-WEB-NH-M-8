<div class="admin-sidebar">
    <div class="sidebar-logo">
        <h2>🐾 PawConnect</h2> 
    </div>

    <div class="sidebar-nav">
        <a href="../views/quanly_baidang.php" class="nav-item">
            <img src="../assets/icon/icon_admin_posts.png" alt="Posts">
            <span>Admin/Posts</span>
        </a>
        <a href="../controllers/admin_user_controller.php" class="nav-item">
            <img src="../assets/icon/icon_admin_users.png" alt="Users">
            <span>Admin/Users</span>
        </a>

    </div>

    <div class="sidebar-bottom mt-auto">
        <!-- <a href="../dangxuat.php" class="nav-item logout-btn">
            <span>Đăng xuất</span>
        </a> -->

<!--THAY THẾ PHẦN ĐĂNG XUẤT MỚI-->
        <a href="#" class="nav-item logout-btn" id="logoutBtn">
    <span>Đăng xuất</span>
</a>

<!-- Hộp thoại xác nhận -->
<div id="logoutOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;padding:32px 28px;max-width:360px;width:90%;text-align:center;box-shadow:0 8px 32px rgba(0,0,0,.2);">
        <p style="font-size:16px;font-weight:600;margin-bottom:24px;color:#333">
            Bạn có chắc chắn muốn đăng xuất?
        </p>
        <div style="display:flex;gap:12px;justify-content:center;">
            <a href="../controllers/xulydangxuat.php"
               style="background:#d9534f;color:#fff;border:none;padding:10px 28px;border-radius:8px;font-weight:600;font-size:15px;text-decoration:none;cursor:pointer;">
                Có
            </a>
            <button id="logoutCancel"
                    style="background:#e9ecef;color:#333;border:none;padding:10px 28px;border-radius:8px;font-weight:600;font-size:15px;cursor:pointer;">
                Không
            </button>
        </div>
    </div>
</div>

<script>
document.getElementById('logoutBtn').addEventListener('click', e => {
    e.preventDefault();
    document.getElementById('logoutOverlay').style.display = 'flex';
});
document.getElementById('logoutCancel').addEventListener('click', () => {
    document.getElementById('logoutOverlay').style.display = 'none';
});
// Click ngoài overlay để đóng
document.getElementById('logoutOverlay').addEventListener('click', e => {
    if (e.target === document.getElementById('logoutOverlay')) {
        document.getElementById('logoutOverlay').style.display = 'none';
    }
});
</script>
    </div>
</div>
