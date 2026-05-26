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
}
?>