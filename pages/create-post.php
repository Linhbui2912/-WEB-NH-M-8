<!doctype html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PawConnect - Create Post</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../assets/css/styles.css" />
  </head>
  <body>
    <div class="container-fluid px-0">
      <div class="row g-0">
        <aside class="left-sidebar col-2 col-md-1">
          <a
            class="sidebar-logo mb-4"
            href="../index.php"
            data-bs-toggle="tooltip"
            data-bs-title="Trang chủ PawConnect"
          >
            <img
              src="../assets/icon/PawsConnect.png"
              alt="PawConnect Logo"
            />
          </a>
          <nav class="sidebar-nav">
            <a
              href="../index.php"
              class="nav-icon"
              data-bs-toggle="tooltip"
              data-bs-title="Home"
              ><img src="../assets/icon/home_5973558.png" alt="Home"
            /></a>
            <a
              href="search.php"
              class="nav-icon"
              data-bs-toggle="tooltip"
              data-bs-title="Search"
              ><img src="../assets/icon/search.png" alt="Search"
            /></a>
            <a
              href="discover.php"
              class="nav-icon"
              data-bs-toggle="tooltip"
              data-bs-title="Discover"
              ><img
                src="../assets/icon/discovery_12028921.png"
                alt="Discover"
            /></a>
            <!--  data-bs-toggle="modal"
            data-bs-target="#createPostModal" -->
            <a
              href="create-post.php"
              class="nav-icon active"
              data-bs-toggle="modal"
              data-bs-target="#createPostModal"
              ><img src="../assets/icon/add.png" alt="Create"
            /></a>
            <a
              href="profile.php"
              class="nav-icon"
              data-bs-toggle="tooltip"
              data-bs-title="User Account"
              ><img src="../assets/icon/user.png" alt="Account"
            /></a>
          </nav>
          <a
            href="settings.php"
            class="nav-icon settings-icon"
            data-bs-toggle="tooltip"
            data-bs-title="Settings"
            ><img src="../assets//icon/setting.png" alt="Settings"
          /></a>
        </aside>

        <section
          class="col d-flex justify-content-center align-items-center vh-100"
        >
          <!-- Modal -->
          <div class="modal fade" id="createPostModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
              <div class="modal-content rounded-4">
                <!-- Header -->
                <div class="modal-header d-flex align-items-center">
                  <button type="button" class="btn" data-bs-dismiss="modal">
                    <i class="bi bi-arrow-left"></i>
                  </button>
                  <h5 class="modal-title w-100 text-center">Tạo bài viết mới</h5>
                  <form method="post" action="xulybaidang.php" enctype="multipart/form-data" style="width: max-content;"class="d-flex">
                    <button type="submit" class="btn btn-dark ms-auto" style="width:80px">Chia sẻ </button>                   
                </div>

                <!-- Body -->
                <div class="modal-body d-flex flex-wrap">
                  <!-- LEFT: Ảnh -->
                  <div
                    class="w-50 col-12 col-md-6 d-flex align-items-top border-end"
                  >                  
                    <!-- <img
                      src="../assets/Posts/C1.1.jpg"
                      style="width: 100%; height: 100%; object-fit:cover"
                    /> -->  
                    <div class="row"> 
                    <textarea name="noidung" placeholder="Bạn đang nghĩ gì?" required 
                      style="width: 100%; border: none; outline: none; resize: none; min-height: 100px;""></textarea>
                    
                    <!-- Khu vực hiển thị ảnh xem trước -->
                    <div id="preview-container" style="margin: 10px 0px 5px 0px ; position: relative;">
                    <!-- Nút xóa -->                  
                    <button type="button" id="deleteBtn" 
                      style="position: absolute; top: 10px; right: 10px; background-color: white; border: none; padding: 5px 8px; border-radius: 50%; box-shadow: 0 2px 5px rgba(0,0,0,0.2); display: none; z-index: 10; color: #0e0e0e;">
                      <i class="bi bi-trash"></i>
                      </button> 
                                    
                    <img id="imagePreview" src="#" alt="Xem trước ảnh"
                        style="width: 100%; display: none; border: 1px solid #ddd;">

                    <!-- Input chọn ảnh -->
                    <input type="file" name="uploadfile" id="imageInput" accept="image/* "style="width: 100%;display: none;">
                      <label for="imageInput" class="btn d-inline-flex align-items-center gap-2">
                      <i class="bi bi-image"></i> Thêm ảnh/video
                      </label>
                    </div>  
                                            
                    </form>             
                                        
                    </div>  
                                    
                  </div>

                  <!-- RIGHT: Settings -->
                  <div class="w-50 col-12 col-md-6 ps-3">
                    <!-- User -->
                    <div class="container account-item">
                      <div class="row">
                        <div
                          class="left col-9"
                          style="display: flex; align-items: center; gap: 20px"
                        >
                          <img
                            src="../assets/Profile/C1.jpg"
                            style="
                              width: 45px;
                              height: 45px;
                              border-radius: 50%;
                              object-fit: cover;
                            "
                          />
                          <div style="display: flex; flex-direction: column">
                            <span style="font-weight: 600">mieu123</span>
                            <span style="font-size: 13px; color: #8e8e8e">
                              Threads · Riêng tư
                            </span>
                          </div>
                        </div>
                        <div class="col-3 form-check form-switch ps-0">
                          <input class="form-check-input ms-1" type="checkbox" />
                        </div>
                      </div>
                      <div class="row">
                        <div
                          class="left col-9"
                          style="
                            display: flex;
                            align-items: center;
                            gap: 20px;
                            margin-top: 10px;
                          "
                        >
                          <img
                            src="../assets/Profile/C1.jpg"
                            style="
                              width: 45px;
                              height: 45px;
                              border-radius: 50%;
                              object-fit: cover;
                            "
                          />
                          <div style="display: flex; flex-direction: column">
                            <span style="font-weight: 600">mieumieu</span>
                            <span style="font-size: 13px; color: #8e8e8e">
                              Facebook · Riêng tư
                            </span>
                          </div>                          
                        </div>
                        <div class="col-3 form-check form-switch ps-0"">
                            <input
                              class="form-check-input ms-1"
                              type="checkbox"
                            />
                          </div>
                      </div>
                    </div>
                    <!-- Settings -->
                    <div class="container settings">
                      <style>
                        .desc {
                          display: block;
                          font-size: 13px;
                          margin-top: 4px;
                          line-height: 1.4;
                          color: #8e8e8e;
                          padding-right: 40px;
                        }

                        .form-check-input {
                          margin-left: 0;
                          cursor: pointer;
                        }
                      </style>
                      <div
                        class="row ps-0"
                      >
                      <div class ="d-flex justify-content-between align-items-center">
                        <span style="font-weight: 600">Cài đặt nâng cao</span>
                        <i class="bi bi-chevron-down"></i>
                      </div>                        
                      </div>
                      <!-- Item 1 -->
                      <div class="row mb-3">
                        <!-- Cột nội dung -->
                        <div class="col col-9">
                          <label class="row form-check-label d-block ps-0">
                            Ẩn lượt thích và lượt xem trên bài viết này
                          </label>
                        </div>
                        <!-- Cột nút gạt -->
                        <div
                          class="col-3 d-flex align-items-start justify-content-end"
                        >
                          <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" />
                          </div>
                        </div>
                        <!-- Cột nội dung mô tả -->
                        <div class="row">
                          <div class="col-11 p-0">
                            <span class="desc d-block p-0">
                              Số lượt thích và lượt xem trên bài viết của bạn sẽ
                              không hiển thị cho người khác. Bạn vẫn có thể xem
                              tổng số lượt thích trong phần thống kê chi tiết.
                            </span>
                          </div>
                        </div>
                      </div>
                      <!-- Item 2 -->
                      <div class="row mb-3">
                        <div class="col col-9">
                          <label class="row form-check-label d-block ps-0">
                            Tắt tính năng bình luận
                          </label>
                        </div>
                        <!-- Cột nút gạt -->
                        <div
                          class="col col-3 d-flex align-items-start justify-content-end"
                        >
                          <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" />
                          </div>
                        </div>
                        <!-- Cột nội dung mô tả -->
                        <div class="row">
                          <div class="col col-11 p-0">
                            <span class="desc d-block p-0">
                              Người khác sẽ không thể bình luận vào bài viết
                              này. Bạn có thể bật lại bình luận bất kỳ lúc nào
                              sau khi đăng.
                            </span>
                          </div>
                        </div>
                      </div>
                      <!-- Item 3 -->
                      <div class="row mb-3">
                        <div class="col col-9">
                          <label class="row form-check-label d-block ps-0">
                            Tự động chia sẻ lên Threads
                          </label>
                        </div>
                        <!-- Cột nút gạt -->
                        <div
                          class="col col-3 d-flex align-items-start justify-content-end"
                        >
                          <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" />
                          </div>
                        </div>
                        <!-- Cột nội dung mô tả -->
                        <div class="row">
                          <div class="col col-11 p-0">
                            <span class="desc d-block p-0">
                              Bài viết của bạn sẽ được chia sẻ đồng thời lên
                              Threads. Nội dung và hình ảnh sẽ được giữ nguyên
                              khi đăng.
                            </span>
                          </div>
                        </div>
                      </div>
                      <!-- Item 4 -->
                      <div class="row mb-3">
                        <div class="col col-9">
                          <label class="row form-check-label d-block ps-0">
                            Tự động chia sẻ lên Facebook
                          </label>
                        </div>
                        <!-- Cột nút gạt -->
                        <div
                          class="col col-3 d-flex align-items-start justify-content-end"
                        >
                          <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" />
                          </div>
                        </div>
                        <!-- Cột nội dung mô tả -->
                        <div class="row">
                          <div class="col col-11 p-0">
                            <span class="desc d-block p-0">
                              Bài viết sẽ được đồng bộ lên tài khoản Facebook đã
                              liên kết. Bạn có thể quản lý quyền riêng tư trong
                              cài đặt Facebook.
                            </span>
                          </div>                          
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>    
    <script>
    // 1. Lấy các phần tử HTML cần thiết
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');

    const deleteBtn = document.getElementById('deleteBtn');

    // 2. Lắng nghe sự kiện 'change' (khi người dùng chọn file xong)
    imageInput.addEventListener('change', function() {
        const file = this.files[0]; // Lấy file đầu tiên trong danh sách chọn


        if (file) {
            // 3. Sử dụng đối tượng FileReader để đọc nội dung file
            const reader = new FileReader();


            // 4. Định nghĩa hành động khi đọc file hoàn tất
            reader.onload = function(e) {
                imagePreview.src = e.target.result; // Gán dữ liệu ảnh vào thuộc tính src
                imagePreview.style.display = 'block'; // Hiện thẻ img lên
                deleteBtn.style.display = 'block'; //Hiện nút xóa khi ảnh đã load xong
            }


            // 5. Bắt đầu đọc file dưới dạng đường dẫn URL tạm thời
            reader.readAsDataURL(file);
        } else {
            // Nếu người dùng hủy chọn file, ẩn ảnh xem trước
            imagePreview.style.display = 'none';
            imagePreview.src = "#";
            deleteBtn.style.display = 'none'; //Ẩn nút xóa nếu người dùng hủy chọn
        }
      });
        // Sự kiện click cho nút xóa ảnh
          deleteBtn.addEventListener('click', function(e) {
              e.preventDefault(); // Ngăn chặn form bị submit nhầm
              
              imagePreview.style.display = 'none'; // Ẩn ảnh đi
              imagePreview.src = "#";             // Reset đường dẫn ảnh về rỗng
              imageInput.value = "";              // Xóa dữ liệu file trong input (để chọn lại chính ảnh đó vẫn ăn)
              deleteBtn.style.display = 'none';   // Tự ẩn nút xóa đi
          });
</script>

  </body>
</html>
