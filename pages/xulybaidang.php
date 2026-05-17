<?php
require_once "../db_module.php";
// Tạm thời không cần session_start() nếu gán cứng giá trị maNguoiDung
// Kiểm tra xem người dùng có gửi nội dung bài đăng (textarea name="noidung")
if (isset($_POST['noidung'])) {
    $link = null;
    taoKetNoi($link);

    // Lấy dữ liệu từ Form
    $noidung =$_POST['noidung'];
    
    // GIẢ ĐỊNH: Gán mã người dùng cố định để test    
    $maNguoiDung = "U001"; 

    // Xử lý File ảnh 
    $duongDanAnh = "";
    if (isset($_FILES['uploadfile']) && $_FILES['uploadfile']['error'] == 0) {
        $target_dir = "../uploads/";
        // Khai báo thư mục lưu trữ
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        // Tạo tên file duy nhất
        $file_name = time() . "_" . basename($_FILES["uploadfile"]["name"]);
        $target_file = $target_dir . $file_name;
        // Thiết lập đường dẫn đầy đủ
        if (move_uploaded_file($_FILES["uploadfile"]["tmp_name"], $target_file)) {
            $duongDanAnh = $target_file;
        }
    }

    // Lưu vào CSDL
      // LOGIC TỰ TĂNG MÃ BÀI ĐĂNG
    $sql_max_id = "SELECT maBaiDang FROM BaiDang ORDER BY maBaiDang DESC LIMIT 1";
    $result_max = chayTruyVanKhongTraVeDL($link, $sql_max_id);
    $row_max = mysqli_fetch_assoc($result_max);
    
    if ($row_max) {
        $number = (int)substr($row_max['maBaiDang'], 2);
        $maBaiDang = "BD" . str_pad($number + 1, 3, "0", STR_PAD_LEFT);
    } else {
        $maBaiDang = "BD001";
    }
    $sql_baidang = "INSERT INTO BaiDang (maBaiDang, maNguoiDung, noiDung) 
                    VALUES ('$maBaiDang', '$maNguoiDung', '$noidung')";
    $result = chayTruyVanKhongTraVeDL($link, $sql_baidang);

    if ($result && $duongDanAnh != "") {
    // LOGIC TỰ TĂNG MÃ BÀI PHƯƠNG TIỆN
    $sql_max_id = "SELECT maPhuongTien FROM PhuongTien ORDER BY maPhuongTien DESC LIMIT 1";
    $result_max = chayTruyVanKhongTraVeDL($link, $sql_max_id);
    $row_max = mysqli_fetch_assoc($result_max);
        if ($row_max) {
        $number = (int)substr($row_max['maPhuongTien'], 2);
        $maPhuongTien = "PT" . str_pad($number + 1, 3, "0", STR_PAD_LEFT);
    } else {
        $maPhuongTien = "PT001";
    }      
        $sql_pt = "INSERT INTO PhuongTien (maPhuongTien, maBaiDang, maNguoiDung, duongDan, loaiPhuongTien) 
                VALUES ('$maPhuongTien', '$maBaiDang', '$maNguoiDung', '$duongDanAnh', 'image')";      
        chayTruyVanKhongTraVeDL($link, $sql_pt);
    }

    giaiPhongBoNho($link, null);
    // Chuyển về trang trang chủ
    if ($result) {
        header("Location: homepage.php?msg=done");
    } else {
        header("Location: homepage.php?msg=error");
    }
}
?>
