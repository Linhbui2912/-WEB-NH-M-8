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
                
                $maBaiDang = mysqli_real_escape_string($link, $row['maBaiDang']);
                
                
                $queryComments = "SELECT n.tenDangNhap AS username, bl.noiDungBinhLuan AS text 
                                  FROM binhluan bl 
                                  JOIN nguoidung n ON bl.maNguoiDung = n.maNguoiDung 
                                  WHERE bl.maBaiDang = '$maBaiDang'
                                  ORDER BY bl.thoiGian ASC"; 
                                  
                $resComments = chayTruyVanTraVeDL($link, $queryComments);
                $comments = [];
                
                if ($resComments && mysqli_num_rows($resComments) > 0) {
                    while ($cmt = mysqli_fetch_assoc($resComments)) {
                        $comments[] = $cmt;
                    }
                }
                
                $row['comments'] = $comments; 
                // -------------------------------------------
                
                $posts[] = $row;
            }
        }
        return $posts;
    }
}
?> 
