<?php

declare(strict_types=1);

function auth_start(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function auth_user_id(): ?string
{
    auth_start();
    $id = $_SESSION['maNguoiDung'] ?? null;
    return is_string($id) && $id !== '' ? $id : null;
}

function auth_require_login(): void
{
    if (auth_user_id() === null) {
        header('Location: dangnhap.php?msg=login-required');
        exit;
    }
}

function auth_require_login_json(): void
{
    if (auth_user_id() === null) {
        json_response(['success' => false, 'message' => 'Vui lòng đăng nhập.'], 401);
    }
}
