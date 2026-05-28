<?php
// models/admin_user_model.php

class admin_user_model {
    
    // Lấy danh sách tài khoản bị báo cáo
    public function getReportedUsers($link) {
        $sql = "SELECT 
                    nd_bi.maNguoiDung, 
                    nd_bi.tenDangNhap, 
                    nd_bi.trangThai, 
                    nd_bc.tenDangNhap AS nguoiBaoCao, 
                    bc.lyDoBaoCao, 
                    bc.thoiGianBaoCao AS ngayBaoCao 
                FROM baocaotaikhoan bc
                JOIN nguoidung nd_bi ON bc.maNguoiBiBaoCao = nd_bi.maNguoiDung
                JOIN nguoidung nd_bc ON bc.maNguoiBaoCao = nd_bc.maNguoiDung
                ORDER BY bc.thoiGianBaoCao DESC";
                
        $result = mysqli_query($link, $sql);
        $list = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $list[] = $row;
            }
        }
        return $list;
    }

    // Cập nhật trạng thái người dùng (Khóa / Mở khóa)
    public function toggleStatus($link, $maNguoiDung, $trangThaiMoi) {
        $sql = "UPDATE nguoidung SET trangThai = ? WHERE maNguoiDung = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $trangThaiMoi, $maNguoiDung);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }
}
?>