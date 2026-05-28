<?php
session_start();
require_once "../models/post_module.php";
require_once "../modules/db_module.php";
class PostController {    
    
    public function createPost() {
        if (isset($_POST['noidung'])) {
    $link = null;
    taoKetNoi($link);

    // Lấy dữ liệu từ Form
    $noidung =$_POST['noidung'];
    
    // GIẢ ĐỊNH: Gán mã người dùng cố định để test    
    $maNguoiDung = $_SESSION['maNguoiDung'] ; 

    // Xử lý File ảnh 
    $mangDuongDan = []; // Mảng chứa toàn bộ các đường dẫn ảnh upload
    if (isset($_FILES['uploadfile']) && !empty($_FILES['uploadfile']['name'][0])) {
        $target_dir = "../assets/uploads/";
        // Khai báo thư mục lưu trữ
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        // Đếm tổng số lượng file gửi lên từ dạng Carousel (multiple)
        $total_files = count($_FILES['uploadfile']['name']);
        for ($i = 0; $i < $total_files; $i++) {
            if ($_FILES['uploadfile']['error'][$i] == 0) {
                // Tạo tên file duy nhất dựa trên thời gian và số chỉ mục vòng lặp
                $file_name = time() . "_" . $i . "_" . basename($_FILES["uploadfile"]["name"][$i]);
                $target_file = $target_dir . $file_name;
                // Thiết lập đường dẫn đầy đủ
            if (move_uploaded_file($_FILES["uploadfile"]["tmp_name"][$i], $target_file)) {
            // Lấy kiểu định dạng file (ví dụ: "image/png", "video/mp4")
            $file_type = $_FILES['uploadfile']['type'][$i];
            
            // Kiểm tra xem có phải định dạng video hay không
            $loaiMedia = (strpos($file_type, 'video') !== false) ? 'video' : 'image';

            // Thêm cả đường dẫn và phân loại vào mảng tạm
            $mangDuongDan[] = [
                'path' => $target_file,
                'type' => $loaiMedia
            ];
        }
            }        
        }
        }
        $postModel = new PostModel();
       // Lấy chế độ hiển thị từ dropdown 
        $quyen_rieng_tu = isset($_POST['quyen_rieng_tu']) ? $_POST['quyen_rieng_tu'] : 'public';
        $cheDoHienThi = 'cong_khai'; // Biến tạm lưu giá trị mặc định ban đầu
        // Lọc sang giá trị lưu db
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
        // Khởi tạo thực thể BaiDang và đóng gói dữ liệu văn bản
        $newPost = new BaiDang(null, $maNguoiDung, $noidung);
        // Gắn giá trị quyền riêng tư vào thuộc tính của đối tượng
        $newPost->cheDoHienThi = $cheDoHienThi;
        // Chèn bài viết chữ vào DB, nhận lại mã bài đăng vừa tạo (BDxxx)
        $maBaiDangVuaTao = $postModel->insertPost($link, $newPost);
        if ($maBaiDangVuaTao) {
                    if (count($mangDuongDan) > 0) {
            foreach ($mangDuongDan as $item) {
                // Truyền $item['path'] và $item['type'] thay vì gán cứng 'image'
                $newMedia = new PhuongTien(null, $maBaiDangVuaTao, $maNguoiDung, $item['path'], $item['type']);
                // Chèn thông tin vào bảng PhuongTien (PTxxx)
                $postModel->insertMedia($link, $newMedia);
            }
        }
        $status = "done"; // Gán trạng thái thành công
        } else {
        $status = "error"; // Gán trạng thái thất bại
        }
        giaiPhongBoNho($link, null);      
        // Chuyển về trang trang chủ
        header("Location: ../views/homepage.php?msg=" . $status);
        }
    }    
    }
    // KHỞI CHẠY CONTROLLER: Kích hoạt chạy ngay khi Form từ View action gọi đến file này
    $controller = new PostController();
    $controller->createPost();
?>