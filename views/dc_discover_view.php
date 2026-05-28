<?php

$localPetImages = [
    'C1.1.jpg', 'C1.2.jpg', 'C1.3.jpg', 'C1.4.jpg', 'C1.5.jpg', 'C1.6.jpg', 'C1.7.jpg', 'C1.8.jpg',
    'C2.1.jpg', 'C5.1.jpg', 'C5.2.jpg',
    'D1.1.jpg', 'D1.2.jpg', 'D2.1.jpg', 'D2.2.jpg', 'D2.3.jpg', 'D2.4.jpg', 'D3.1.jpg', 'D3.2.jpg', 'D3.3.jpg', 'D4.1.jpg', 'D4.2.jpg'
];

$safePosts = [];
if (!empty($danhSachBaiDang)) {
    $imgIndex = 0;
    foreach ($danhSachBaiDang as $p) {
        $chosenImage = $localPetImages[$imgIndex % count($localPetImages)];
        $p['duongDan_fixed'] = $chosenImage;
        
    
        $ava = $p['anhDaiDien'] ?? '';
        if ($ava !== '' && !preg_match('/\.(jpg|jpeg|png|gif)$/i', $ava)) {
            $ava .= '.jpg';
        }
        $p['anhDaiDien_fixed'] = $ava ?: 'C1.jpg'; 
        
       
        $p['comments'] = [];
        
        $imgIndex++;
        $safePosts[] = $p;
    }
}
$jsonString = htmlspecialchars(json_encode($safePosts, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
?>

<!doctype html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PawConnect - Khám phá</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #fafafa; }
    

    .left-sidebar { 
        padding-top: 30px; 
        border-right: 1px solid #efefef; 
        background: #fff; 
        min-height: 100vh; 
        position: fixed; 
        width: 80px; 
        z-index: 1000; 
        display: flex; 
        flex-direction: column; 
        align-items: center; 
    }
    .sidebar-logo img { width: 40px; display: block; }
    
    /* ĐÃ SỬA: Thêm flex-grow và justify-content để căn giữa menu */
    .sidebar-nav { 
        display: flex; 
        flex-direction: column; 
        gap: 35px; 
        width: 100%; 
        align-items: center; 
        flex-grow: 1; 
        justify-content: center; 
    }
    
    .sidebar-nav .nav-icon img, .settings-icon img { width: 26px; height: 26px; display: block; transition: transform 0.2s;}
    .sidebar-nav .nav-icon:hover img, .settings-icon:hover img { transform: scale(1.1); }
    
    .feed-wrapper { margin-left: 80px; padding-bottom: 50px; }
    .explore-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 4px; }
    .explore-item { display: block; position: relative; aspect-ratio: 1 / 1; overflow: hidden; border: none; padding: 0; cursor: pointer;}
    .explore-item img { width: 100%; height: 100%; object-fit: cover; display: block; }
    
    .modal-nav-btn { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(255, 255, 255, 0.9); border: none; border-radius: 50%; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; z-index: 1070; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.2s; pointer-events: auto; }
    .modal-nav-btn:hover { background: rgba(255, 255, 255, 1); transform: translateY(-50%) scale(1.05); }
    .modal-prev { left: -65px; } .modal-next { right: -65px; }
    
    .ig-scrollbar::-webkit-scrollbar { width: 5px; } .ig-scrollbar::-webkit-scrollbar-thumb { background: #ccc; border-radius: 5px; }

    .report-option { cursor: pointer; color: #262626; font-size: 15px; border-top: 1px solid #efefef !important; border-bottom: none !important;}
    .report-option.active-reason { color: #0095f6 !important; font-weight: 600; background-color: transparent; }
    .btn-pill { border-radius: 20px; padding: 8px 32px; font-size: 14px; }
    
    @media (max-width: 1200px) { .modal-prev { left: 15px; } .modal-next { right: 15px; } }
    @media (max-width: 768px) {
        .left-sidebar { width: 60px; padding-top: 20px; }
        .sidebar-logo img { width: 30px; }
        .sidebar-nav img, .settings-icon img { width: 22px; }
        .feed-wrapper { margin-left: 60px; padding: 10px; }
    }
  </style>
</head>
<body>
  <div id="postsData" data-json="<?= $jsonString ?>" style="display: none;"></div>

  <div class="container-fluid px-0">
    <div class="row g-0">
      <aside class="left-sidebar">
        <a class="sidebar-logo mb-4" href="../views/homepage.php" data-bs-toggle="tooltip" data-bs-title="Trang chủ PawConnect">
            <img src="../assets/icon/PawsConnect.png" alt="PawConnect Logo" />
        </a>
        <nav class="sidebar-nav">
            <a href="../views/homepage.php" class="nav-icon"><img src="../assets/icon/home_5973558.png" alt="Home" /></a>
            <a href="../views/search.php" class="nav-icon"><img src="../assets/icon/search.png" alt="Search" /></a>
            <a href="../controllers/dc_discover_controller.php" class="nav-icon active"><img src="../assets/icon/discovery_12028921.png" alt="Discover" /></a>
            <a href="../views/create-post.php" class="nav-icon"><img src="../assets/icon/add.png" alt="Create" /></a>
            <a href="../views/profile.php" class="nav-icon"><img src="../assets/icon/user.png" alt="Account" /></a>
        </nav>
        
        <a href="../views/settings.php" class="nav-icon settings-icon mt-auto mb-4">
            <img src="../assets/icon/setting.png" alt="Settings" />
        </a>
      </aside>

      <main class="feed-wrapper col">
        <div class="container-fluid pt-3 px-4" style="max-width: 900px; margin: 0 auto;">
            <div class="explore-grid">
                <?php if (!empty($safePosts)): ?>
                    <?php foreach ($safePosts as $index => $post): ?>
                        <div class="explore-item" onclick="openModal(<?= $index ?>)">
                            <img src="../assets/Posts/<?= $post['duongDan_fixed'] ?>" alt="Pet">
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">Không có dữ liệu bài đăng.</div>
                <?php endif; ?>
            </div>
        </div>
      </main>
    </div>
  </div>

  <div class="modal fade" id="postDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="position: relative;"> 
      
      <button type="button" class="modal-nav-btn modal-prev" onclick="prevPost()">
        <svg fill="currentColor" height="24" viewBox="0 0 24 24" width="24"><path d="M14.207 5.293a1 1 0 0 0-1.414 0l-6 6a1 1 0 0 0 0 1.414l6 6a1 1 0 0 0 1.414-1.414L8.914 12l5.293-5.293a1 1 0 0 0 0-1.414z"></path></svg>
      </button>
      <button type="button" class="modal-nav-btn modal-next" onclick="nextPost()">
        <svg fill="currentColor" height="24" viewBox="0 0 24 24" width="24"><path d="M9.793 18.707a1 1 0 0 0 1.414 0l6-6a1 1 0 0 0 0-1.414l-6-6a1 1 0 0 0-1.414 1.414L15.086 12l-5.293 5.293a1 1 0 0 0 0 1.414z"></path></svg>
      </button>

      <div class="modal-content overflow-hidden" style="border-radius: 4px; border: none;">
        <div class="row g-0"> 
          <div class="col-md-7 bg-dark d-flex align-items-center justify-content-center" style="min-height: 600px;">
            <img id="modalMainImg" src="" class="img-fluid" style="max-height: 90vh; object-fit: contain;">
          </div>
          <div class="col-md-5 d-flex flex-column bg-white" style="height: 100%; max-height: 90vh;">
            
            <div class="p-3 border-bottom d-flex align-items-center">
              <a id="modalHeaderProfileLink" href="#" style="text-decoration: none; color: inherit; display: flex; align-items: center;">
                  <img id="modalHeaderAvatar" src="" class="rounded-circle me-2 border" width="32" height="32" style="object-fit: cover;">
                  <span id="modalHeaderName" class="fw-bold" style="font-size: 0.9rem;"></span>
              </a>
            </div>

            <div class="p-3 flex-grow-1 overflow-auto ig-scrollbar" style="min-height: 250px;">
                <div class="d-flex mb-3">
                    <a id="modalCapProfileLink" href="#" style="text-decoration: none; color: inherit;">
                        <img id="modalAvatarCap" src="" class="rounded-circle me-2 border" width="32" height="32" style="object-fit: cover;">
                    </a>
                    <div>
                        <a id="modalCapNameLink" href="#" style="text-decoration: none; color: inherit;">
                            <span class="fw-bold me-1" id="modalCaptionUser" style="font-size: 0.9rem;"></span>
                        </a>
                        <span id="modalCaption" style="font-size: 0.9rem;"></span>
                    </div>
                </div>
                <div id="modalCommentsContainer"></div>
            </div>

            <div class="p-3 border-top">
              <div class="d-flex justify-content-between mb-2">
                 <div class="d-flex gap-3 align-items-center">
                    <img id="modalLikeIcon" src="../assets/icon/footprint.png" style="width: 24px; cursor: pointer;" onclick="toggleLike()">
                    <img src="../assets/icon/message.png" style="width: 24px; cursor: pointer;" onclick="document.getElementById('commentInput').focus()">
                 </div>
                 <img src="../assets/icon/report_flag.png" style="width: 24px; cursor: pointer;" onclick="openReportModal()">
              </div>
              <p id="modalLikes" class="fw-bold mb-1" style="font-size: 0.9rem;">0 lượt paw</p>
              
              <div class="d-flex border-top pt-3 align-items-center">
                <input id="commentInput" type="text" class="form-control border-0 shadow-none px-2" placeholder="Thêm bình luận..." style="font-size: 0.9rem;">
                <button class="btn text-primary fw-bold px-0 ms-2" type="button" style="font-size: 0.9rem;" onclick="addComment()">Đăng</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="reportModal" tabindex="-1" style="z-index: 1080;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content text-center" style="border-radius: 12px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
            <div class="modal-header border-bottom-0 pb-0 justify-content-center mt-3">
                <h6 class="modal-title fw-bold" style="font-size: 16px;">Vì sao bạn muốn báo cáo bài đăng này?</h6>
            </div>
            <div class="modal-body p-0 mt-3">
                <div class="list-group list-group-flush text-center w-100" id="reportReasonList">
                    <button class="list-group-item list-group-item-action py-3 report-option" onclick="selectReason(this, 'Vấn đề liên quan đến người dưới 18 tuổi')">Vấn đề liên quan đến người dưới 18 tuổi</button>
                    <button class="list-group-item list-group-item-action py-3 report-option" onclick="selectReason(this, 'Bắt nạt, lạm dụng, ngược đãi')">Bắt nạt, lạm dụng, ngược đãi</button>
                    <button class="list-group-item list-group-item-action py-3 report-option" onclick="selectReason(this, 'Có hành vi tự hại')">Có hành vi tự hại</button>
                    <button class="list-group-item list-group-item-action py-3 report-option" onclick="selectReason(this, 'Nội dung kích động thù ghét')">Nội dung kích động thù ghét</button>
                    <button class="list-group-item list-group-item-action py-3 report-option" onclick="selectReason(this, 'Vi phạm Quyền sở hữu trí tuệ')">Vi phạm Quyền sở hữu trí tuệ</button>
                </div>
            </div>
            <div class="modal-footer justify-content-center border-top-0 pt-2 pb-4 gap-2">
                <button type="button" class="btn btn-dark btn-pill fw-bold" id="btnSubmitReport" disabled onclick="submitSelectedReport()">Báo cáo</button>
                <button type="button" class="btn btn-light btn-pill border fw-bold" style="background: #e4e6eb;" onclick="closeReportModal()">Hủy</button>
            </div>
        </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    const postsDataEl = document.getElementById('postsData');
    const postsArray = JSON.parse(postsDataEl.getAttribute('data-json') || '[]');
    let currentIndex = -1;
    let selectedReportReason = null;
    let reportModalInstance = null;

    document.addEventListener('DOMContentLoaded', () => {
        reportModalInstance = new bootstrap.Modal(document.getElementById('reportModal'), { backdrop: false });
    });

    document.addEventListener('keydown', function(event) {
      if (currentIndex !== -1 && document.getElementById('postDetailModal').classList.contains('show')) {
        if (event.key === 'ArrowRight' || event.key === '>') nextPost();
        if (event.key === 'ArrowLeft' || event.key === '<') prevPost();
      }
    });

    function updateModalData(index) {
        if (!postsArray || postsArray.length === 0) return;
        currentIndex = index;
        const data = postsArray[index];

        document.getElementById('modalMainImg').src = '../assets/Posts/' + data.duongDan_fixed;
        
        let avaSrc = '../assets/Profile/' + data.anhDaiDien_fixed;
        let imgHeader = document.getElementById('modalHeaderAvatar');
        let imgCap = document.getElementById('modalAvatarCap');
        imgHeader.src = avaSrc;
        imgCap.src = avaSrc;
        
        imgHeader.onerror = function() { this.src = '../assets/Profile/C1.jpg'; };
        imgCap.onerror = function() { this.src = '../assets/Profile/C1.jpg'; };
        
        document.getElementById('modalHeaderName').innerText = data.tenDangNhap;
        document.getElementById('modalCaptionUser').innerText = data.tenDangNhap;
        document.getElementById('modalCaption').innerText = data.noiDung || '';
        document.getElementById('modalLikes').innerText = data.soLuotPaw + ' lượt paw';

        // ĐÃ SỬA: Chèn link profile tự động thông qua Javascript dựa trên tenDangNhap
        let profileUrl = '../controllers/profile_controller.php?user=' + encodeURIComponent(data.tenDangNhap);
        document.getElementById('modalHeaderProfileLink').href = profileUrl;
        document.getElementById('modalCapProfileLink').href = profileUrl;
        document.getElementById('modalCapNameLink').href = profileUrl;

        const likeIcon = document.getElementById('modalLikeIcon');
        likeIcon.src = '../assets/icon/footprint.png';
        likeIcon.setAttribute('data-liked', 'false');

        const commentsContainer = document.getElementById('modalCommentsContainer');
        commentsContainer.innerHTML = ''; 
        if (data.comments && data.comments.length > 0) {
            data.comments.forEach(c => {
                let newComment = `
                    <div class="d-flex mb-3">
                        <img src="../assets/Profile/C1.jpg" class="rounded-circle me-2 border" width="32" height="32" style="object-fit: cover;">
                        <div><span class="fw-bold me-1" style="font-size: 0.9rem;">${c.username}</span><span style="font-size: 0.9rem;">${c.text}</span></div>
                    </div>`;
                commentsContainer.insertAdjacentHTML('beforeend', newComment);
            });
        }
    }

    function openModal(index) {
        updateModalData(index);
        new bootstrap.Modal(document.getElementById('postDetailModal')).show();
    }

    function prevPost() {
        if (postsArray.length <= 1) return;
        let newIndex = (currentIndex - 1 + postsArray.length) % postsArray.length;
        updateModalData(newIndex);
    }

    function nextPost() {
        if (postsArray.length <= 1) return;
        let newIndex = (currentIndex + 1) % postsArray.length;
        updateModalData(newIndex);
    }

    function toggleLike() {
        const likeIcon = document.getElementById('modalLikeIcon');
        const pawsText = document.getElementById('modalLikes');
        let currentPaws = parseInt(pawsText.innerText) || 0;
        
        if (likeIcon.getAttribute('data-liked') === 'true') {
            likeIcon.src = '../assets/icon/footprint.png';
            likeIcon.setAttribute('data-liked', 'false');
            pawsText.innerText = currentPaws - 1 + ' lượt paw';
        } else {
            likeIcon.src = '../assets/icon/pawheart.png';
            likeIcon.setAttribute('data-liked', 'true');
            pawsText.innerText = currentPaws + 1 + ' lượt paw';
        }
    }

    function openReportModal() {
        selectedReportReason = null;
        document.querySelectorAll('.report-option').forEach(opt => opt.classList.remove('active-reason'));
        document.getElementById('btnSubmitReport').disabled = true;
        reportModalInstance.show();
    }

    function closeReportModal() {
        reportModalInstance.hide();
    }

    function selectReason(btnElement, reason) {
        selectedReportReason = reason;
        document.querySelectorAll('.report-option').forEach(opt => opt.classList.remove('active-reason'));
        btnElement.classList.add('active-reason');
        document.getElementById('btnSubmitReport').disabled = false;
    }

    function submitSelectedReport() {
        if (!selectedReportReason || currentIndex === -1) return;
        const currentPost = postsArray[currentIndex];
        
        fetch('../controllers/ReportController.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ post_id: currentPost.maBaiDang, reason: selectedReportReason })
        }).finally(() => {
            alert("Đã gửi báo cáo thành công!");
            reportModalInstance.hide();
        });
    }

    function addComment() {
        const input = document.getElementById('commentInput');
        const text = input.value.trim();
        if (text === '') return; 
        
        postsArray[currentIndex].comments.push({
            username: 'Bạn',
            text: text
        });
        
        let newComment = `
            <div class="d-flex mb-3">
                <img src="../assets/Profile/C1.jpg" class="rounded-circle me-2 border" width="32" height="32" style="object-fit: cover;">
                <div><span class="fw-bold me-1" style="font-size: 0.9rem;">Bạn</span><span style="font-size: 0.9rem;">${text}</span></div>
            </div>`;
        document.getElementById('modalCommentsContainer').insertAdjacentHTML('beforeend', newComment);
        input.value = ''; 
        
        const container = document.getElementById('modalCommentsContainer');
        container.scrollTop = container.scrollHeight;
    }
  </script>
</body>
</html>