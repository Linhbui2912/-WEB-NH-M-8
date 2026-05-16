<?php
// ==========================================
// PHẦN 1: TRUY VẤN DATABASE MYSQL ĐỘNG (RADMIN VPN)
// ==========================================
define("HOST", "26.151.17.5");       
define("DB", "db_pawsconnect");      
define("USER", "paws_user");         
define("PASSWORD", "");              

$errorMsg = "";
$posts = [];

try {
    $dsn = "mysql:host=" . HOST . ";dbname=" . DB . ";charset=utf8mb4";
    $db = new PDO($dsn, USER, PASSWORD);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
   
    $query = "SELECT BaiDang.*, NguoiDung.tenDangNhap, PhuongTien.duongDan
              FROM BaiDang 
              JOIN NguoiDung ON BaiDang.maNguoiDung = NguoiDung.maNguoiDung
              LEFT JOIN PhuongTien ON BaiDang.maBaiDang = PhuongTien.maBaiDang
              ORDER BY BaiDang.thoiGianDang DESC";
              
    $stmt = $db->prepare($query);
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $errorMsg = "Lỗi kết nối Cơ sở dữ liệu: " . $e->getMessage();
}


$localPetImages = [
    'C1.1.jpg', 'C1.2.jpg', 'C1.3.jpg', 'C1.4.jpg', 'C1.5.jpg', 'C1.6.jpg', 'C1.7.jpg', 'C1.8.jpg',
    'C2.1.jpg', 'C5.1.jpg', 'C5.2.jpg',
    'D1.1.jpg', 'D1.2.jpg', 'D2.1.jpg', 'D2.2.jpg', 'D2.3.jpg', 'D2.4.jpg', 'D3.1.jpg', 'D3.2.jpg', 'D3.3.jpg', 'D4.1.jpg', 'D4.2.jpg'
];
?>

<!doctype html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PawConnect - Discover</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/styles.css">
  
  <style>
    
    .left-sidebar {
        padding-top: 30px !important;
        border-right: 1px solid #efefef;
        background: #fff;
    }
    .sidebar-logo img {
        width: 40px !important; 
        height: auto !important;
        display: block;
        margin: 0 auto;
    }
    .sidebar-nav .nav-icon img {
        width: 26px !important;
        height: 26px !important;
        display: block;
        margin: 25px auto !important;
    }
    .settings-icon img {
        width: 26px !important;
        height: 26px !important;
        display: block;
        margin: 40px auto !important;
    }

    
    .explore-item {
        display: block;
        position: relative;
        aspect-ratio: 1 / 1;
        overflow: hidden;
        border-radius: 4px;
        margin-bottom: 4px;
    }
    .explore-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    
    .modal-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.9);
        border: none;
        border-radius: 50%;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1070;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        pointer-events: auto; 
        transition: background 0.2s, transform 0.1s;
    }
    .modal-nav-btn:hover { background: rgba(255, 255, 255, 1); transform: translateY(-50%) scale(1.05); }
    .modal-prev { left: -65px; }
    .modal-next { right: -65px; }
    
    @media (max-width: 1200px) {
        .modal-prev { left: 15px; }
        .modal-next { right: 15px; }
    }
    .saved-active { fill: currentColor !important; color: #000 !important; }
  </style>
</head>
<body>
  <div class="container-fluid px-0">
    <div class="row g-0">
      
      <aside class="left-sidebar col-2 col-md-1">
        <a class="sidebar-logo mb-4" href="../index.html">
            <img src="../assets/icon/PawsConnect.png" alt="PawConnect Logo" />
        </a>
        <nav class="sidebar-nav">
            <a href="../index.html" class="nav-icon"><img src="../assets/icon/home_5973558.png" alt="Home" /></a>
            <a href="search.html" class="nav-icon"><img src="../assets/icon/search.png" alt="Search" /></a>
            <a href="discover.php" class="nav-icon active"><img src="../assets/icon/discovery_12028921.png" alt="Discover" /></a>
            <a href="create-post.html" class="nav-icon"><img src="../assets/icon/add.png" alt="Create" /></a>
            <a href="profile.html" class="nav-icon"><img src="../assets/icon/user.png" alt="Account" /></a>
        </nav>
        <a href="settings.html" class="nav-icon settings-icon">
            <img src="../assets/icon/setting.png" alt="Settings" />
        </a>
      </aside>

      <main class="feed-wrapper col">
        <div class="container-fluid pt-3 px-2" style="max-width: 900px;">
          
          <?php if (!empty($errorMsg)) { ?>
              <div class="alert alert-danger text-center"><?php echo $errorMsg; ?></div>
          <?php } ?>

          <div class="row g-1"> 
            
            <?php 
            if (!empty($posts)) { 
                $imgIndex = 0; 
                foreach ($posts as $post) { 
                    $chosenImage = $localPetImages[$imgIndex % count($localPetImages)];
                    $imgIndex++;
            ?>
                    <div class="col-4">
                        <a href="#" class="explore-item" data-bs-toggle="modal" data-bs-target="#postDetailModal" onclick="openModal('post_<?php echo $post['maBaiDang']; ?>')">
                            <img src="../assets/Posts/<?php echo $chosenImage; ?>" class="explore-img" alt="Khám phá">
                        </a>
                    </div>
            <?php 
                } 
            } else { 
                if (empty($errorMsg)) {
            ?>
                    <div class="col-12 text-center text-muted py-5">Không có bài đăng nào trong hệ thống.</div>
            <?php 
                }
            } 
            ?>

          </div>
        </div>
      </main>

    </div>
  </div>

  <div class="modal fade" id="postDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="position: relative;"> 
      
      <button type="button" class="modal-nav-btn modal-prev" onclick="prevPost()">
        <svg aria-label="Quay lại" fill="currentColor" height="24" role="img" viewBox="0 0 24 24" width="24"><path d="M14.207 5.293a1 1 0 0 0-1.414 0l-6 6a1 1 0 0 0 0 1.414l6 6a1 1 0 0 0 1.414-1.414L8.914 12l5.293-5.293a1 1 0 0 0 0-1.414z"></path></svg>
      </button>

      <button type="button" class="modal-nav-btn modal-next" onclick="nextPost()">
        <svg aria-label="Tiếp" fill="currentColor" height="24" role="img" viewBox="0 0 24 24" width="24"><path d="M9.793 18.707a1 1 0 0 0 1.414 0l6-6a1 1 0 0 0 0-1.414l-6-6a1 1 0 0 0-1.414 1.414L15.086 12l-5.293 5.293a1 1 0 0 0 0 1.414z"></path></svg>
      </button>

      <div class="modal-content overflow-hidden" style="border-radius: 4px; border: none;">
        <div class="row g-0"> 
          
          <div class="col-md-7 bg-dark d-flex align-items-center justify-content-center" style="min-height: 600px;">
            <img id="modalMainImg" src="" class="img-fluid" alt="Ảnh chi tiết" style="max-height: 90vh; object-fit: contain;">
          </div>

          <div class="col-md-5 d-flex flex-column bg-white" style="height: 100%; max-height: 90vh;">
            
            <div class="p-3 border-bottom d-flex align-items-center">
              <img id="modalHeaderAvatar" src="" class="rounded-circle me-2 border" width="32" height="32" style="object-fit: cover;" alt="avatar">
              <div class="d-flex align-items-center flex-grow-1">
                <span id="modalHeaderName" class="fw-bold" style="font-size: 0.9rem;">Tên user</span>
                <span class="mx-1 text-muted">•</span>
                <span id="modalFollowBtn" class="fw-bold text-primary" style="font-size: 0.9rem; cursor: pointer;" onclick="toggleFollow()">Theo dõi</span>
              </div>
              <button class="btn p-0 border-0 text-dark fw-bold me-3">•••</button>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.7rem;"></button>
            </div>

            <div id="modalCommentsContainer" class="p-3 flex-grow-1 overflow-auto ig-scrollbar" style="height: 400px; font-size: 0.9rem;">
            </div>

            <div class="p-3 border-top">
              <div class="d-flex justify-content-between mb-2">
                 <div class="d-flex gap-3 align-items-center">
                    <img id="modalLikeIcon" src="../assets/icon/footprint.png" style="width: 24px; cursor: pointer;" alt="Like" onclick="toggleLike()">
                    <img src="../assets/icon/message.png" style="width: 24px; cursor: pointer;" alt="Comment" onclick="focusCommentInput()">
                    <img src="../assets/icon/share.png" style="width: 24px; cursor: pointer;" alt="Share">
                 </div>
                 <svg id="modalSaveIcon" aria-label="Lưu" class="x1lliihq x1n2onr6 x5n08af" fill="currentColor" height="24" role="img" viewBox="0 0 24 24" width="24" style="cursor: pointer;" onclick="toggleSave()"><title>Lưu</title><polygon fill="none" points="20 21 12 13.44 4 21 4 3 20 3 20 21" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></polygon></svg>
              </div>
              
              <p id="modalLikes" class="fw-bold mb-1" style="font-size: 0.9rem;">0 lượt thích</p>
              <p id="modalTimeAgo" class="text-muted mb-3" style="font-size: 0.7rem; text-transform: uppercase;"></p>
              
              <div class="d-flex border-top pt-3 align-items-center">
                <svg aria-label="Biểu tượng cảm xúc" class="me-2" fill="currentColor" height="24" role="img" viewBox="0 0 24 24" width="24" style="cursor: pointer;"><title>Biểu tượng cảm xúc</title><path d="M15.83 10.997a1.167 1.167 0 1 0 1.167 1.167 1.167 1.167 0 0 0-1.167-1.167Zm-6.5 1.167a1.167 1.167 0 1 0-1.166 1.167 1.167 1.167 0 0 0 1.166-1.167Zm5.163 3.24a3.406 3.406 0 0 1-4.982.007 1 1 0 1 0-1.557 1.256 5.397 5.397 0 0 0 8.09 0 1 1 0 0 0-1.55-1.263ZM12 .503a11.5 11.5 0 1 0 11.5 11.5A11.513 11.513 0 0 0 12 .503Zm0 21a9.5 9.5 0 1 1 9.5-9.5 9.51 9.51 0 0 1-9.5 9.5Z"></path></svg>
                <input id="commentInput" type="text" class="form-control border-0 shadow-none px-2" placeholder="Thêm bình luận..." style="font-size: 0.9rem;">
                <button class="btn text-primary fw-bold px-0 ms-2" type="button" style="font-size: 0.9rem;" onclick="addComment()">Đăng</button>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // ==========================================
    // PHẦN 3: ĐỔ DỮ LIỆU ĐỘNG TỪ MYSQL VÀO JAVASCRIPT
    // ==========================================
    const postsDatabase = {
      <?php 
      if (!empty($posts)) {
          $imgIndex = 0;
          foreach ($posts as $post) {
              $chosenImage = $localPetImages[$imgIndex % count($localPetImages)];
              $imgIndex++;
              
              $captionCleaned = addslashes($post['noiDung']);
              $usernameCleaned = htmlspecialchars($post['tenDangNhap']);
              $formattedDate = date("d/m/Y", strtotime($post['thoiGianDang']));
              $randomLikes = rand(50, 1200);
              
              echo "'post_" . $post['maBaiDang'] . "': { \n";
              echo "  image: '../assets/Posts/" . $chosenImage . "',\n";
              echo "  avatar: '../assets/Posts/C1.1.jpg',\n"; 
              echo "  username: '" . $usernameCleaned . "',\n";
              echo "  isFollowing: false,\n";
              echo "  isLiked: false,\n";
              echo "  isSaved: false,\n";
              echo "  likesCount: " . $randomLikes . ",\n"; 
              echo "  timeAgo: '" . $formattedDate . "',\n";
              echo "  caption: '" . $captionCleaned . "',\n";
              echo "  captionTime: '1 ngày',\n";
              echo "  comments: [\n";
              echo "    { avatar: '../assets/Posts/C1.3.jpg', username: 'paws_member', text: 'Nhìn bé cưng xỉu up xỉu down luôn á! ❤️', time: '5 giờ' }\n";
              echo "  ]\n";
              echo "},\n";
          }
      }
      ?>
    };

    const postKeys = Object.keys(postsDatabase);
    let currentPostId = null;

    function openModal(postId) {
      currentPostId = postId;
      updateModalData(postId);
    }

    function nextPost() {
      if (!currentPostId) return;
      let currentIndex = postKeys.indexOf(currentPostId);
      if (currentIndex < postKeys.length - 1) {
        openModal(postKeys[currentIndex + 1]);
      } else {
        openModal(postKeys[0]);
      }
    }

    function prevPost() {
      if (!currentPostId) return;
      let currentIndex = postKeys.indexOf(currentPostId);
      if (currentIndex > 0) {
        openModal(postKeys[currentIndex - 1]);
      } else {
        openModal(postKeys[postKeys.length - 1]);
      }
    }

    // SỬA LỖI ĐIỀU HƯỚNG BẰNG CẢ NÚT PHÍM MŨI TÊN VÀ NÚT SỰ KIỆN < >
    document.addEventListener('keydown', function(event) {
      if (currentPostId) {
        if (event.key === 'ArrowRight' || event.key === '>') nextPost();
        if (event.key === 'ArrowLeft' || event.key === '<') prevPost();
      }
    });

    // ==========================================
    // PHẦN 4: CÁC HÀM XỬ LÝ TƯƠNG TÁC ĐỘNG (INTERACTIVE)
    // ==========================================
    function updateModalData(postId) {
      const data = postsDatabase[postId];
      if (!data) return; 

      document.getElementById('modalMainImg').src = data.image;
      document.getElementById('modalHeaderAvatar').src = data.avatar;
      document.getElementById('modalHeaderName').innerText = data.username;
      document.getElementById('modalLikes').innerText = data.likesCount + ' lượt thích';
      document.getElementById('modalTimeAgo').innerText = data.timeAgo;
      
      const followBtn = document.getElementById('modalFollowBtn');
      if(data.isFollowing) {
          followBtn.innerText = 'Đang theo dõi';
          followBtn.classList.replace('text-primary', 'text-dark');
      } else {
          followBtn.innerText = 'Theo dõi';
          followBtn.classList.replace('text-dark', 'text-primary');
      }

      const likeIcon = document.getElementById('modalLikeIcon');
      if(data.isLiked) {
          likeIcon.src = '../assets/icon/pawheart.png'; 
      } else {
          likeIcon.src = '../assets/icon/footprint.png'; 
      }

      const saveIcon = document.getElementById('modalSaveIcon');
      if(data.isSaved) {
          saveIcon.classList.add('saved-active');
      } else {
          saveIcon.classList.remove('saved-active');
      }

      let commentsHTML = `
        <div class="d-flex mb-3">
          <img src="${data.avatar}" class="rounded-circle me-2 border" width="32" height="32" style="object-fit: cover;">
          <div>
            <span class="fw-bold me-1">${data.username}</span>
            <span>${data.caption}</span>
            <div class="text-muted mt-1" style="font-size: 0.75rem;">${data.captionTime}</div>
          </div>
        </div>
      `;

      if(data.comments) {
          data.comments.forEach(comment => {
            commentsHTML += `
              <div class="d-flex mb-3 justify-content-between">
                <div class="d-flex">
                  <img src="${comment.avatar}" class="rounded-circle me-2 border" width="32" height="32" style="object-fit: cover;">
                  <div>
                    <span class="fw-bold me-1">${comment.username}</span>
                    <span>${comment.text}</span>
                    <div class="text-muted mt-1" style="font-size: 0.75rem;">
                      <span class="me-3">${comment.time}</span>
                      <span class="me-3 fw-bold" style="cursor:pointer">Trả lời</span>
                    </div>
                  </div>
                </div>
              </div>
            `;
          });
      }
      document.getElementById('modalCommentsContainer').innerHTML = commentsHTML;
    }

    function toggleFollow() {
        if (!currentPostId) return;
        const data = postsDatabase[currentPostId];
        data.isFollowing = !data.isFollowing;
        updateModalData(currentPostId);
    }

    function toggleLike() {
        if (!currentPostId) return;
        const data = postsDatabase[currentPostId];
        data.isLiked = !data.isLiked;
        if(data.isLiked) {
            data.likesCount++;
        } else {
            data.likesCount--;
        }
        updateModalData(currentPostId);
    }

    // Tương tác Lưu bài viết
    function toggleSave() {
        if (!currentPostId) return;
        const data = postsDatabase[currentPostId];
        data.isSaved = !data.isSaved;
        updateModalData(currentPostId);
    }

    function focusCommentInput() {
        document.getElementById('commentInput').focus();
    }

    // Đăng bình luận động trực tiếp vào mảng JS
    function addComment() {
        if (!currentPostId) return;
        const input = document.getElementById('commentInput');
        const text = input.value.trim();
        if (text === '') return; 

        const data = postsDatabase[currentPostId];
        data.comments.push({
            avatar: '../assets/Posts/C1.5.jpg', 
            username: 'paws_user',
            text: text,
            time: 'Vừa xong'
        });

        input.value = ''; 
        updateModalData(currentPostId); 
        
        const container = document.getElementById('modalCommentsContainer');
        container.scrollTop = container.scrollHeight;
    }
  </script>
</body>
</html>