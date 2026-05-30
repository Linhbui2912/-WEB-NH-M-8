<?php

declare(strict_types=1);

/** @var string|null $error */
/** @var array<string,mixed>|null $profile */
/** @var list<array<string,mixed>> $posts */
/** @var int $postCount */
/** @var int $followerCount */
/** @var int $followingCount */
/** @var bool $isFollowing */
/** @var bool $isOwnProfile */
/** @var string $viewerIdForView */
/** @var string $activeNav */

$profileTitle = $profile ? (string) $profile['tenDangNhap'] : 'Profile';
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PawConnect - <?= h($profileTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= h(asset_url('css/styles_pro.css')) ?>" />
    <link rel="stylesheet" href="<?= h(asset_url('css/profile.css')) ?>" />
    <link rel="stylesheet" href="<?= h(asset_url('css/profile_report.css')) ?>" />
</head>
<body
    data-viewer-id="<?= h($viewerIdForView) ?>"
    data-profile-id="<?= h($profile['maNguoiDung'] ?? '') ?>"
    data-is-own-profile="<?= $isOwnProfile ? '1' : '0' ?>"
    data-api-base="../controllers/"
>
    <div class="profile-page container-fluid px-0">
        <div class="row g-0 min-vh-100 flex-nowrap">
            <?php require __DIR__ . '/partials/profile_sidebar.php'; ?>

            <main class="profile-main col col-md-11">
                <div class="container py-4">
                    <?php if (!empty($_GET['msg']) && $_GET['msg'] === 'profile-saved'): ?>
                        <div class="alert alert-success">Đã lưu thay đổi hồ sơ.</div>
                    <?php elseif (!empty($_GET['msg']) && $_GET['msg'] === 'profile-error'): ?>
                        <div class="alert alert-danger">Không lưu được hồ sơ. Thử lại.</div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= h($error) ?></div>
                    <?php elseif ($profile): ?>
                        <section class="mb-4 profile-header-section position-relative">
                            <?php if (!$isOwnProfile): ?>
                            <button type="button"
                                    class="btn btn-link profile-report-account-btn p-0 position-absolute top-0 end-0"
                                    id="btnReportAccount"
                                    data-user-id="<?= h($profile['maNguoiDung']) ?>"
                                    aria-label="Báo cáo tài khoản">
                                <img src="<?= h(asset_url('icon/report_flag.png')) ?>" alt="" width="28" height="28" />
                            </button>
                            <?php endif; ?>

                            <!-- Mobile: avatar nhỏ bên trái + username/stats bên phải -->
                            <div class="d-flex d-lg-none align-items-center gap-3 mb-3">
                                <div class="profile-avatar-box flex-shrink-0" style="width:80px;height:80px;">
                                    <img src="<?= h(profile_image_url($profile['anhDaiDien'])) ?>"
                                         alt="<?= h($profile['tenHienThi']) ?>"
                                         class="profile-avatar w-100 h-100 rounded-circle object-fit-cover"
                                         id="profilePageAvatar" />
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <h1 class="profile-username mb-0 fs-6 fw-bold text-truncate"><?= h($profile['tenDangNhap']) ?></h1>
                                    <p class="profile-name mb-2 small text-muted text-truncate" id="profilePageDisplayName"><?= h($profile['tenHienThi']) ?></p>
                                    <div class="d-flex gap-2 flex-wrap profile-stats">
                                        <span class="small"><strong><?= $postCount ?></strong> bài</span>
                                        <button type="button" class="profile-stat-btn small p-0 border-0 bg-transparent" id="btnShowFollowersMobile" data-follow-type="followers">
                                            <strong id="followerCountMobile"><?= $followerCount ?></strong> theo dõi
                                        </button>
                                        <button type="button" class="profile-stat-btn small p-0 border-0 bg-transparent" id="btnShowFollowingMobile" data-follow-type="following">
                                            Đang theo <strong id="followingCountMobile"><?= $followingCount ?></strong>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Mobile: bio hiển thị full width phía dưới -->
                            <div class="d-lg-none px-1 mb-2">
                                <?php if ($profile['moTa']): ?>
                                    <p class="profile-bio mb-0 small" id="profilePageBioMobile"><?= nl2br(h($profile['moTa'])) ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Desktop: layout cũ giữ nguyên -->
                            <div class="d-none d-lg-flex row align-items-center justify-content-center g-4">
                                <div class="col-lg-3 text-center">
                                    <div class="profile-avatar-box mx-auto">
                                        <img src="<?= h(profile_image_url($profile['anhDaiDien'])) ?>"
                                             alt="<?= h($profile['tenHienThi']) ?>"
                                             class="profile-avatar"
                                             id="profilePageAvatar" />
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <div class="profile-info text-lg-start">
                                        <h1 class="profile-username mb-1"><?= h($profile['tenDangNhap']) ?></h1>
                                        <p class="profile-name mb-3" id="profilePageDisplayName"><?= h($profile['tenHienThi']) ?></p>
                                        <div class="row g-2 mb-3 justify-content-lg-start profile-stats">
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
                                            <p class="profile-bio mb-0" id="profilePageBio"><?= nl2br(h($profile['moTa'])) ?></p>
                                        <?php else: ?>
                                            <p class="profile-bio mb-0 d-none" id="profilePageBio"></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <?php if (!$isOwnProfile): ?>
                        <section class="mb-4 profile-actions px-1">
                            <div class="d-flex gap-2">
                                <button type="button"
                                        class="btn btn-follow flex-fill <?= $isFollowing ? 'following' : '' ?>"
                                        id="btnFollow"
                                        data-target-id="<?= h($profile['maNguoiDung']) ?>"
                                        data-following="<?= $isFollowing ? '1' : '0' ?>">
                                    <?= $isFollowing ? 'Đang theo dõi' : 'Theo dõi' ?>
                                </button>
                                <button type="button" class="btn btn-pet flex-fill">Vuốt ve 🐾</button>
                            </div>
                        </section>
                        <?php else: ?>
<section class="mb-4 profile-actions px-1">
    <div class="d-flex gap-2">
        <button type="button"
                class="btn btn-dark flex-fill rounded-3 py-2"
                id="btnOpenEditProfile">
            Chỉnh sửa trang cá nhân
        </button>
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
                                            style="overflow:hidden;display:block;"
                                            data-post-id="<?= h($post['maBaiDang']) ?>"
                                            aria-label="Xem bài đăng">
                                        <img src="<?= h(post_image_url($post['duongDan'])) ?>"
                                             alt="<?= h($post['noiDung'] ?? 'Bài đăng') ?>"
                                             style="width:100%;aspect-ratio:1/1;object-fit:cover;display:block;" />
                                        <div class="post-item-overlay">
                                            <span><img src="<?= h(asset_url('icon/pawheart.png')) ?>" alt="" /> <?= (int) $post['soPhanUng'] ?></span>
                                            <span><img src="<?= h(asset_url('icon/message.png')) ?>" alt="" /> <?= (int) $post['soBinhLuan'] ?></span>
                                        </div>
                                    </button>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </section>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <?php require __DIR__ . '/partials/profile_modals.php'; ?>

    <div class="toast-container position-fixed bottom-0 end-0 p-3" id="profileToastWrap"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= h(asset_url('js/profile.js')) ?>"></script>
    <script src="<?= h(asset_url('js/settings.js')) ?>"></script>
</body>
</html>