<?php
// Thiết lập tiêu đề cho tập tin PNG
function setPNGHeader(){
header("Content-Type: image/png");
header("Expires: -1");
header("Cache-Control:no-store,no-cache,must-revalidate");
header("Pragma: no-cache" );
}
// Tạo một xâu captcha ngẫu nhiên tách từ các ký tự trong bảng chữ cái và số
function makeCaptcha($source, $len){
$c = "";
for($i=0; $i<$len; $i++)
	$c.=substr($source, rand(0, strlen($source)-1), 1);
return $c;
}
// Vẽ xâu captcha dưới dạng ảnh
function makePNGCaptcha($captcha){
// imagecreate(); imagepng(); imagettftext() cần bật extension GD
// mở php.ini tìm:  ;extension=gd  --> bỏ dấu ";" còn extension=gd
$img = imagecreate(160, 45);
imagecolorallocate($img, 0, 0, 0);
$color = imagecolorallocate($img, 255, 255, 0);
$fontPath = "../assets/unicode.display.UVNNguyenDu.TTF"; 
imagettftext($img, 20, 2, 10, 35, $color, $fontPath, $captcha);
imagepng($img);
imagedestroy($img);
}
?>