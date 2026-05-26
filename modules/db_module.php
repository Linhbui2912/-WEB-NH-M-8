<!-- File chứa các hàm  kết nối, truy vấn và giải phóng bộ nhớ -->
<!-- Sử dụng tham số truyền vào từ file config.php -->
<?php
require_once "config.php";
function taoKetNoi(&$link) // mở kết máy chủ đến CSDL
{  
    // Sử dụng hàm mysqli_connect
    $link = mysqli_connect(HOST,USER,PASSWORD,DB);
    if (mysqli_connect_errno()) {
        echo"Lỗi kết nối đến máy chủ: " .mysqli_connect_error();
        exit();
    }
}
// thực thi truy vấn dạng select và trả dữ liệu về bản ghi dataset
function chayTruyVanTraVeDL ($link,$q) {
    // Sử dụng hàm mysqli_query
    $result = mysqli_query($link,$q);
    return $result;
}
// thực thi truy vấn dạng update, insert, delete và trả về giá trị true (thực thi thành công)/ false (thưc thi thất bại)
function chayTruyVanKhongTraVeDL ($link,$q)
{   
    // Sử dụng hàm mysqli_query
    $result = mysqli_query($link,$q);
    return $result;
}
// giải phóng tài nguyên lưu trữ kết quả truy vấn ($result) và đóng kết nối ($link)
function giaiPhongBoNho( $link,$result)
{
try{
    // Sử dụng hàm mysqli_close
    mysqli_close($link);
    // Sử dụng hàm mysqli_free_result
    mysqli_free_result($result);    
}
catch (TypeError $e) {
}
}
?>