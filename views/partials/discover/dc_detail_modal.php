<style>
    .modal-nav-btn { position: absolute; top: 50%; transform: translateY(-50%); background: #fff; border: none; border-radius: 50%; width: 45px; height: 45px; z-index: 1070; box-shadow: 0 4px 12px rgba(0,0,0,0.2); font-size: 20px; font-weight: bold; }
    .modal-prev { left: -60px; } .modal-next { right: -60px; }
    @media (max-width: 768px) { .modal-prev { left: 10px; } .modal-next { right: 10px; } }
</style>

<div class="modal fade" id="postDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="position: relative;">
        <button class="modal-nav-btn modal-prev" onclick="prevPost()">❮</button>
        <button class="modal-nav-btn modal-next" onclick="nextPost()">❯</button>

        <div class="modal-content overflow-hidden" style="border-radius: 8px; border: none;">
            <div class="row g-0">
                <div class="col-12 col-md-7 bg-dark d-flex align-items-center justify-content-center" style="min-height: 300px;">
                    <img id="modalImg" src="" alt="Lỗi tải ảnh" style="max-height: 80vh; max-width: 100%; object-fit: contain;">
                </div>
                
                <div class="col-12 col-md-5 d-flex flex-column bg-white" style="height: 100%; max-height: 80vh;">
                    <div class="p-3 border-bottom d-flex align-items-center">
                        <img id="modalAvatar" src="" class="rounded-circle me-2 border" width="32" height="32" style="object-fit: cover;">
                        <span id="modalUsername" class="fw-bold"></span>
                    </div>

                    <div class="p-3 flex-grow-1 overflow-auto ig-scrollbar" style="min-height: 200px;">
                        <div class="d-flex mb-3">
                            <span class="fw-bold me-2" id="modalCaptionUser"></span>
                            <span id="modalCaption"></span>
                        </div>
                        <div id="modalCommentsContainer"></div>
                    </div>

                    <div class="p-3 border-top">
                        <div class="d-flex justify-content-between mb-2 align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <img id="modalLikeIcon" src="../assets/icon/footprint.png" style="width: 24px; cursor: pointer;" alt="Like" onclick="toggleLike()">
                                <img src="../assets/icon/message.png" style="width: 24px; cursor: pointer;" alt="Comment" onclick="document.getElementById('commentInput').focus();">
                            </div>
                            <img src="../assets/icon/report_flag.png" style="width: 24px; cursor: pointer;" alt="Report" onclick="openReportModal()">
                        </div>
                        
                        <div class="fw-bold mb-2"><span id="modalPaws">0</span> lượt paw</div>
                        
                        <div class="d-flex border-top pt-3">
                            <input type="text" id="commentInput" class="form-control border-0 shadow-none px-0" placeholder="Thêm bình luận...">
                            <button class="btn text-primary fw-bold px-2" onclick="addComment()">Đăng</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>