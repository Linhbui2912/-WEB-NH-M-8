<?php

declare(strict_types=1);

session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 'U001';
}

$userId = rawurlencode((string) $_SESSION['user_id']);
header('Location: profile.php?id=' . $userId . '&tab=saved');
exit;
