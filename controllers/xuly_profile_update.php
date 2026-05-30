<?php

declare(strict_types=1);

require_once __DIR__ . '/../modules/db_module.php';
require_once __DIR__ . '/../modules/helpers.php';
require_once __DIR__ . '/../modules/auth.php';
require_once __DIR__ . '/../models/UserModel.php';

auth_start();
auth_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/profile.php');
    exit;
}

$userId = auth_user_id();
$tenHienThi = trim($_POST['tenHienThi'] ?? '');
$moTa = trim($_POST['moTa'] ?? '');
$anhDaiDien = null;

if ($tenHienThi === '') {
    header('Location: ../views/profile.php?msg=profile-error');
    exit;
}

if (isset($_FILES['anhDaiDien']) && $_FILES['anhDaiDien']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = dirname(__DIR__) . '/assets/Profile/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $ext = pathinfo($_FILES['anhDaiDien']['name'], PATHINFO_EXTENSION);
    $filename = 'avatar_' . $userId . '_' . time() . '.' . ($ext ?: 'jpg');
    $target = $uploadDir . $filename;
    if (move_uploaded_file($_FILES['anhDaiDien']['tmp_name'], $target)) {
        $anhDaiDien = $filename;
    }
}

$link = null;
taoKetNoi($link);
$userModel = new UserModel();
$ok = $userModel->updateHoSo($link, $userId, $tenHienThi, $moTa, $anhDaiDien);
giaiPhongBoNho($link, null);

$msg = $ok ? 'profile-saved' : 'profile-error';
header('Location: ../views/profile.php?msg=' . $msg);
exit;
