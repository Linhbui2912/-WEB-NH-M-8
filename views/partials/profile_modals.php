<?php

declare(strict_types=1);

/** @var array<string,mixed>|null $profile */
/** @var bool $isOwnProfile */

$icon = static fn (string $name): string => asset_url('icon/' . $name);

$postReportReasons = [
    'Vấn đề liên quan đến người dưới 18 tuổi',
    'Bắt nạt, lạm dụng, ngược đãi',
    'Có hành vi tự hại',
    'Nội dung kích động thù ghét',
    'Vi phạm Quyền sở hữu trí tuệ',
];

$accountReportReasons = [
    'Giả mạo người khác',
    'Spam hoặc lừa đảo',
    'Quấy rối, bắt nạt',
    'Nội dung phản cảm',
    'Vi phạm tiêu chuẩn cộng đồng',
];
?>
<!-- Modal chi tiết bài đăng -->
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
                        <div class="post-detail-actions d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-3">
                                <button type="button" class="btn btn-link p-0 post-action-btn" id="btnLikePost" aria-label="Yêu thích">
                                    <img src="<?= h($icon('footprint.png')) ?>" alt="Paw" width="28" height="28" data-icon-default="<?= h($icon('footprint.png')) ?>" data-icon-liked="<?= h($icon('pawheart.png')) ?>" />
                                </button>
                                <button type="button" class="btn btn-link p-0 post-action-btn" id="btnFocusComment" aria-label="Bình luận">
                                    <img src="<?= h($icon('message.png')) ?>" alt="Comment" width="26" height="26" />
                                </button>
                            </div>
                            <button type="button" class="btn btn-link p-0 post-action-btn" id="btnReportPost" aria-label="Báo cáo bài đăng">
                                <img src="<?= h($icon('report_flag.png')) ?>" alt="Báo cáo" width="26" height="26" />
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
            <div class="modal-body p-0" id="followListBody"></div>
        </div>
    </div>
</div>

<!-- Modal báo cáo bài đăng -->
<div class="modal fade profile-report-modal" id="reportPostModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content profile-report-content border-0">
            <div class="modal-body p-4">
                <h5 class="text-center fw-bold mb-4">Vì sao bạn muốn báo cáo bài đăng này?</h5>
                <ul class="list-unstyled profile-report-reasons mb-4" id="reportPostReasons">
                    <?php foreach ($postReportReasons as $reason): ?>
                    <li><button type="button" class="profile-report-reason-btn" data-reason="<?= h($reason) ?>"><?= h($reason) ?></button></li>
                    <?php endforeach; ?>
                </ul>
                <div class="row g-2">
                    <div class="col-6">
                        <button type="button" class="btn btn-dark w-100 rounded-3 py-2" id="btnSubmitReportPost" disabled>Gửi báo cáo</button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-secondary w-100 rounded-3 py-2" data-bs-dismiss="modal">Hủy</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal báo cáo tài khoản -->
<div class="modal fade profile-report-modal" id="reportAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content profile-report-content border-0">
            <div class="modal-body p-4">
                <h5 class="text-center fw-bold mb-4">Vì sao bạn muốn báo cáo tài khoản này?</h5>
                <ul class="list-unstyled profile-report-reasons mb-4" id="reportAccountReasons">
                    <?php foreach ($accountReportReasons as $reason): ?>
                    <li><button type="button" class="profile-report-reason-btn" data-reason="<?= h($reason) ?>"><?= h($reason) ?></button></li>
                    <?php endforeach; ?>
                </ul>
                <div class="row g-2">
                    <div class="col-6">
                        <button type="button" class="btn btn-dark w-100 rounded-3 py-2" id="btnSubmitReportAccount" disabled>Gửi báo cáo</button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-secondary w-100 rounded-3 py-2" data-bs-dismiss="modal">Hủy</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($isOwnProfile): ?>

<!-- Modal chỉnh sửa profile -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form method="post" action="../controllers/xuly_profile_update.php" enctype="multipart/form-data">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Chỉnh sửa trang cá nhân</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên hiển thị</label>
                        <input type="text" name="tenHienThi" class="form-control" required
                               value="<?= h($profile['tenHienThi'] ?? '') ?>" maxlength="100" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="moTa" class="form-control" rows="3" maxlength="500"><?= h($profile['moTa'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ảnh đại diện</label>
                        <input type="file" name="anhDaiDien" class="form-control" accept="image/*" />
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-follow w-100 rounded-3">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
