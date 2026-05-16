<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/helpers.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 'U001';
}

$sessionUserId = paw_normalize_user_id($_SESSION['user_id']);
$targetUserId = isset($_GET['id']) ? paw_normalize_user_id($_GET['id']) : $sessionUserId;
if ($targetUserId === '') {
    $targetUserId = $sessionUserId;
}

$tab = isset($_GET['tab']) ? (string) $_GET['tab'] : 'posts';
if ($tab !== 'saved') {
    $tab = 'posts';
}

$isOwnProfile = $targetUserId === $sessionUserId;
if ($tab === 'saved' && !$isOwnProfile) {
    $tab = 'posts';
}

$userRow = null;
$gridPosts = [];
$dbError = null;
$rootDir = dirname(__DIR__);

try {
    $conn = paw_db();
    $targetEsc = mysqli_real_escape_string($conn, $targetUserId);

    $sqlUser = "
        SELECT u.tenDangNhap AS username, h.tenHienThi AS display_name, h.anhDaiDien AS avatar_file, h.moTa AS bio
        FROM NguoiDung u
        LEFT JOIN HoSo h ON h.maNguoiDung = u.maNguoiDung
        WHERE u.maNguoiDung = '{$targetEsc}'
        LIMIT 1
    ";
    $userResult = mysqli_query($conn, $sqlUser);
    if ($userResult && ($row = mysqli_fetch_assoc($userResult))) {
        $userRow = $row;
        mysqli_free_result($userResult);
    }

    if ($tab === 'saved' && $isOwnProfile) {
        $viewerEsc = mysqli_real_escape_string($conn, $sessionUserId);
        $sqlPosts = "
            SELECT
                b.maBaiDang AS id,
                (
                    SELECT pt.duongDan
                    FROM PhuongTien pt
                    WHERE pt.maBaiDang = b.maBaiDang AND pt.loaiPhuongTien = 'image'
                    ORDER BY pt.maPhuongTien
                    LIMIT 1
                ) AS post_file
            FROM PhanUng pu
            INNER JOIN BaiDang b ON b.maBaiDang = pu.maBaiDang
            WHERE pu.maNguoiDung = '{$viewerEsc}'
              AND pu.loaiPhanUng = 'luu'
              AND EXISTS (
                    SELECT 1 FROM PhuongTien pt
                    WHERE pt.maBaiDang = b.maBaiDang AND pt.loaiPhuongTien = 'image'
                      AND pt.duongDan IS NOT NULL AND pt.duongDan <> ''
              )
            ORDER BY b.maBaiDang DESC
        ";
    } else {
        $sqlPosts = "
            SELECT
                b.maBaiDang AS id,
                (
                    SELECT pt.duongDan
                    FROM PhuongTien pt
                    WHERE pt.maBaiDang = b.maBaiDang AND pt.loaiPhuongTien = 'image'
                    ORDER BY pt.maPhuongTien
                    LIMIT 1
                ) AS post_file
            FROM BaiDang b
            WHERE b.maNguoiDung = '{$targetEsc}'
              AND b.cheDoHienThi = 'cong_khai'
              AND EXISTS (
                    SELECT 1 FROM PhuongTien pt
                    WHERE pt.maBaiDang = b.maBaiDang AND pt.loaiPhuongTien = 'image'
                      AND pt.duongDan IS NOT NULL AND pt.duongDan <> ''
              )
            ORDER BY b.maBaiDang DESC
        ";
    }

    $postsResult = mysqli_query($conn, $sqlPosts);
    if ($postsResult) {
        while ($row = mysqli_fetch_assoc($postsResult)) {
            $src = paw_feed_post_src((string) ($row['post_file'] ?? ''), $rootDir);
            if ($src === '') {
                continue;
            }
            $row['image_src'] = '../' . ltrim($src, './');
            $gridPosts[] = $row;
        }
        mysqli_free_result($postsResult);
    }
} catch (Throwable $e) {
    $dbError = 'Không kết nối được CSDL.';
}

$isRoot = false;
$activeNav = 'profile';
$assetPrefix = '../';
$apiBase = '../';

$profileBase = 'profile.php?id=' . rawurlencode($targetUserId);
$avatarSrc = '../' . ltrim(
    paw_feed_avatar_src((string) ($userRow['avatar_file'] ?? ''), $rootDir),
    './'
);
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PawConnect - Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/styles.css">
  <link rel="stylesheet" href="../assets/css/profile.css">
</head>
<body data-api-base="<?= h($apiBase) ?>">
  <div class="profile-page container-fluid px-0">
    <div class="row g-0 min-vh-100">
      <?php require __DIR__ . '/../partials/sidebar.php'; ?>

      <main class="profile-main flex-grow-1">
        <div class="container py-4">
          <?php if ($dbError !== null): ?>
            <div class="alert alert-warning"><?= h($dbError) ?></div>
          <?php elseif ($userRow === null): ?>
            <div class="alert alert-warning">Không tìm thấy hồ sơ người dùng.</div>
          <?php else: ?>
            <section class="row align-items-center justify-content-center g-4 mb-4">
              <div class="col-12 col-lg-3 text-center">
                <div class="profile-avatar-box mx-auto">
                  <img src="<?= h($avatarSrc) ?>" alt="Avatar" class="profile-avatar">
                </div>
              </div>
              <div class="col-12 col-lg-7">
                <div class="profile-info text-center text-lg-start">
                  <h1 class="profile-username mb-1"><?= h((string) $userRow['username']) ?></h1>
                  <p class="profile-name mb-3"><?= h((string) $userRow['display_name']) ?></p>
                  <p class="profile-bio mb-0"><?= nl2br(h((string) ($userRow['bio'] ?? ''))) ?></p>
                </div>
              </div>
            </section>

            <?php if ($isOwnProfile): ?>
            <nav class="profile-tabs row g-0 mb-3" aria-label="Loại bài viết">
              <div class="col-6">
                <a href="<?= h($profileBase) ?>&tab=posts" class="profile-tab<?= $tab === 'posts' ? ' active' : '' ?>">Bài viết</a>
              </div>
              <div class="col-6">
                <a href="<?= h($profileBase) ?>&tab=saved" class="profile-tab<?= $tab === 'saved' ? ' active' : '' ?>">Đã lưu</a>
              </div>
            </nav>
            <?php endif; ?>

            <section class="row g-0 profile-posts">
              <?php foreach ($gridPosts as $p): ?>
                <div class="col-6 col-md-4 col-lg-3">
                  <div class="post-item">
                    <img src="<?= h((string) $p['image_src']) ?>" alt="Post">
                  </div>
                </div>
              <?php endforeach; ?>
            </section>

            <?php if (count($gridPosts) === 0): ?>
              <p class="text-secondary text-center mt-4">
                <?= $tab === 'saved' ? 'Chưa có bài đã lưu.' : 'Chưa có bài viết công khai.' ?>
              </p>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
