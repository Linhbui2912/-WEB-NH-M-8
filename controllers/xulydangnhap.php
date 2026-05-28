<?php
session_start();
require_once "../models/users_module.php";
require_once "../modules/db_module.php";
$link = null;

if (isset($_POST["username"]) && isset($_POST["password"])) {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);    
    taoKetNoi($link); 
    $us_module = new users_module();
    // Model trả về nguyên một Object NguoiDung (hoặc null nếu sai tài khoản)
    $user = $us_module->dangnhap($link, $username, $password); 

    if ($user !== null) {
        // Dùng phương thức Getter của Object $user để lưu vào Session hệ thống
        $_SESSION['tenDangNhap'] = $user->getTenDangNhap();
        $_SESSION['maNguoiDung'] = $user->getMaNguoiDung();
        $_SESSION['maQuyen']     = $user->getMaQuyen();
        giaiPhongBoNho($link, true);

        // KIỂM TRA QUYỀN ĐỂ ĐIỀU HƯỚNG
        
        if ($_SESSION['maQuyen'] == 1) { 
            header("Location: ../controllers/admin_user_controller.php");
        } else {
            header("Location: ../views/homepage.php");        
        }
        exit(); 
    } else {
        giaiPhongBoNho($link, true);
        header("Location: ../views/dangnhap.php?msg=login-fail");
        exit();
    }
} else {
    giaiPhongBoNho($link, true);
    header("Location: ../views/dangnhap.php");
    exit();
}
?>