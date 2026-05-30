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
<div class="modal fade" id="editPostModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-4">
      <!-- Header -->
      <form id="editPostForm" method="post" action="../controllers/editpost.php" enctype="multipart/form-data" autocomplete="off"> 
        <input type="hidden" name="ma_bai_dang" id="editPostId">

        <!-- BỔ SUNG DÒNG NÀY: Giá trị 0 nghĩa là giữ ảnh, 1 là người dùng bấm xóa ảnh -->
        <input type="hidden" name="xoa_anh_cu" id="editDeleteImageInput" value="0">
            <div class="modal-header d-flex align-items-center">
          <button type="button" class="btn" data-bs-dismiss="modal">
            <i class="bi bi-arrow-left"></i>
          </button>
          <h5 class="modal-title w-100 text-center" id="editPostModalLabel">Chỉnh sửa bài viết</h5>
          <button type="submit" class="btn btn-dark ms-auto" style="width:120px">Cập nhật</button> 
        </div>
        <!-- Body -->
        <div class="modal-body row g-0">
          <!-- LEFT: Ảnh -->
          <div class="col-12 col-md-6 pe-md-4 border-end d-flex flex-column gap-2"> 
            <textarea name="noidung" id="editPostContent" placeholder="Bạn đang nghĩ gì?" required 
                      style="width: 100%; border: none; outline: none; resize: none; min-height: 100px;"></textarea>
              <!-- Khu vực hiển thị ảnh xem trước -->
            <div id="edit-preview-container" style="position: relative; width: 100%;">   
              <!-- Nút xóa -->            
              <button type="button" id="editDeleteBtn" 
                      style="position: absolute; top: 10px; right: 10px; background-color: white; border: none; padding: 5px 8px; border-radius: 50%; box-shadow: 0 2px 5px rgba(0,0,0,0.2); display: none; z-index: 30; color: #0e0e0e;">
                <i class="bi bi-trash"></i>
              </button>
              
              <style>
                 /* Class này sẽ chỉ kích hoạt màu nền khi có ảnh */
                .carousel-inner.edit-has-images {
                  background-color: #151515;
                }
                 /* Xử lý ảnh ôm khít khung và nút control tự động chuẩn theo */
                .carousel-item img {
                  display: block; 
                  margin: 0 auto; 
                  height: 400px; 
                  object-fit: contain;
                  object-position: center; 
                }
              </style> 
              
              <div id="editImageCarouselPreview" class="carousel slide" data-bs-ride="carousel" style="display: none;">
                <!-- The slideshow-->
              <div class="carousel-inner" id="editCarouselPreviewItems"></div>
                <!--Left and right controls -->
                <a class="carousel-control-prev" href="#editImageCarouselPreview" data-bs-slide="prev">
                  <span class="carousel-control-prev-icon"></span>
                </a>
                <a class="carousel-control-next" href="#editImageCarouselPreview" data-bs-slide="next">
                  <span class="carousel-control-next-icon"></span>
                </a>
              </div>
            </div>
            <!-- Input chọn ảnh -->
            <div class="mt-2">
              <input type="file" name="uploadfile[]" id="editImageInput" accept="image/*" multiple style="display: none;">
              <label for="editImageInput" class="btn d-inline-flex align-items-center gap-2 class-upload-btn" style="cursor: pointer; padding-left: 0;">
                <i class="bi bi-image text-primary"></i> <strong>Thêm ảnh/video mới</strong>
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
                <div id="edit-user-profile-container">
                  <span class="text-muted" style="font-size: 13px;">Đang tải thông tin...</span>
                </div>
                
                <div class="dropdown">
                  <button class="btn btn-sm privacy-btn border dropdown-toggle d-flex align-items-center gap-2" type="button" id="editPrivacyDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-globe2 text-secondary"></i> <span id="edit-selected-privacy-text">Công khai</span>
                  </button>
                  
                  <input type="hidden" name="quyen_rieng_tu" id="editPrivacyInput" value="public">

                  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="editPrivacyDropdown" style="font-size: 13px; min-width: 140px;">
                    <li>
                      <a class="dropdown-item edit-privacy-item d-flex align-items-center gap-2 active" href="#" data-value="public" data-icon="bi-globe2">
                        <i class="bi bi-globe2 text-secondary"></i> Công khai
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item edit-privacy-item d-flex align-items-center gap-2" href="#" data-value="friends" data-icon="bi-people">
                        <i class="bi bi-people text-secondary"></i> Bạn bè
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item edit-privacy-item d-flex align-items-center gap-2" href="#" data-value="private" data-icon="bi-lock">
                        <i class="bi bi-lock text-secondary"></i> Chỉ mình tôi
                      </a>
                    </li>
                  </ul>
                </div>
              </div>

              <div class="py-4 text-muted" style="font-size: 13px; line-height: 1.5; font-style: italic;">
                Bài viết sau khi chỉnh sửa sẽ cập nhật thay đổi ngay lập tức trên bảng tin hệ thống.
              </div>
            </div>

          </div> </div> </form>
    </div>
  </div>
</div>
  </body>
</html>
<script>
document.addEventListener("DOMContentLoaded", function() { 
    const editPostModal = document.getElementById('editPostModal');
    
    if (editPostModal) {
        // Lấy các thành phần giao diện liên quan đến ảnh và nút xóa
        const deleteBtn = document.getElementById('editDeleteBtn');
        const carouselContainer = document.getElementById('editImageCarouselPreview');
        const carouselInner = document.getElementById('editCarouselPreviewItems');
        const deleteImageInput = document.getElementById('editDeleteImageInput');

        // --- 1. XỬ LÝ KHI MỞ MODAL (Tải profile tác giả) ---
        editPostModal.addEventListener('show.bs.modal', function () {
            // Khi mở bài viết mới, reset trạng thái xóa ảnh về 0 (Chưa xóa)
            if (deleteImageInput) deleteImageInput.value = "0";

            // Đặt lại trạng thái chờ mỗi lần mở modal
            document.getElementById('edit-user-profile-container').innerHTML = '<span class="text-muted" style="font-size: 13px;">Đang tải thông tin...</span>';

            // Khởi tạo đối tượng yêu cầu chạy ngầm
            const xhr = new XMLHttpRequest();
            xhr.open('GET', '../controllers/displaycreatepost.php', true);
            
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        document.getElementById('edit-user-profile-container').innerHTML = xhr.responseText;
                    } else {
                        console.error('Lỗi kết nối Controller. Mã trạng thái:', xhr.status);
                        document.getElementById('edit-user-profile-container').innerHTML = '<span>Lỗi tải thông tin tác giả</span>';
                    }
                }
            };
            xhr.send();
        });
         // ---2. Xử lý Dropdown trạng thái hiển thị quyền riêng tư cho Edit Modal
          const editPrivacyItems = document.querySelectorAll('.edit-privacy-item');
          const editPrivacyBtn = document.getElementById('editPrivacyDropdown');
          const editPrivacyInput = document.getElementById('editPrivacyInput');
          
          if (editPrivacyItems.length > 0 && editPrivacyBtn) {
              editPrivacyItems.forEach(item => {
                  item.addEventListener('click', function(e) {
                      e.preventDefault();                
                      // Cập nhật trạng thái hiển thị Active trong menu
                      editPrivacyItems.forEach(i => i.classList.remove('active'));
                      this.classList.add('active');                
                      // Lấy các giá trị thuộc tính cấu hình từ Item được chọn
                      const value = this.getAttribute('data-value');
                      const iconClass = this.getAttribute('data-icon');
                      const text = this.innerText.trim();                
                      // Thay đổi nội dung và icon hiển thị trên mặt nút Button chính
                      editPrivacyBtn.innerHTML = `<i class="bi ${iconClass} text-secondary"></i> <span>${text}</span>`;
                      // Đồng bộ giá trị vào thẻ input hidden
                      if (editPrivacyInput) {
                          editPrivacyInput.value = value;
                      }
                  });
              });
          }  
          // --- BỔ SUNG: XỬ LÝ XEM TRƯỚC ẢNH KHI NGƯỜI DÙNG CHỌN FILE MỚI ---
        const editImageInput = document.getElementById('editImageInput');        
        if (editImageInput) {
            editImageInput.addEventListener('change', function() {
                const files = this.files;
                
                // Nếu có chọn file
                if (files && files.length > 0) {
                    if (carouselInner) {
                        carouselInner.innerHTML = ''; // Xóa ảnh cũ đang hiển thị (nếu có)
                        carouselInner.classList.add('edit-has-images'); // Thêm nền tối cho Carousel
                    }
                    
                    // Duyệt qua từng file được chọn và dùng FileReader để đọc dữ liệu ảnh
                    Array.from(files).forEach((file, index) => {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            // Tạo khối item cho Carousel của Bootstrap, ảnh đầu tiên phải có class 'active'
                            const isActive = index === 0 ? 'active' : '';
                            const carouselItem = `
                                <div class="carousel-item ${isActive}">
                                    <img src="${e.target.result}" class="d-block w-100" alt="Preview Image">
                                </div>
                            `;
                            if (carouselInner) {
                                carouselInner.insertAdjacentHTML('beforeend', carouselItem);
                            }
                        };
                        
                        reader.readAsDataURL(file); // Kích hoạt đọc file
                    });
                    
                    // Hiện khung Carousel và nút xóa (thùng rác) lên
                    if (carouselContainer) carouselContainer.style.display = 'block';
                    if (deleteBtn) deleteBtn.style.display = 'block';
                    
                    // Đặt lại cờ xóa ảnh cũ về "0" vì người dùng vừa tải ảnh mới lên
                    if (deleteImageInput) deleteImageInput.value = "0";
                }
            });
        }
        // --- 3. XỬ LÝ KHI CLICK NÚT THÙNG RÁC (Xóa ảnh) ---
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function () {
                // Hiển thị hộp thoại xác nhận nhỏ cho chắc chắn
                if (confirm('Bạn có chắc chắn muốn xóa hình ảnh này khỏi bài viết không?')) {
                    
                    // Bước A: Ẩn và xóa sạch ruột Carousel ảnh trên modal giao diện công việc
                    if (carouselContainer) carouselContainer.style.display = 'none';
                    if (carouselInner) {
                        carouselInner.innerHTML = '';
                        carouselInner.classList.remove('edit-has-images');
                    }
                    
                    // Bước B: Ẩn chính chiếc nút thùng rác này đi
                    deleteBtn.style.display = 'none';
                    
                    // Bước C: Bật cờ trạng thái lên 1 để thông báo cho file editpost.php biết là cần xóa ảnh cũ
                    if (deleteImageInput) {
                        deleteImageInput.value = "1";
                    }
                }
            });
        }
    }
});
</script>
