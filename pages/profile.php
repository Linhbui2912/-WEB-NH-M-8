<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/helpers.php';

// U002 = bob — tài khoản demo mặc định đang "đăng nhập"
$viewerId = $_GET['viewer'] ?? 'U002';
$profileUserId = $_GET['id'] ?? null;
$username = $_GET['user'] ?? 'bob';

$error = null;
$profile = null;
$posts = [];
$postCount = 0;
$followerCount = 0;
$followingCount = 0;
$isFollowing = false;
$isOwnProfile = false;

try {
    $pdo = getDB();

    if ($profileUserId) {
        $stmt = $pdo->prepare('
            SELECT nd.maNguoiDung, nd.tenDangNhap, hs.tenHienThi, hs.anhDaiDien, hs.moTa
            FROM NguoiDung nd
            INNER JOIN HoSo hs ON hs.maNguoiDung = nd.maNguoiDung
            WHERE nd.maNguoiDung = :id AND nd.trangThai = \'hoat_dong\'
            LIMIT 1
        ');
        $stmt->execute(['id' => $profileUserId]);
    } else {
        $stmt = $pdo->prepare('
            SELECT nd.maNguoiDung, nd.tenDangNhap, hs.tenHienThi, hs.anhDaiDien, hs.moTa
            FROM NguoiDung nd
            INNER JOIN HoSo hs ON hs.maNguoiDung = nd.maNguoiDung
            WHERE nd.tenDangNhap = :username AND nd.trangThai = \'hoat_dong\'
            LIMIT 1
        ');
        $stmt->execute(['username' => $username]);
    }

    $profile = $stmt->fetch();

    if (!$profile) {
        $error = 'Không tìm thấy hồ sơ người dùng.';
    } else {
        $profileUserId = $profile['maNguoiDung'];
        $isOwnProfile = $viewerId === $profileUserId;

        $stmt = $pdo->prepare('SELECT COUNT(*) FROM BaiDang WHERE maNguoiDung = :id');
        $stmt->execute(['id' => $profileUserId]);
        $postCount = (int) $stmt->fetchColumn();

        $stmt = $pdo->prepare('SELECT COUNT(*) FROM TheoDoi WHERE maNguoiDuocTheoDoi = :id');
        $stmt->execute(['id' => $profileUserId]);
        $followerCount = (int) $stmt->fetchColumn();

        $stmt = $pdo->prepare('SELECT COUNT(*) FROM TheoDoi WHERE maNguoiTheoDoi = :id');
        $stmt->execute(['id' => $profileUserId]);
        $followingCount = (int) $stmt->fetchColumn();

        if (!$isOwnProfile) {
            $stmt = $pdo->prepare('
                SELECT 1 FROM TheoDoi
                WHERE maNguoiTheoDoi = :viewer AND maNguoiDuocTheoDoi = :target
                LIMIT 1
            ');
            $stmt->execute(['viewer' => $viewerId, 'target' => $profileUserId]);
            $isFollowing = (bool) $stmt->fetchColumn();
        }

        $visibility = $isOwnProfile ? '' : 'AND bd.cheDoHienThi = \'cong_khai\'';
        $sql = "
            SELECT bd.maBaiDang, bd.noiDung, bd.thoiGianDang, pt.duongDan,
                   (SELECT COUNT(*) FROM BinhLuan bl WHERE bl.maBaiDang = bd.maBaiDang) AS soBinhLuan,
                   (SELECT COUNT(*) FROM PhanUng pu WHERE pu.maBaiDang = bd.maBaiDang) AS soPhanUng
            FROM BaiDang bd
            INNER JOIN PhuongTien pt ON pt.maBaiDang = bd.maBaiDang AND pt.loaiPhuongTien = 'image'
            WHERE bd.maNguoiDung = :id {$visibility}
            ORDER BY bd.thoiGianDang DESC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $profileUserId]);
        $posts = $stmt->fetchAll();
    }
} catch (PDOException $ex) {
    $error = 'Không kết nối được CSDL. Kiểm tra config/db.php và máy chủ MySQL.';
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PawConnect - <?= $profile ? e($profile['tenDangNhap']) : 'Profile' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/styles.css" />
    <link rel="stylesheet" href="../assets/css/profile.css" />
</head>
<body
    data-viewer-id="<?= e($viewerId) ?>"
    data-profile-id="<?= e($profile['maNguoiDung'] ?? '') ?>"
>
    <div class="profile-page container-fluid px-0">
        <div class="row g-0 min-vh-100 flex-nowrap">
            <aside class="left-sidebar col-2 col-md-1">
                <a class="sidebar-logo mb-4" href="../index.html" data-bs-toggle="tooltip" data-bs-title="Trang chủ PawConnect">
                    <img src="../assets/icon/PawsConnect.png" alt="PawConnect Logo" />
                </a>
                <nav class="sidebar-nav">
                    <a href="../index.html" class="nav-icon" data-bs-toggle="tooltip" data-bs-title="Home"><img src="../assets/icon/home_5973558.png" alt="Home" /></a>
                    <a href="search.html" class="nav-icon" data-bs-toggle="tooltip" data-bs-title="Search"><img src="../assets/icon/search.png" alt="Search" /></a>
                    <a href="discover.html" class="nav-icon" data-bs-toggle="tooltip" data-bs-title="Discover"><img src="../assets/icon/discovery_12028921.png" alt="Discover" /></a>
                    <a href="create-post.html" class="nav-icon" data-bs-toggle="tooltip" data-bs-title="Create New Post"><img src="../assets/icon/add.png" alt="Create" /></a>
                    <a href="profile.php?user=bob&viewer=U002" class="nav-icon active" data-bs-toggle="tooltip" data-bs-title="User Account"><img src="../assets/icon/user.png" alt="Account" /></a>
                </nav>
                <a href="settings.html" class="nav-icon settings-icon" data-bs-toggle="tooltip" data-bs-title="Settings">
                    <img src="../assets/icon/setting.png" alt="Settings" />
                </a>
            </aside>

            <main class="profile-main col col-md-11">
                <div class="container py-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= e($error) ?></div>
                    <?php elseif ($profile): ?>
                        <section class="row align-items-center justify-content-center g-4 mb-4">
                            <div class="col-12 col-lg-3 text-center">
                                <div class="profile-avatar-box mx-auto">
                                    <img src="<?= e(profileImageUrl($profile['anhDaiDien'])) ?>"
                                         alt="<?= e($profile['tenHienThi']) ?>"
                                         class="profile-avatar" />
                                </div>
                            </div>
                            <div class="col-12 col-lg-7">
                                <div class="profile-info text-center text-lg-start">
                                    <h1 class="profile-username mb-1"><?= e($profile['tenDangNhap']) ?></h1>
                                    <p class="profile-name mb-3"><?= e($profile['tenHienThi']) ?></p>
                                    <div class="row g-2 mb-3 justify-content-center justify-content-lg-start profile-stats">
                                        <div class="col-auto"><strong><?= $postCount ?></strong> bài viết</div>
                                        <div class="col-auto">
                                            <button type="button" class="profile-stat-btn" id="btnShowFollowers" data-follow-type="followers">
                                                <strong id="followerCount"><?= $followerCount ?></strong> người theo dõi
                                            </button>
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="profile-stat-btn" id="btnShowFollowing" data-follow-type="following">
                                                Đang theo dõi <strong id="followingCount"><?= $followingCount ?></strong>
                                            </button>
                                        </div>
                                    </div>
                                    <?php if ($profile['moTa']): ?>
                                        <p class="profile-bio mb-0"><?= nl2br(e($profile['moTa'])) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </section>

                        <?php if (!$isOwnProfile): ?>
                        <section class="row g-3 justify-content-center mb-4 profile-actions">
                            <div class="col-12 col-md-6 col-lg-5">
                                <button type="button"
                                        class="btn btn-follow w-100 <?= $isFollowing ? 'following' : '' ?>"
                                        id="btnFollow"
                                        data-target-id="<?= e($profileUserId) ?>"
                                        data-following="<?= $isFollowing ? '1' : '0' ?>">
                                    <?= $isFollowing ? 'Đang theo dõi' : 'Theo dõi' ?>
                                </button>
                            </div>
                            <div class="col-12 col-md-6 col-lg-5">
                                <button type="button" class="btn btn-pet w-100">Vuốt ve 🐾</button>
                            </div>
                        </section>
                        <?php endif; ?>

                        <section class="row g-2 profile-posts" id="profileGrid">
                            <?php if (empty($posts)): ?>
                                <div class="col-12 text-center text-muted py-5">
                                    <p class="mb-0">Chưa có bài đăng công khai nào.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($posts as $post): ?>
                                <div class="col-6 col-md-3 profile-post-col">
                                    <button type="button"
                                            class="post-item w-100 border-0 p-0"
                                            data-post-id="<?= e($post['maBaiDang']) ?>"
                                            aria-label="Xem bài đăng">
                                        <img src="<?= e(postImageUrl($post['duongDan'])) ?>"
                                             alt="<?= e($post['noiDung'] ?? 'Bài đăng') ?>" />
                                        <div class="post-item-overlay">
                                            <span><img src="../assets/icon/pawheart.png" alt="" /> <?= (int) $post['soPhanUng'] ?></span>
                                            <span><img src="../assets/icon/message.png" alt="" /> <?= (int) $post['soBinhLuan'] ?></span>
                                        </div>
                                    </button>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </section>

                        <div class="profile-switcher mt-4 text-center">
                            <p class="text-muted small mb-2">Xem profile demo (từ CSDL):</p>
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                <?php
                                $demoUsers = ['alice', 'bob', 'diana', 'eric', 'fiona'];
                                foreach ($demoUsers as $u):
                                    $active = ($profile['tenDangNhap'] === $u) ? 'active' : '';
                                ?>
                                <a href="profile.php?user=<?= e($u) ?>&viewer=U002"
                                   class="btn btn-sm btn-outline-secondary profile-demo-link <?= $active ?>"><?= e($u) ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal chi tiết bài đăng (Instagram style) -->
    <div class="modal fade" id="postDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl post-detail-dialog">
            <div class="modal-content post-detail-modal border-0">
                <button type="button" class="btn-close post-detail-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                <div class="row g-0 post-detail-inner">
                    <div class="col-lg-7 post-detail-media">
                        <img id="postDetailImage" src="" alt="Ảnh bài đăng" />
                    </div>
                    <div class="col-lg-5 post-detail-panel d-flex flex-column">
                        <header class="post-detail-header d-flex align-items-center gap-2">
                            <img id="postDetailAvatar" src="" alt="" class="post-detail-avatar" />
                            <div class="flex-grow-1 min-w-0">
                                <a href="#" id="postDetailUsername" class="post-detail-username text-decoration-none"></a>
                                <p id="postDetailTime" class="post-detail-time mb-0"></p>
                            </div>
                        </header>

                        <div class="post-detail-body flex-grow-1 overflow-auto" id="postDetailBody">
                            <p class="post-detail-caption mb-3" id="postDetailCaption"></p>
                            <ul class="list-unstyled post-detail-comments mb-0" id="postDetailComments"></ul>
                        </div>

                        <footer class="post-detail-footer">
                            <div class="post-detail-actions d-flex align-items-center gap-3 mb-2">
                                <button type="button" class="btn btn-link p-0 post-action-btn" id="btnLikePost" aria-label="Thích">
                                    <img src="../assets/icon/footprint.png" alt="Paw" width="28" height="28" />
                                </button>
                                <button type="button" class="btn btn-link p-0 post-action-btn" aria-label="Bình luận">
                                    <img src="../assets/icon/message.png" alt="Comment" width="26" height="26" />
                                </button>
                                <button type="button" class="btn btn-link p-0 post-action-btn" aria-label="Chia sẻ">
                                    <img src="../assets/icon/share.png" alt="Share" width="26" height="26" />
                                </button>
                            </div>
                            <p class="post-detail-likes mb-2" id="postDetailLikes"><strong>0</strong> lượt paw</p>
                            <form id="commentForm" class="post-detail-comment-form d-flex gap-2">
                                <input type="hidden" id="commentPostId" name="maBaiDang" value="" />
                                <input type="text"
                                       id="commentInput"
                                       class="form-control form-control-sm border-0 shadow-none"
                                       placeholder="Thêm bình luận..."
                                       autocomplete="off"
                                       maxlength="500" />
                                <button type="submit" class="btn btn-link post-comment-submit p-0" disabled>Đăng</button>
                            </form>
                        </footer>
                    </div>
                </div>
                <div id="postDetailLoading" class="post-detail-loading d-none">
                    <div class="spinner-border text-light" role="status"><span class="visually-hidden">Đang tải...</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal danh sách theo dõi -->
    <div class="modal fade" id="followListModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable follow-list-dialog">
            <div class="modal-content follow-list-modal border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title w-100 text-center" id="followListTitle">Người theo dõi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body p-0" id="followListBody">
                    <div class="follow-list-loading text-center py-4">
                        <div class="spinner-border spinner-border-sm text-secondary" role="status"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/profile.js"></script>
</body>
</html>
