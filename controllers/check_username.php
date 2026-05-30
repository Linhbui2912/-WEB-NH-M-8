<?php
declare(strict_types=1);
require_once "../models/users_module.php";
require_once "../modules/db_module.php";
$username = $_GET['username'] ?? null;

// Khai báo kiểu nội dung trả về là XHTML để đồng bộ với cấu trúc toàn trang
header('Content-Type: text/html; charset=UTF-8');

if ($username) {
   $link=null;
    taoKetNoi($link);
    $userBus = new users_module();    
    // Kiểm tra sự tồn tại của tên tài khoản trong cơ sở dữ liệu
    if ($userBus->existsUserName($link, trim($username))) {
        // Trả về nguyên khối giao diện XHTML báo lỗi 
        echo '<span class="text-danger small"><i class="bi bi-exclamation-circle-fill me-1"></i> Tên tài khoản này đã có người sử dụng!</span>';
    } else {
        // Trả về rỗng nếu tài khoản hợp lệ 
        echo '';
    }
    
    giaiPhongBoNho($link,true);
}
exit();
?>