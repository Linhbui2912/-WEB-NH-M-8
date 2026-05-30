<form id="hiddenDeletePostForm" action="../controllers/deletepost.php" method="POST">
    <input type="hidden" name="maBaiDang" id="hiddenDeletePostId">
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger w-100 text-center" id="confirmDeleteModalLabel">Xác nhận xóa</h5>
                </div>
                <div class="modal-body text-center py-3 text-muted">
                    Bạn có chắc chắn muốn xóa bài đăng này không? Hành động này không thể hoàn tác.
                </div>
                <div class="modal-footer d-flex border-0 pt-0 gap-2">
                    <input type="hidden" id="deletePostIdHidden" value="">
                    <button type="button" class="btn btn-light flex-fill rounded-3" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger flex-fill rounded-3" id="btnConfirmDeleteExecute">Xóa</button>
                </div>
            </div>
        </div>
    </div>
</form>