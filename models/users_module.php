<?php
require_once "../modules/db_module.php";
require_once "../models/nguoidung.php";
class users_module{
    function dangnhap($link,$username,$password){       
    $result=chayTruyVanTraVeDL($link,"select maNguoiDung, tenDangNhap, maQuyen from nguoidung where tenDangNhap='$username'and matKhau='$password'");    
    $userObject = null;
    if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
    // Ánh xạ dữ liệu thô vào Object NguoiDung
    $userObject = new NguoiDung($row['maNguoiDung'], $row['tenDangNhap'],$row['maQuyen']);
    }   
    return $userObject; 
    }
    // HÀM TỰ ĐỘNG SINH MÃ NGƯỜI DÙNG TỰ TĂNG
    private function generateNextUserId($link) {
        $sql_max_id = "SELECT maNguoiDung FROM nguoidung ORDER BY maNguoiDung DESC LIMIT 1";
        $result_max = chayTruyVanTraVeDL($link, $sql_max_id); 
        $row_max = mysqli_fetch_assoc($result_max);
        
        if ($row_max) {
            // Cắt bỏ chữ "U" (bắt đầu lấy từ ký tự thứ 1 trở đi) để chuyển thành số
            $number = (int)substr($row_max['maNguoiDung'], 1); 
            // Trả về chữ U ghép với số được điền thêm số 0 cho đủ 3 chữ số (Ví dụ: U002)
            return "U" . str_pad($number + 1, 3, "0", STR_PAD_LEFT);
        }
        return "U001"; // Nếu bảng rỗng thì tài khoản đầu tiên là U001
    }
   // HÀM TỰ ĐỘNG SINH MÃ HỒ SƠ TỰ TĂNG 
    private function generateNextProfileId($link) {        
        $sql_max_id = "SELECT maHoSo FROM hoso ORDER BY maHoSo DESC LIMIT 1";
        $result_max = chayTruyVanTraVeDL($link, $sql_max_id);
        $row_max = mysqli_fetch_assoc($result_max);
        
        if ($row_max) {
            // Cắt bỏ 2 ký tự đầu "HS" (lấy từ vị trí số 2 trở đi) để lấy phần số
            $number = (int)substr($row_max['maHoSo'], 2);
            // Cộng thêm 1 và bù số 0 vào bên trái cho đủ 3 chữ số
            return "HS" . str_pad($number + 1, 3, "0", STR_PAD_LEFT);
        }
        return "HS001"; // Nếu bảng hoso rỗng thì mã đầu tiên là HS001
    }
    // Hàm kiểm tra trùng lặp username
    public function existsUserName($link, $username)
    {   $username = mysqli_real_escape_string($link, $username);  
        $sql_check = "SELECT maNguoiDung FROM nguoidung WHERE tenDangNhap = '$username' LIMIT 1";
        $result_check = chayTruyVanTraVeDL($link, $sql_check);
        $row = mysqli_fetch_row($result_check);        
       // Nếu $row có dữ liệu (khác null) tức là tên tài khoản đã tồn tại
        if ($row) {
            return true; 
        }        
        // Ngược lại nếu $row là null (không tìm thấy dòng nào) tức là tài khoản hợp lệ
        return false;   
    }

    // --- HÀM ĐĂNG KÝ TÀI KHOẢN MỚI ---
    function dangky($link, $profilename, $username, $password) {
        $profilename   = mysqli_real_escape_string($link, $profilename);
        $username = mysqli_real_escape_string($link, $username);       
        $password = mysqli_real_escape_string($link, $password);     
        
        // Gọi hàm sinh mã tự động dạng U001, U002...
        $maNguoiDung = $this->generateNextUserId($link);
        // Chèn vào bảng nguoidung
        $sql_user = "INSERT INTO nguoidung (maNguoiDung, tenDangNhap, matKhau) 
                    VALUES ('$maNguoiDung', '$username', '$password')";        
        $insert_user_result = chayTruyVanKhongTraVeDL($link, $sql_user);
        if ($insert_user_result) {
            //Gọi hàm tự tăng mã hồ sơ trước khi INSERT
            $maHoSo = $this->generateNextProfileId($link);
            // Đồng bộ tạo luôn bản ghi bên bảng hoso với mã người dùng mới
            $sql_profile = "INSERT INTO hoso (maHoSo,maNguoiDung, tenHienThi, anhDaiDien) 
                            VALUES ('$maHoSo','$maNguoiDung', '$profilename', 'default_avatar.jpg')";
            chayTruyVanKhongTraVeDL($link, $sql_profile);
            return true; 
        }
        return false; 
    }
}
?>