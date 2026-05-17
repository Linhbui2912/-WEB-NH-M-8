<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/helpers.php';

spl_autoload_register(static function (string $class): void {
    $file = __DIR__ . '/../classes/' . $class . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

/** User demo khi chưa có đăng nhập — sau này thay bằng session đăng nhập thật. */
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 'U002'; // bob — session demo
}
