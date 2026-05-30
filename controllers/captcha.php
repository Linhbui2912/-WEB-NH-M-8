<?php
session_start(); // Bắt buộc phải có để dùng được $_SESSION
require "../models/captcha_module.php";
setPNGHeader() ;
$alphabet = "aaAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ0123456789";
$random_captcha = makeCaptcha($alphabet, 6);
//Lưu vào Session (chuyển về chữ thường để khi so sánh không phân biệt hoa thường)
$_SESSION['captcha_code'] = strtolower($random_captcha);
makePNGCaptcha($random_captcha);
?>