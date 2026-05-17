<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/helpers.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === '') {
    $_SESSION['user_id'] = 'U001';
}
