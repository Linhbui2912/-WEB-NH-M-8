<?php

declare(strict_types=1);

function hp_h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function hp_json(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function hp_esc(mysqli $link, string $value): string
{
    return mysqli_real_escape_string($link, $value);
}

function hp_project_root(): string
{
    return dirname(__DIR__);
}

/** Ảnh bài đăng từ PhuongTien.duongDan */
function hp_post_image_url(string $duongDan, string $assetPrefix = '../'): string
{
    $duongDan = trim(str_replace('\\', '/', $duongDan));
    if ($duongDan === '') {
        return '';
    }

    if (preg_match('#^https?://#i', $duongDan)) {
        return $duongDan;
    }

    if (str_contains($duongDan, 'uploads/')) {
        return $assetPrefix . 'assets/uploads/' . basename($duongDan);
    }

    // if (str_contains($duongDan, 'uploads/')) {
    //     return $assetPrefix . 'uploads/' . basename($duongDan);
    // }

    if (str_starts_with($duongDan, 'assets/')) {
        return $assetPrefix . ltrim($duongDan, './');
    }

    $name = basename($duongDan);
    $postsDir = hp_project_root() . '/assets/Posts/';
    if (is_dir($postsDir)) {
        foreach (scandir($postsDir) ?: [] as $file) {
            if ($file !== '.' && $file !== '..' && strcasecmp($file, $name) === 0) {
                return $assetPrefix . 'assets/Posts/' . $file;
            }
        }
    }

    return $assetPrefix . 'assets/Posts/' . $name;
}


function hp_avatar_url(string $anhDaiDien, string $assetPrefix = '../'): string
{
    $name = basename(str_replace('\\', '/', trim($anhDaiDien)));
    if ($name === '' || $name === '.' || $name === '..') {
        return $assetPrefix . 'assets/icon/user.png';
    }

    $profileDir = hp_project_root() . '/assets/Profile/';
    if (is_dir($profileDir)) {
        foreach (scandir($profileDir) ?: [] as $file) {
            if ($file !== '.' && $file !== '..' && strcasecmp($file, $name) === 0) {
                return $assetPrefix . 'assets/Profile/' . $file;
            }
        }
    }

    return $assetPrefix . 'assets/Profile/' . $name;
}

function hp_time_ago(?string $datetime): string
{
    if ($datetime === null || trim($datetime) === '') {
        return '';
    }
    $ts = strtotime($datetime);
    if ($ts === false) {
        return '';
    }
    $diff = time() - $ts;
    if ($diff < 60) {
        return 'Vừa xong';
    }
    if ($diff < 3600) {
        return (int) floor($diff / 60) . ' phút trước';
    }
    if ($diff < 86400) {
        return (int) floor($diff / 3600) . ' giờ trước';
    }
    if ($diff < 86400 * 7) {
        return (int) floor($diff / 86400) . ' ngày trước';
    }

    return date('d/m/Y', $ts);
}

function hp_new_id(string $prefix): string
{
    return $prefix . strtoupper(substr(bin2hex(random_bytes(6)), 0, 8));
}

function hp_require_login_json(): string
{
    if (!isset($_SESSION['maNguoiDung']) || $_SESSION['maNguoiDung'] === '') {
        hp_json(['ok' => false, 'error' => 'login_required'], 401);
    }

    return (string) $_SESSION['maNguoiDung'];
}
