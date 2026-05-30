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
            href="homepage.php"
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
              href="homepage.php"
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
              href="../controllers/displaycreatepost.php"
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
              <form method="post" action="../controllers/xulybaidang.php" enctype="multipart/form-data" autocomplete="off">                  
                  <div class="modal-header d-flex align-items-center">
                    <button type="button" class="btn" data-bs-dismiss="modal">
                      <i class="bi bi-arrow-left"></i>
                    </button>
                    <h5 class="modal-title w-100 text-center">Tạo bài viết mới</h5>
                    <button type="submit" class="btn btn-dark ms-auto" style="width:100px">Chia sẻ</button> 
                  </div>
                <!-- Body -->
                <div class="modal-body row g-0">
                  <!-- LEFT: Ảnh -->
                  <div class="col-12 col-md-6 pe-md-4 border-end d-flex flex-column gap-2">                 
                  <textarea name="noidung" placeholder="Bạn đang nghĩ gì?" required 
                        style="width: 100%; border: none; outline: none; resize: none; min-height: 100px;"></textarea>
                    
                    <!-- Khu vực hiển thị ảnh xem trước -->
                    <div id="preview-container" style="position: relative; width: 100%;">
                    <!-- Nút xóa -->                  
                    <button type="button" id="deleteBtn" 
                          style="position: absolute; top: 10px; right: 10px; background-color: white; border: none; padding: 5px 8px; border-radius: 50%; box-shadow: 0 2px 5px rgba(0,0,0,0.2); display: none; z-index: 30; color: #0e0e0e;">
                          <i class="bi bi-trash"></i>
                    </button>
                      <style>
                        /* Class này sẽ chỉ kích hoạt màu nền khi có ảnh */
                        .carousel-inner.has-images {
                        background-color: #151515;
                        }
                        /* Xử lý ảnh ôm khít khung và nút control tự động chuẩn theo */
                        .carousel-item img, .carousel-item video {
                          display: block; /* Bắt buộc phải có để margin: 0 auto hoạt động */
                          margin: 0 auto; /* Căn giữa ảnh vào trong khung .carousel-inner */
                          height: 400px; /* Bắt buộc bằng height của .carousel-inner */
                          /* Nếu dùng margin auto, hãy đổi cover thành contain để ảnh không bị mất góc */
                          object-fit: contain;
                          object-position: center; /* Luôn lấy tâm bức ảnh làm trung tâm */
                        }
                      </style>             
                      <div id="imageCarouselPreview" class="carousel slide" data-bs-ride="carousel" style="display: none;">
                            <!-- The slideshow-->
                            <div class="carousel-inner" id="carouselPreviewItems"></div>
                            <!--Left and right controls -->
                            <a class="carousel-control-prev" href="#imageCarouselPreview" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                            </a>
                            <a class="carousel-control-next" href="#imageCarouselPreview" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                            </a>
                    </div>
                  </div>
                    <!-- Input chọn ảnh -->
                    <div class="mt-2">
                        <input type="file" name="uploadfile[]" id="imageInput" accept="image/* , video/*" multiple style="display: none;">
                        <label for="imageInput" class="btn d-inline-flex align-items-center gap-2 class-upload-btn" style="cursor: pointer; padding-left: 0;">
                          <i class="bi bi-image text-primary"></i> <strong>Thêm ảnh/video</strong>
                        </label>
                      </div>
                    </div>
                  <!-- RIGHT: Settings -->
            <div class="col-12 col-md-6 ps-md-4 d-flex flex-column justify-content-between layout-right mt-3 mt-md-0">  
            <style>
              .layout-right {
                max-height: 480px; /* Khống chế chiều cao bằng vùng ảnh bên trái */
                overflow-y: auto;
                padding-right: 8px;
              }
              /* Thanh cuộn nhỏ gọn hiện đại */
              .layout-right::-webkit-scrollbar {
                width: 4px;
              }
              .layout-right::-webkit-scrollbar-thumb {
                background: #e6e6e6;
                border-radius: 10px;
              }
              /* Style nút dropdown quyền riêng tư */
              .privacy-btn {
                font-size: 12px;
                font-weight: 500;
                color: #4f4f4f;
                padding: 5px 12px;
                background-color: #f8f9fa;
                transition: all 0.2s;
              }
              .privacy-btn:hover {
                background-color: #eaeaea;
              }
              /* Làm sạch Accordion Bootstrap */
              .clean-accordion .accordion-item {
                border: none;
                background: transparent;
              }
              .clean-accordion .accordion-button {
                padding: 16px 0;
                font-size: 14px;
                font-weight: 600;
                color: #262626;
                background-color: transparent !important;
                box-shadow: none !important;
                border-bottom: 1px solid #efefef;
              }
              .clean-accordion .accordion-button:not(.collapsed)::after {
                transform: rotate(-180deg);
              }
              /* Định dạng switch */
              .switch-title {
                font-size: 14px;
                font-weight: 500;
                color: #262626;
                margin-bottom: 2px;
                cursor: pointer;
              }
              .switch-desc {
                font-size: 12px;
                color: #737373;
                line-height: 1.4;
                display: block;
              }
              .form-check-input:checked {
                background-color: #000000;
                border-color: #000000;
              }
              .form-check-input:focus {
                border-color: #dbdbdb;
                box-shadow: none;
              }
            </style>

          <div>
      <div class="d-flex align-items-center justify-content-between pb-3 border-bottom">
        <div id="user-profile-container">
        <span class="text-muted" style="font-size: 13px;">Đang tải thông tin...</span>
      </div>
      
      <div class="dropdown">
                            <button class="btn btn-sm privacy-btn border dropdown-toggle d-flex align-items-center gap-2" type="button" id="privacyDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                              <i class="bi bi-globe2 text-secondary"></i> <span id="selected-privacy-text">Công khai</span>
                            </button>
                            
                            <input type="hidden" name="quyen_rieng_tu" id="privacyInput" value="public">

                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="privacyDropdown" style="font-size: 13px; min-width: 140px;">
                              <li>
                                <a class="dropdown-item dropdown-privacy-item d-flex align-items-center gap-2 active" href="#" data-value="public" data-icon="bi-globe2">
                                  <i class="bi bi-globe2 text-secondary"></i> Công khai
                                </a>
                              </li>
                              <li>
                                <a class="dropdown-item dropdown-privacy-item d-flex align-items-center gap-2" href="#" data-value="friends" data-icon="bi-people">
                                  <i class="bi bi-people text-secondary"></i> Bạn bè
                                </a>
                              </li>
                              <li>
                                <a class="dropdown-item dropdown-privacy-item d-flex align-items-center gap-2" href="#" data-value="private" data-icon="bi-lock">
                                  <i class="bi bi-lock text-secondary"></i> Chỉ mình tôi
                                </a>
                              </li>
                            </ul>
                          </div>
                        </div>
    
    <div class="py-4 text-muted" style="font-size: 13px; line-height: 1.5; font-style: italic;">
                          Bài viết sau khi chia sẻ sẽ xuất hiện dựa trên cấu hình quyền riêng tư bạn đã chọn.
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
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
  
    // Gọi 2 phần tử của Carousel:
    const imageCarouselPreview = document.getElementById('imageCarouselPreview');   
    const carouselPreviewItems = document.getElementById('carouselPreviewItems');   //Nơi chứa ảnh

    const deleteBtn = document.getElementById('deleteBtn'); //// Nút thùng rác lớn (Xóa tất cả)
      //Biến mảng tạm để lưu trữ các file ảnh thực tế
        let selectedFiles = [];

    // 2. Lắng nghe sự kiện 'change' (khi người dùng chọn file xong)
    imageInput.addEventListener('change', function() {
        // Lấy toàn bộ danh sách file dưới dạng mảng
        const files = this.files; 
      // Đồng thời mã mới sẽ reset vùng chứa để dọn sạch ảnh cũ.
        carouselPreviewItems.innerHTML = '';         

        // Kiểm tra xem mảng có ảnh hay không
        if (files && files.length > 0) {
          // Gộp các file mới chọn vào mảng selectedFiles hiện tại
          // Việc này giúp người dùng bấm chọn ảnh nhiều lần mà không bị mất các ảnh đã chọn trước đó
            Array.from(files).forEach(file => {
                selectedFiles.push(file);
            });
            // Gọi hàm render lại giao diện Carousel dựa trên mảng selectedFiles
            renderCarousel();
          } 
});
        // 3. Hàm renderCarousel dựa trên mảng file tạm
        function renderCarousel() {
          // Reset sạch dải slide cũ trước khi vẽ giao diện mới
        carouselPreviewItems.innerHTML = ''; 

        // Nếu mảng trống (không có ảnh nào), tiến hành ẩn khung preview
        if (selectedFiles.length === 0) {
            resetPreview();
            return;
        }
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => {
        dataTransfer.items.add(file);
        });
        imageInput.files = dataTransfer.files; // Cập nhật lại danh sách file thực tế trong input gửi đi
        
        let loadedCount = 0;

       // Duyệt qua mảng biến tạm selectedFiles thay vì 'this.files' của input
        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Tạo thẻ div bọc item theo chuẩn Bootstrap (Ảnh đầu tiên phải có class 'active')
                const carouselItem = document.createElement('div');
                carouselItem.classList.add('carousel-item');
                if (index === 0) carouselItem.classList.add('active'); // Ảnh đầu tiên luôn active

                // Kiểm tra xem file có phải là video không bằng cách check type
                if (file.type.startsWith('video/')) {
                    const video = document.createElement('video');
                    video.src = e.target.result;
                    video.controls = true; // Hiện thanh điều khiển phát/tạm dừng
                    video.muted = true;    // Tắt tiếng để tránh gây phiền khi xem trước
                    carouselItem.appendChild(video);
                } else {
                // Tạo thẻ img hiển thị ảnh
                const img = document.createElement('img');
                img.src = e.target.result;
                carouselItem.appendChild(img);}
                // NÂNG CẤP MỚI: Tự động sinh ra nút "Xóa lẻ" (Dấu X nhỏ) nằm góc trên bên phải của TỪNG TẤM ẢNH
                const closeBtn = document.createElement('button');
                closeBtn.type = 'button';
                closeBtn.innerHTML = '<i class="bi bi-x-lg"></i>'; // Icon dấu X của Bootstrap Icons
                // Chỉnh CSS trực tiếp cho nút X nhỏ nổi lên trên tấm ảnh hiện tại
                closeBtn.style = "position: absolute; top: 10px; left: 10px; background: rgba(0,0,0,0.6); color: white; border: none; border-radius: 4px; padding: 4px 8px; z-index: 20; font-size: 12px; cursor: pointer;";
                // NÂNG CẤP MỚI: Bắt sự kiện xóa lẻ khi click vào dấu X này
                closeBtn.addEventListener('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation(); // Ngăn chặn sự kiện click làm trượt slide lung tung

                    // Xóa đúng 1 phần tử tại vị trí 'index' ra khỏi mảng biến tạm
                    selectedFiles.splice(index, 1);

                    // Vẽ lại giao diện sau khi đã xóa tấm ảnh đó
                    renderCarousel();
                });
                
                // Cắm nút X nhỏ vào khung chứa ảnh
                carouselItem.appendChild(closeBtn);
                // Cắm toàn bộ khối ảnh + nút X vào dải slide Carousel
                carouselPreviewItems.appendChild(carouselItem);
                
                loadedCount++;
                // Khi đã đọc xong tất cả các ảnh thì mới hiển thị khung lên
                if (loadedCount === selectedFiles.length) {
                    imageCarouselPreview.style.display = 'block'; 
                    deleteBtn.style.display = 'block'; // Hiện nút xóa tất cả (thùng rác lớn)

                    // Thêm class để kích hoạt background màu tối
                    carouselPreviewItems.classList.add('has-images');
                }
            }
            reader.readAsDataURL(file);
        });
        }
        // 4. Sự kiện click cho nút xóa ảnh (xóa toàn bộ album)
          deleteBtn.addEventListener('click', function(e) {
              e.preventDefault(); // Ngăn chặn form bị submit nhầm
              // NÂNG CẤP MỚI: Làm rỗng mảng biến tạm trước, sau đó gọi hàm reset
              selectedFiles = [];
              resetPreview();             
          });
        // 5. Hàm reset toàn bộ giao diện về trạng thái trống
          function resetPreview() {
           imageCarouselPreview.style.display = 'none'; // Ẩn ảnh đi
           carouselPreviewItems.innerHTML = '';         // Reset đường dẫn ảnh về rỗng
           imageInput.value = "";              // Xóa dữ liệu file trong input (để chọn lại chính ảnh đó vẫn ăn)
           deleteBtn.style.display = 'none';   // Tự ẩn nút xóa đi
          carouselPreviewItems.classList.remove('has-images');
          }  
    
    document.addEventListener("DOMContentLoaded", function() { 
    // Xử lý XMLHttpRequest tải thông tin hồ sơ khi mở Modal
    const createPostModal = document.getElementById('createPostModal');
          if (createPostModal) {
              createPostModal.addEventListener('show.bs.modal', function () {
                  // Khởi tạo đối tượng yêu cầu chạy ngầm
                  const xhr = new XMLHttpRequest();
                  
                  // Thiết lập gửi yêu cầu dạng GET đến file Controller
                  xhr.open('GET', '../controllers/displaycreatepost.php', true);
                  
                  // Lắng nghe phản hồi từ Server
                  xhr.onreadystatechange = function () {
                      if (xhr.readyState === 4) { // Khi request hoàn thành xong xuôi
                          if (xhr.status === 200) { // Nếu kết nối thành công tốt đẹp
                              // Đổ nguyên khối XHTML nhận được vào cái hộp container bên phải modal
                              document.getElementById('user-profile-container').innerHTML = xhr.responseText;
                          } else {
                              console.error('Lỗi kết nối Controller. Mã trạng thái:', xhr.status);
                              document.getElementById('user-profile-container').innerHTML = '<span>Lỗi tải thông tin tác giả</span>';
                          }
                      }
                  };
                  
                  // Thực hiện bắn request đi ngầm dưới nền
                  xhr.send();
              });
          }
      // Xử lý Dropdown trạng thái hiển thị bài đăng 
    const privacyItems = document.querySelectorAll('.dropdown-privacy-item');
    const privacyBtn = document.getElementById('privacyDropdown');
    const privacyInput = document.getElementById('privacyInput');
    if (privacyItems.length > 0 && privacyBtn) {
        privacyItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();                
                // Cập nhật trạng thái hiển thị Active trong menu
                privacyItems.forEach(i => i.classList.remove('active'));
                this.classList.add('active');                
                // Lấy các giá trị thuộc tính cấu hình từ Item được chọn
                const value = this.getAttribute('data-value');
                const iconClass = this.getAttribute('data-icon');
                const text = this.innerText.trim();                
                // Thay đổi nội dung và icon hiển thị trên mặt nút Button chính
                privacyBtn.innerHTML = `<i class="bi ${iconClass} text-secondary"></i> <span>${text}</span>`;
                // Đồng bộ giá trị vào thẻ input hidden để khi SUBMIT FORM, PHP sẽ nhận được $_POST['quyen_rieng_tu']
                if (privacyInput) {
                    privacyInput.value = value;
                    console.log("Quyền riêng tư đã đổi thành:", value);
                }
            });
        });
    }  
});    
</script>
  </body>
</html>
