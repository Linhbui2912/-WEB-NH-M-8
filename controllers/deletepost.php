<?php
declare(strict_types=1);

require_once "../modules/db_module.php"; 
require_once "../models/post_module.php"; 

$link = null;
taoKetNoi($link);

// 3. Hứng mã bài đăng từ Form POST ẩn truyền sang
$maBaiDang = $_POST['maBaiDang'] ?? null;

if ($maBaiDang && $link) {
    $postModel = new PostModel();

    // Xóa dữ liệu trong bảng Phương Tiện trước bằng khóa ngoại bài đăng
    $postModel->deleteMedia($link, $maBaiDang);

    // Xóa dữ liệu trong bảng Bài Đăng
    $isDeleted = $postModel->deletePost($link, $maBaiDang);

    giaiPhongBoNho($link, null);   

    // Điều hướng trang kèm theo thông báo trạng thái 
    if ($isDeleted) {
        header('Location: ../views/profile.php?msg=delete-success');
    } else {
        header('Location: ../views/profile.php?msg=delete-error');
    }
    exit;
} else {
    // Nếu không có ID hợp lệ, điều hướng về trang cá nhân
    header('Location: ../views/profile.php');
    exit;
}