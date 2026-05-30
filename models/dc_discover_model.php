<?php
require_once "../modules/db_module.php";

class DiscoverModel {
    public function layDanhSachBaiDang($link) {
        $query = "SELECT b.maBaiDang, b.noiDung, 
                         n.tenDangNhap, h.anhDaiDien, p.duongDan,
                         (SELECT COUNT(*) FROM PhanUng pu WHERE pu.maBaiDang = b.maBaiDang) AS soLuotPaw
                  FROM BaiDang b
                  JOIN NguoiDung n ON b.maNguoiDung = n.maNguoiDung
                  LEFT JOIN HoSo h ON n.maNguoiDung = h.maNguoiDung
                  LEFT JOIN PhuongTien p ON b.maBaiDang = p.maBaiDang";
                  
        $result = chayTruyVanTraVeDL($link, $query);
        $posts = [];
        
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $row['soLuotPaw'] = (int)$row['soLuotPaw'];
                $posts[] = $row;
            }
        }
        return $posts;
    }
}
?>