<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/helpers.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 'U001';
}

$viewerId = (string) $_SESSION['user_id'];
$posts = [];
$rootDir = dirname(__DIR__);

try {
    $conn = paw_db();
    $viewerEsc = mysqli_real_escape_string($conn, $viewerId);

    $sql = "
    SELECT
        b.maBaiDang AS id,
        b.maNguoiDung AS user_id,
        (
            SELECT pt.duongDan
            FROM PhuongTien pt
            WHERE pt.maBaiDang = b.maBaiDang AND pt.loaiPhuongTien = 'image'
            ORDER BY pt.maPhuongTien
            LIMIT 1
        ) AS post_file,
        h.anhDaiDien AS avatar_file,
        b.noiDung AS caption,
        u.tenDangNhap AS username,
        EXISTS(
            SELECT 1 FROM PhanUng pu
            WHERE pu.maBaiDang = b.maBaiDang
              AND pu.maNguoiDung = '{$viewerEsc}'
              AND pu.loaiPhanUng IN ('thich', 'yeu_thich')
        ) AS liked,
        EXISTS(
            SELECT 1 FROM PhanUng pu
            WHERE pu.maBaiDang = b.maBaiDang
              AND pu.maNguoiDung = '{$viewerEsc}'
              AND pu.loaiPhanUng = 'luu'
        ) AS saved
    FROM BaiDang b
    INNER JOIN NguoiDung u ON b.maNguoiDung = u.maNguoiDung
    LEFT JOIN HoSo h ON h.maNguoiDung = b.maNguoiDung
    WHERE b.cheDoHienThi = 'cong_khai'
      AND u.tenDangNhap <> 'alice'
      AND EXISTS (
            SELECT 1 FROM PhuongTien pt
            WHERE pt.maBaiDang = b.maBaiDang AND pt.loaiPhuongTien = 'image'
              AND pt.duongDan IS NOT NULL AND pt.duongDan <> ''
      )
    ORDER BY b.maBaiDang DESC
    ";

    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            if (paw_feed_post_src((string) ($row['post_file'] ?? ''), $rootDir) === '') {
                continue;
            }
            $posts[] = $row;
        }
        mysqli_free_result($result);
    }
} catch (Throwable $e) {
    $posts = [];
}

$isRoot = false;
$activeNav = 'home';
$assetPrefix = '../';
$apiBase = '../';
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PawConnect - Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= h($assetPrefix) ?>assets/css/style_homepage.css">
</head>
<body data-api-base="<?= h($apiBase) ?>">
  <div class="container-fluid px-0">
    <div class="row g-0">
      <?php require __DIR__ . '/../partials/sidebar.php'; ?>

      <main class="feed-wrapper col">
        <section class="feed-column">
          <?php foreach ($posts as $post): ?>
            <?php require __DIR__ . '/../partials/post_card.php'; ?>
          <?php endforeach; ?>
        </section>
      </main>
    </div>
  </div>

  <?php require __DIR__ . '/../partials/comments_modal.php'; ?>
    <?php
                      if (isset($_GET['msg'])) {
                          if ($_GET['msg'] == "done") {
                        echo '<div style="position: fixed; top: 20px; right: 20px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 15px;">
                        <b>Thành công!</b> Bài viết của bạn đã được đăng.
                        </div>';
                      } elseif ($_GET['msg'] == "error") {
                      echo '<div style="position: fixed; top: 20px; right: 20px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; margin-bottom: 15px;">
                      <b>Lỗi!</b> Không thể lưu bài đăng, vui lòng thử lại.
                      </div>';
                    }
                    }
    ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= h($assetPrefix) ?>assets/js/homepage_main.js"></script>
</body>
</html>
