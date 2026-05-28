<?php
// controllers/admin_user_controller.php

session_start();
require_once __DIR__ . '/../modules/db_module.php';
require_once __DIR__ . '/../models/admin_user_model.php';

// 1. Kiểm tra quyền Admin (maQuyen = 1)
if (!isset($_SESSION['maQuyen']) || $_SESSION['maQuyen'] != 1) {
    header("Location: ../views/dangnhap.php");
    exit();
}

$link = null;
taoKetNoi($link);
$adminModel = new admin_user_model();

// 2. Xử lý khi Admin bấm nút Khóa / Mở khóa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
    $maNguoiDung = trim($_POST['maNguoiDung']);
    $trangThaiHienTai = trim($_POST['trangThaiHienTai']);
    
    // Đảo trạng thái: Nếu đang 'hoat_dong' thì chuyển thành 'bi_khoa' và ngược lại
    $trangThaiMoi = ($trangThaiHienTai === 'hoat_dong') ? 'bi_khoa' : 'hoat_dong';
    
    $adminModel->toggleStatus($link, $maNguoiDung, $trangThaiMoi);
    
    // Reset lại trang để tải dữ liệu mới
    header("Location: admin_user_controller.php");
    exit();
}

// 3. Lấy dữ liệu và format ngày tháng (d/m/Y)
$danhSachNguoiDung = $adminModel->getReportedUsers($link);

foreach ($danhSachNguoiDung as &$user) {
    if(!empty($user['ngayBaoCao'])) {
        $user['ngayBaoCao'] = date('d/m/Y', strtotime($user['ngayBaoCao']));
    }
}

// 4. Đổ ra giao diện View
require_once __DIR__ . '/../views/quanly_nguoidung.php';

giaiPhongBoNho($link, true);
?>