<style>
    .report-option.active-reason { color: #0d6efd !important; font-weight: 600; background-color: #f8f9fa; }
</style>

<div class="modal fade" id="reportModal" tabindex="-1" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center rounded-4">
            <div class="modal-header border-bottom-0 pb-0 justify-content-center mt-3">
                <h6 class="modal-title fw-bold">Vì sao bạn muốn báo cáo bài đăng này?</h6>
            </div>
            <div class="modal-body p-0 mt-3">
                <div class="list-group list-group-flush text-center" id="reportReasonList">
                    <button type="button" class="list-group-item list-group-item-action py-3 border-bottom report-option" onclick="selectReason(this, 'Người dưới 18 tuổi')">Vấn đề liên quan đến người dưới 18 tuổi</button>
                    <button type="button" class="list-group-item list-group-item-action py-3 border-bottom report-option" onclick="selectReason(this, 'Bắt nạt, ngược đãi')">Bắt nạt, lạm dụng, ngược đãi</button>
                    <button type="button" class="list-group-item list-group-item-action py-3 border-bottom report-option" onclick="selectReason(this, 'Tự hại')">Có hành vi tự hại</button>
                    <button type="button" class="list-group-item list-group-item-action py-3 border-bottom report-option" onclick="selectReason(this, 'Kích động thù ghét')">Nội dung kích động thù ghét</button>
                    <button type="button" class="list-group-item list-group-item-action py-3 border-bottom report-option" onclick="selectReason(this, 'Sở hữu trí tuệ')">Vi phạm Quyền sở hữu trí tuệ</button>
                </div>
            </div>
            <div class="modal-footer justify-content-center border-top-0 pt-3 pb-3 gap-2">
                <button type="button" class="btn btn-dark rounded-pill px-4 fw-bold" id="btnSubmitReport" disabled onclick="submitSelectedReport()">Báo cáo</button>
                <button type="button" class="btn btn-light border rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Hủy</button>
            </div>
        </div>
    </div>
</div>