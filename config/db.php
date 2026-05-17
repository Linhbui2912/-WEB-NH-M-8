<?php
define('HOST', '26.151.17.5'); // tên máy chủ cài đặt MySQL
define('DB', 'db_pawsconnect'); // Tên CSDL kết nối
define('USER', 'paws_user'); // Tên người dùng sử dụng CSDL
define('PASSWORD', ''); // Mật khẩu người dùng sử dụng CSDL

function getDB(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . HOST . ';dbname=' . DB . ';charset=utf8mb4';
        $pdo = new PDO($dsn, USER, PASSWORD, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}
