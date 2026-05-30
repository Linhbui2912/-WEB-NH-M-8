<?php declare(strict_types=1); session_start(); if (!isset($_SESSION['maNguoiDung'])) { header('Location: dangnhap.php?msg=login-required'); exit(); } 
require_once __DIR__ . '/../models/homepage_helpers.php'; 
require_once __DIR__ . '/../controllers/HomepageController.php'; 
$viewerId = (string) $_SESSION['maNguoiDung']; 
$posts = HomepageController::getFeed($viewerId); 
$activeNav = 'home'; $assetPrefix = '../'; 
$apiControllers = '../controllers/'; 
?> 
<!doctype html> 
<html lang="vi"> 
    <head> 
        <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>PawConnect - Home</title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link rel="stylesheet" href="<?= hp_h($assetPrefix) ?>assets/css/homepage.css"> 
</head> 
<body data-api-controllers="<?= hp_h($apiControllers) ?>" data-viewer-id="<?= hp_h($viewerId) ?>"> 
    <div class="container-fluid px-0 app-shell"> 
        <div class="row g-0"> <?php require __DIR__ . '/partials/homepage/sidebar.php'; ?> 
        <main class="feed-wrapper col"> 
            <section class="feed-column"> <?php if (count($posts) === 0): ?> 
                <p class="text-secondary text-center mt-4">Chưa có bài đăng nào trên bảng tin.</p> 
                <?php else: ?> 
                    <?php foreach ($posts as $post): ?> 
                        <?php require __DIR__ . '/partials/homepage/post_card.php'; ?> 
                        <?php endforeach; ?> <?php endif; ?> </section> </main> </div> </div> 
                        <?php require __DIR__ . '/partials/homepage/post_detail_modal.php'; ?> 
                        <?php require __DIR__ . '/partials/homepage/report_modal.php'; ?> 
                        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> 
                        <script src="<?= hp_h($assetPrefix) ?>assets/js/homepage.js"></script> 
</body> 
</html>