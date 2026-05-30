<?php
require_once "../modules/db_module.php";
require_once "../models/baidang.php";
class PostModel {
    // Truy vấn bảng HoSo để lấy thông tin hiển thị
    public function getProfileByUserId($link, $maNguoiDung) {
        $maNguoiDung = mysqli_real_escape_string($link, $maNguoiDung);
        $sql = "SELECT tenHienThi, anhDaiDien FROM hoso WHERE maNguoiDung = '$maNguoiDung' LIMIT 1";
                
        $result = chayTruyVanTraVeDL($link, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
        
        return null;
    }
    private function generateNextId($link, $tableName, $columnName, $prefix) {
         // LOGIC TỰ TĂNG MÃ BÀI ĐĂNG
        $sql_max_id = "SELECT $columnName FROM $tableName ORDER BY $columnName DESC LIMIT 1";
        $result_max = chayTruyVanTraVeDL($link, $sql_max_id); 
        $row_max = mysqli_fetch_assoc($result_max);
        
        if ($row_max) {
            $number = (int)substr($row_max[$columnName], strlen($prefix));
            return $prefix . str_pad($number + 1, 3, "0", STR_PAD_LEFT);
        }
        return $prefix . "001";
    }
    // Truyền vào một đối tượng thuộc class BaiDang
    public function insertPost($link, BaiDang $baiDang) {
        // Tự động sinh mã và gắn ngược lại vào đối tượng
        $baiDang->maBaiDang = $this->generateNextId($link, 'BaiDang', 'maBaiDang', 'BD');
        
        $sql_baidang = "INSERT INTO BaiDang (maBaiDang, maNguoiDung, noiDung, cheDoHienThi) 
                VALUES ('$baiDang->maBaiDang', '$baiDang->maNguoiDung', '$baiDang->noiDung','$baiDang->cheDoHienThi')";
        //Tiến hành chèn Bài Đăng vào DB trước        
        $result = chayTruyVanKhongTraVeDL($link, $sql_baidang);
        return $result ? $baiDang->maBaiDang : false;
    }
    // Truyền vào một đối tượng thuộc class PhuongTien
    public function insertMedia($link, PhuongTien $phuongTien) {
        $phuongTien->maPhuongTien = $this->generateNextId($link, 'PhuongTien', 'maPhuongTien', 'PT');
        
        $sql_pt = "INSERT INTO PhuongTien (maPhuongTien, maBaiDang, maNguoiDung, duongDan, loaiPhuongTien) 
                VALUES ('$phuongTien->maPhuongTien', '$phuongTien->maBaiDang', '$phuongTien->maNguoiDung', '$phuongTien->duongDan', '$phuongTien->loaiPhuongTien')";   
        return chayTruyVanKhongTraVeDL($link,  $sql_pt);
    }
}
?>