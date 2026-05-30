<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../models/post_module.php";
require_once "../modules/db_module.php";

// Nếu chưa đăng nhập, xuất XHTML báo lỗi
if (!isset($_SESSION['maNguoiDung'])) {
    echo '<div class="text-danger" style="font-size: 13px;">Vui lòng đăng nhập lại!</div>';
    exit();
}

$maNguoiDung = $_SESSION['maNguoiDung'];
$link = null;
taoKetNoi($link);

$postModel = new PostModel();
$thongTinHoSo = $postModel->getProfileByUserId($link, $maNguoiDung);

// Định nghĩa dữ liệu mặc định phòng hờ
$tenHienThi = "Người dùng PawConnect";
$anhDaiDien = "../assets/Profile/default_avatar.jpg"; 

if ($thongTinHoSo) {
    // Sử dụng đúng 2 Key chữ HOA (tenHienThi, anhDaiDien) đã debug ra
    if (!empty($thongTinHoSo['tenHienThi'])) {
        $tenHienThi = $thongTinHoSo['tenHienThi'];
    }
    if (!empty($thongTinHoSo['anhDaiDien'])) {
        $anhDaiDien = "../assets/uploads/" . $thongTinHoSo['anhDaiDien'];
    }
}
giaiPhongBoNho($link, null);

// XUẤT KHỐI XHTML GIAO DIỆN PROFILE (Sau này muốn đổi giao diện chỉ cần sửa ở đây)
?>
<div class="d-flex align-items-center gap-3">
    <img src="<?php echo $anhDaiDien; ?>" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1px solid #efefef;" alt="Avatar"/>
    <div class="d-flex flex-column">
        <span class="fw-bold text-dark" style="font-size: 14px; line-height: 1.2;"><?php echo $tenHienThi; ?></span>
        <span class="text-muted" style="font-size: 12px; margin-top: 2px;">Tác giả bài viết</span>
    </div>
</div>
<?php
exit();
?>
