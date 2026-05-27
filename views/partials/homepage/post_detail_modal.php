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
                <button type="button" class="btn btn-link p-0 post-action-btn paw-like-btn" id="postDetailLikeBtn" aria-label="Thích">
                  <img src="../assets/icon/footprint.png" data-icon-white="../assets/icon/footprint.png" data-icon-liked="../assets/icon/pawheart.png" alt="Paw" width="28" height="28" />
                </button>
                <button type="button" class="btn btn-link p-0 post-action-btn" id="postDetailCommentFocusBtn" aria-label="Bình luận">
                  <img src="../assets/icon/message.png" alt="Comment" width="26" height="26" />
                </button>
              </div>
              <button type="button" class="btn btn-link p-0 post-action-btn" id="postDetailReportBtn" aria-label="Báo cáo bài đăng">
                <!-- Nếu icon không hiện: kiểm tra file assets/icon/report_flag.png -->
                <img src="../assets/icon/report_flag.png" alt="Báo cáo" width="26" height="26" /> 
              </button>
            </div>
            <p class="post-detail-likes mb-2" id="postDetailLikes"><strong>0</strong> lượt paw</p>
            <form id="postDetailCommentForm" class="post-detail-comment-form d-flex gap-2 align-items-center">
              <input type="hidden" id="postDetailPostId" name="post_id" value="" />
              <input type="text"
                     id="postDetailCommentInput"
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
