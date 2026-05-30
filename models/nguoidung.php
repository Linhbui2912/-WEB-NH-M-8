<?php
class NguoiDung {
    private $maNguoiDung;
    private $tenDangNhap;
    private $maQuyen;
    
    public function __construct($maNguoiDung, $tenDangNhap,$maQuyen = null) {
        $this->maNguoiDung = $maNguoiDung;
        $this->tenDangNhap = $tenDangNhap;
        $this->maQuyen = $maQuyen;
    }

    // Các hàm Getter để lấy dữ liệu ra ngoài
    public function getMaNguoiDung() {
        return $this->maNguoiDung;
    }

    public function getTenDangNhap() {
        return $this->tenDangNhap;
    }
    public function getMaQuyen() {
        return $this->maQuyen;
    }
}
?>