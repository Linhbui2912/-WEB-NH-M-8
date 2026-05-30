<?php
declare(strict_types=1);
session_start();
// 1. Nhúng các module kết nối DB và xử lý user dữ liệu
require_once "../models/users_module.php";
require_once "../modules/db_module.php";
// 2. Lấy dữ liệu gửi lên từ form Đăng ký bằng phương thức POST
$profilename = $_POST['fullname'] ?? null;   // Name của ô "Tên hiển thị" 
$username    = $_POST['username'] ?? null;   // Name của ô "Tên tài khoản" 
$password    = $_POST['password'] ?? null;   // Name của ô "Mật khẩu"
$rePassword  = $_POST['re-password'] ?? null; // Name của ô "Xác nhận mật khẩu" 

// 3. Kiểm tra xem người dùng có nhập đầy đủ các trường bắt buộc không
if ($profilename && $username && $password && $rePassword) {    
    $link=null;
    taoKetNoi($link); 
    $userBus = new users_module();
    // Kiểm tra xem 2 mật khẩu nhập vào có khớp nhau hoàn toàn không 
    if ($password !== $rePassword) {  
        giaiPhongBoNho($link, true);      
        header("Location: ../views/dangky.php?msg=password-mismatch");
        exit();
    }
    //  Kiểm tra xem Tên tài khoản (Username) đã có ai đăng ký chưa
    if ($userBus->existsUserName($link, $username)) {
        giaiPhongBoNho($link,true);
        header("Location: ../views/dangky.php?msg=register-fail"); // Tài khoản đã tồn tại
        exit();
    }
    // Lấy mã người dùng nhập và chuyển về chữ thường
    $user_captcha = isset($_POST['txt_captcha']) ? strtolower(trim($_POST['txt_captcha'])) : '';
    // Lấy mã gốc lưu trong session
    $session_captcha = isset($_SESSION['captcha_code']) ? $_SESSION['captcha_code'] : '';

    // Kiểm tra xem khớp nhau không
    if ($user_captcha !== $session_captcha || empty($session_captcha)) {
        // Nếu sai, xóa session captcha cũ đi và chuyển hướng quay về trang đăng ký kèm thông báo lỗi
        unset($_SESSION['captcha_code']);
        header("Location: ../views/dangky.php?msg=captcha-failed"); 
        exit();
    }
    // Tiến hành gọi hàm đăng ký tài khoản mới
    $isSuccess = $userBus->dangky($link, $profilename, $username, $password);    
    // Giải phóng kết nối sau khi thực thi xong
    giaiPhongBoNho($link,true);
    if ($isSuccess) {
        // Đăng ký thành công, đá người dùng về trang Đăng nhập và báo vui lòng đăng nhập
        header("Location: ../views/dangnhap.php?msg=login-required"); 
    } else {
        // Lỗi hệ thống không chèn được
        header("Location: ../views/dangky.php?msg=system-error");
    }
    exit();
} else {    
    header("Location: ../views/dangky.php");
    exit();
}
?>