<?php
class BaiDang {
    public $maBaiDang;
    public $maNguoiDung;
    public $noiDung;
    public $cheDoHienThi;

    public function __construct($maBaiDang = null, $maNguoiDung = null, $noiDung = null) {
        $this->maBaiDang = $maBaiDang;
        $this->maNguoiDung = $maNguoiDung;
        $this->noiDung = $noiDung;
    }
}
class PhuongTien {
    public $maPhuongTien;
    public $maBaiDang;
    public $maNguoiDung;
    public $duongDan;
    public $loaiPhuongTien;

    public function __construct($maPhuongTien = null, $maBaiDang = null, $maNguoiDung = null, $duongDan = null, $loaiPhuongTien = 'image') {
        $this->maPhuongTien = $maPhuongTien;
        $this->maBaiDang = $maBaiDang;
        $this->maNguoiDung = $maNguoiDung;
        $this->duongDan = $duongDan;
        $this->loaiPhuongTien = $loaiPhuongTien;
    }
}
?>