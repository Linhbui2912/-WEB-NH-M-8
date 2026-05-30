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
                        <section class="mb-4 profile-actions px-1 ">
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
                                <div class="col-6 col-md-3 profile-post-col position-relative">

                                <?php if ($isOwnProfile): ?>
                                    <div class="dropdown position-absolute top-0 end-0 m-2" style="z-index: 10;">
                                        <button class="btn btn-dark btn-sm rounded-circle opacity-75 border-0 p-1 d-flex align-items-center justify-content-center" 
                                                type="button" 
                                                data-bs-toggle="dropdown" 
                                                aria-expanded="false"
                                                style="width: 28px; height: 28px;">
                                            <span style="line-height: 0; font-size: 14px; color: #fff;">•••</span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <li>
                                                <button type="button" 
                                                        class="dropdown-item btn-edit-post" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editPostModal"
                                                        data-post-id="<?= h($post['maBaiDang']) ?>"
                                                        data-post-content="<?= h($post['noiDung'] ?? '') ?>"
                                                        data-post-image="<?= h(post_image_url($post['duongDan'])) ?>">
                                                    Chỉnh sửa bài viết
                                                </button>
                                            </li>
                                            <li>
                                            <button type="button" 
                                                class="dropdown-item text-danger"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#confirmDeleteModal"
                                                data-post-id="<?= h($post['maBaiDang']) ?>">
                                            Xóa bài viết
                                        </button>
                                        </li>
                                        </ul>
                                    </div>
                                    <?php endif; ?>

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
    <?php require __DIR__ . '/partials/editpostmodal.php'; ?>
    <?php require __DIR__ . '/partials/deletepostmodal.php'; ?>
                                    
    <div class="toast-container position-fixed bottom-0 end-0 p-3" id="profileToastWrap"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= h(asset_url('js/profile.js')) ?>"></script>
    <script>

document.addEventListener('DOMContentLoaded', function () {

    const editPostModal = document.getElementById('editPostModal');

    if (editPostModal) {

        // Lắng nghe hành vi mở modal tự động từ Bootstrap 5

        editPostModal.addEventListener('show.bs.modal', function (event) {

            // 1. Xác định nút 3 chấm vừa được click

            const button = event.relatedTarget;           

            // 2. Đọc các giá trị dữ liệu từ thuộc tính data-* của nút đó

            const postId = button.getAttribute('data-post-id');

            const postContent = button.getAttribute('data-post-content');

            const postImage = button.getAttribute('data-post-image'); // Lấy đường dẫn ảnh từ Profile truyền sang



            // 3. Tìm các thành phần đích bên trong editpostmodal.php để đổ dữ liệu vào

            const inputId = editPostModal.querySelector('#editPostId');

            const textareaContent = editPostModal.querySelector('#editPostContent');

            const carouselInner = editPostModal.querySelector('#editCarouselPreviewItems');

            const carouselContainer = editPostModal.querySelector('#editImageCarouselPreview');

            const deleteBtn = editPostModal.querySelector('#editDeleteBtn');

            // 4. Điền thông tin chữ vào Form

            inputId.value = postId;

            textareaContent.value = postContent;


            // 5. Xử lý hiển thị hình ảnh vào Carousel

            // Reset lại Carousel cũ tránh bị cộng dồn ảnh của bài viết bấm trước đó

            carouselInner.innerHTML = '';


            if (postImage && postImage.trim() !== '' && !postImage.includes('default-placeholder')) {

                // Nếu bài viết có ảnh hợp lệ, tạo cấu trúc item ảnh cho Bootstrap Carousel

                const carouselItem = `

                    <div class="carousel-item active">

                        <img src="${postImage}" class="d-block w-100" alt="Ảnh bài viết">

                    </div>

                `;

                // Chèn ảnh vào trong lòng Carousel

                carouselInner.innerHTML = carouselItem;              

                // Kích hoạt hiển thị khung Carousel và nút xóa ảnh

                carouselContainer.style.display = 'block';

                carouselInner.classList.add('edit-has-images');

                if (deleteBtn) deleteBtn.style.display = 'block';

            } else {

                // Nếu bài viết chỉ có chữ (không có ảnh), ẩn toàn bộ khung Carousel đi

                carouselContainer.style.display = 'none';

                carouselInner.classList.remove('edit-has-images');

                if (deleteBtn) deleteBtn.style.display = 'none';

            }

        });

    }
    const confirmDeleteModal = document.getElementById('confirmDeleteModal');

if (confirmDeleteModal) {
    // 1. Khi modal hiện lên -> bắt ID từ nút 3 chấm truyền vào input ẩn của form
    confirmDeleteModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget; 
        const postId = button.getAttribute('data-post-id'); 

        const hiddenInput = document.getElementById('hiddenDeletePostId');
        if (hiddenInput) {
            hiddenInput.value = postId;
        }
    });

    // 2. Khi nhấn nút "Xóa" màu đỏ -> submit form trực tiếp sang controller
    const btnConfirmDeleteExecute = document.getElementById('btnConfirmDeleteExecute');
    if (btnConfirmDeleteExecute) {
        btnConfirmDeleteExecute.addEventListener('click', function () {
            const hiddenForm = document.getElementById('hiddenDeletePostForm');
            if (hiddenForm) {
                hiddenForm.submit(); 
            }
        });
    }
}
});

    </script>
    <script src="<?= h(asset_url('js/settings.js')) ?>"></script>
</body>
</html>