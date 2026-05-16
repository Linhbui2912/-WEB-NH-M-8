<?php
define('HOST', '26.151.17.5');
define('DB', 'db_pawsconnect');
define('USER', 'paws_user');
define('PASSWORD', '');

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
