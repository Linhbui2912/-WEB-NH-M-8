<!doctype html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PawConnect - Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <?php
                      if (isset($_GET['msg'])) {
                          if ($_GET['msg'] == "done") {
                        echo '<div style="position: fixed; top: 20px; right: 20px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 15px;">
                        <b>Thành công!</b> Bài viết của bạn đã được đăng.
                        </div>';
                      } elseif ($_GET['msg'] == "error") {
                      echo '<div style="position: fixed; top: 20px; right: 20px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; margin-bottom: 15px;">
                      <b>Lỗi!</b> Không thể lưu bài đăng, vui lòng thử lại.
                      </div>';
                    }
                    }
                    ?>
  <div class="container-fluid px-0">
    <div class="row g-0">
    <aside class="left-sidebar col-2 col-md-1">
      <a class="sidebar-logo mb-4" href="index.php" data-bs-toggle="tooltip" data-bs-title="Trang chủ PawConnect">
        <img src="assets/SOURCE IMAGES/PawsConnect.png" alt="PawConnect Logo">
      </a>

      <nav class="sidebar-nav">
        <a href="index.php" class="nav-icon active" data-bs-toggle="tooltip" data-bs-title="Home">
          <img src="assets/SOURCE IMAGES/home_5973558.png" alt="Home">
        </a>
        <a href="pages/search.php" class="nav-icon" data-bs-toggle="tooltip" data-bs-title="Search">
          <img src="assets/SOURCE IMAGES/search.png" alt="Search">
        </a>
        <a href="pages/discover.php" class="nav-icon" data-bs-toggle="tooltip" data-bs-title="Discover">
          <img src="assets/SOURCE IMAGES/discovery_12028921.png" alt="Discover">
        </a>
        <a href="pages/create-post.php" class="nav-icon" data-bs-toggle="tooltip" data-bs-title="Create New Post">
          <img src="assets/SOURCE IMAGES/add.png" alt="Create Post">
        </a>
        <a href="pages/profile.php" class="nav-icon" data-bs-toggle="tooltip" data-bs-title="User Account">
          <img src="assets/SOURCE IMAGES/user.png" alt="Account">
        </a>
      </nav>

      <a href="pages/settings.php" class="nav-icon settings-icon" data-bs-toggle="tooltip" data-bs-title="Settings">
        <img src="assets/SOURCE IMAGES/setting.png" alt="Settings">
      </a>
    </aside>

    <main class="feed-wrapper col">
      <section class="feed-column">
        <article class="post-card" data-post-id="post-1">
          <header class="post-header">
            <img class="avatar" src="./assets/Profile/C1.jpg" alt="Avatar">
            <div>
              <h2 class="username">Meobeo_3123 🐾</h2>
              <p class="post-time">2 ngày trước</p>
            </div>
            <button class="btn post-more" aria-label="More">•••</button>
          </header>

          <img class="post-image" src="./assets/Posts/C1.1.jpg" alt="Bài đăng thú cưng">

          <div class="post-actions">
            <button class="icon-btn paw-like-btn" data-liked="false" aria-label="Like post">
              <img src="assets/SOURCE IMAGES/footprint.png" data-icon-white="assets/SOURCE IMAGES/footprint.png" data-icon-liked="assets/SOURCE IMAGES/pawheart.png" alt="Like">
            </button>
            <button class="icon-btn open-comments-btn" data-post-id="post-1" data-bs-toggle="modal" data-bs-target="#commentsModal" aria-label="Comment">
              <img src="assets/SOURCE IMAGES/message.png" alt="Comment">
            </button>
            <button class="icon-btn" aria-label="Share">
              <img src="assets/SOURCE IMAGES/share.png" alt="Share">
            </button>
          </div>

          <div class="post-caption">
            <p class="mb-1"><strong>Meobeo_3123 🐾</strong></p>
            <p class="mb-0">With decades of maintenance of way expertise and experience, no one knows...</p>
          </div>
        </article>

        <article class="post-card" data-post-id="post-2">
          <header class="post-header">
            <img class="avatar" src="assets/SOURCE IMAGES/inu_avatar.jpg" alt="Avatar">
            <div>
              <h2 class="username">InuShiba_lalala</h2>
              <p class="post-time">4 giờ trước</p>
            </div>
            <button class="btn post-more" aria-label="More">•••</button>
          </header>

          <img class="post-image" src="assets/SOURCE IMAGES/inushibademo.jpg" alt="Bài đăng thú cưng">

          <div class="post-actions">
            <button class="icon-btn paw-like-btn" data-liked="false" aria-label="Like post">
              <img src="assets/SOURCE IMAGES/footprint.png" data-icon-white="assets/SOURCE IMAGES/footprint.png" data-icon-liked="assets/SOURCE IMAGES/pawheart.png" alt="Like">
            </button>
            <button class="icon-btn open-comments-btn" data-post-id="post-2" data-bs-toggle="modal" data-bs-target="#commentsModal" aria-label="Comment">
              <img src="assets/SOURCE IMAGES/message.png" alt="Comment">
            </button>
            <button class="icon-btn" aria-label="Share">
              <img src="assets/SOURCE IMAGES/share.png" alt="Share">
            </button>
          </div>

          <div class="post-caption">
            <p class="mb-1"><strong>InuShiba_lalala 🐾</strong></p>
            <p class="mb-0">With decades of maintenance of way expertise and experience, no one knows...</p>
          </div>
        </article>
      </section>
    </main>
    </div>
  </div>

  <div class="modal fade" id="commentsModal" tabindex="-1" aria-labelledby="commentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content comments-modal">
        <div class="modal-header">
          <h5 class="modal-title" id="commentsModalLabel">Bình luận</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <ul id="commentsList" class="comments-list"></ul>
        </div>
        <div class="modal-footer comment-input-wrap">
          <input id="commentInput" type="text" class="form-control" placeholder="Viết bình luận...">
          <button id="submitCommentBtn" type="button" class="btn btn-dark">Đăng</button>
        </div>
      </div>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>
