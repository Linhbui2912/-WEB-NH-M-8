<?php
session_start();

// Bảo vệ trang chủ: Chặn tuyệt đối nếu chưa đăng nhập tài khoản
if (!isset($_SESSION['maNguoiDung'])) {
    header("Location: ../views/dangnhap.php?msg=login-required");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Babe Nuboli - Mạng xã hội Mẹ & Bé</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex">
        
        <?php include "left-side-menu.php"; ?>

        <div class="p-4 flex-grow-1" style="background-color: #f8f9fa;">
            <div class="container bg-white p-4 rounded shadow-sm" style="max-width: 700px;">
                <h3>Bảng tin Babe Nuboli</h3>
                <p>Nơi các mẹ bỉm sữa trao đổi kinh nghiệm nuôi dạy con và thanh lý đồ dùng...</p>
                <hr>
                </div>
        </div>

    </div>
</body>
</html>