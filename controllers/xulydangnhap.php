<?php
session_start();
require_once "../models/users_module.php";
require_once "../modules/db_module.php";
$link = null;

if (isset($_POST["username"]) && isset($_POST["password"])) {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);    
    taoKetNoi($link); 

    // --- CHỐT CHẶN: KIỂM TRA TRẠNG THÁI TÀI KHOẢN ---
    $sql_check = "SELECT trangThai FROM nguoidung WHERE tenDangNhap = ?";
    $stmt_check = mysqli_prepare($link, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "s", $username);
    mysqli_stmt_execute($stmt_check);
    $res_check = mysqli_stmt_get_result($stmt_check);
    
    if ($row_check = mysqli_fetch_assoc($res_check)) {
        if ($row_check['trangThai'] === 'bi_khoa') {
            giaiPhongBoNho($link, true);
            // Nếu bị khóa, đá thẳng về trang đăng nhập kèm thông báo "locked"
            header("Location: ../views/dangnhap.php?msg=locked");
            exit();
        }
    }
    // --------------------------------------------------

    $us_module = new users_module();
    $user = $us_module->dangnhap($link, $username, $password); 

    if ($user !== null) {
        $_SESSION['tenDangNhap'] = $user->getTenDangNhap();
        $_SESSION['maNguoiDung'] = $user->getMaNguoiDung();
        $_SESSION['maQuyen']     = $user->getMaQuyen();
        giaiPhongBoNho($link, true);

        // Phân quyền điều hướng
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
