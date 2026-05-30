<?php
session_start();
// 1. Nhúng các module quản lý dữ liệu giống hệt bên createPost
require_once "../models/post_module.php";
require_once "../modules/db_module.php";

class EditPostController {    
    
    public function editPost() {
        // Kiểm tra xem form Chỉnh sửa có gửi mã bài đăng lên không
        if (isset($_POST['ma_bai_dang'])) {
            $link = null;
            taoKetNoi($link);

            // 2. Lấy dữ liệu từ Form Modal Sửa gửi lên
            $maBaiDangVuaTao = $_POST['ma_bai_dang']; // Giữ tên biến trùng với ID bài viết để xử lý khóa ngoại
            $noidung         = $_POST['noidung'];
            $xoa_anh_cu      = isset($_POST['xoa_anh_cu']) ? $_POST['xoa_anh_cu'] : '0'; // Nhận cờ hiệu từ nút thùng rác (0 hoặc 1)
            $maNguoiDung     = $_SESSION['maNguoiDung']; 

            // 3. Xử lý chuẩn hóa Quyền riêng tư sang định dạng lưu Database
            $quyen_rieng_tu = isset($_POST['quyen_rieng_tu']) ? $_POST['quyen_rieng_tu'] : 'public';
            $cheDoHienThi = 'cong_khai'; 
            
            switch ($quyen_rieng_tu) {      
                case 'friends':
                    $cheDoHienThi = 'ban_be';
                    break;
                case 'private':
                    $cheDoHienThi = 'chi_minh_toi';
                    break;
                case 'public':
                default:
                    $cheDoHienThi = 'cong_khai'; 
                    break;
            }

            // 4. Khởi tạo Model để tương tác với các hàm cập nhật/xóa
            $postModel = new PostModel();

            // Đóng gói dữ liệu văn bản vào thực thể BaiDang (truyền ID bài viết vào tham số đầu tiên)
            $editPost = new BaiDang($maBaiDangVuaTao, $maNguoiDung, $noidung);
            $editPost->cheDoHienThi = $cheDoHienThi;

            // Gọi hàm cập nhật nội dung bài viết (Bạn nhớ bổ sung hàm updatePost này trong post_module.php nếu chưa có nhé)
            $isUpdated = $postModel->updatePost($link, $editPost);

            if ($isUpdated) {
            // 5. XỬ LÝ XÓA ẢNH CŨ
            // Điều kiện mới: Nếu người dùng yêu cầu xóa HOẶC họ có upload file mới lên
            $co_file_moi = (isset($_FILES['uploadfile']) && !empty($_FILES['uploadfile']['name'][0]));

            if ($xoa_anh_cu == '1' || $co_file_moi) {
                // Gọi hàm xóa toàn bộ phương tiện cũ gắn với mã bài đăng này
                $postModel->deleteMedia($link, $maBaiDangVuaTao);
            }

                // 6. XỬ LÝ ALBUM ẢNH / VIDEO MỚI CHỌN THÊM (Nếu có)
                $mangDuongDan = []; 
                if (isset($_FILES['uploadfile']) && !empty($_FILES['uploadfile']['name'][0])) {
                    $target_dir = "../assets/uploads/";
                    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                    
                    $total_files = count($_FILES['uploadfile']['name']);
                    for ($i = 0; $i < $total_files; $i++) {
                        if ($_FILES['uploadfile']['error'][$i] == 0) {
                            
                            $file_name = time() . "_" . $i . "_" . basename($_FILES["uploadfile"]["name"][$i]);
                            $target_file = $target_dir . $file_name;
                            
                            if (move_uploaded_file($_FILES["uploadfile"]["tmp_name"][$i], $target_file)) {
                                $file_type = $_FILES['uploadfile']['type'][$i];
                                $loaiMedia = (strpos($file_type, 'video') !== false) ? 'video' : 'image';

                                $mangDuongDan[] = [
                                    'path' => $target_file,
                                    'type' => $loaiMedia
                                ];
                            }
                        }        
                    }
                }

                // 7. Chèn đống ảnh/video mới chọn thêm vào DB (nếu có)
                if (count($mangDuongDan) > 0) {
                    foreach ($mangDuongDan as $item) {
                        $newMedia = new PhuongTien(null, $maBaiDangVuaTao, $maNguoiDung, $item['path'], $item['type']);
                        $postModel->insertMedia($link, $newMedia);
                    }
                }

                $status = "updated"; // Trạng thái cập nhật thành công
            } else {
                $status = "error"; // Trạng thái thất bại
            }

            giaiPhongBoNho($link, null);      
            
           // Chuyển hướng về file trung gian để nó kích hoạt Controller nạp biến $profile
            header("Location: ../views/profile.php?msg=" . $status);
            exit();
        }
    }    
}

// KHỞI CHẠY CONTROLLER: Kích hoạt chạy ngay khi Modal Edit submit form đến file này
$controller = new EditPostController();
$controller->editPost();
?>