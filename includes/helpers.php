<?php

declare(strict_types=1);

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function json_response(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function paw_db(): mysqli
{
    static $conn = null;
    if ($conn instanceof mysqli) {
        return $conn;
    }

    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conn) {
        throw new RuntimeException(mysqli_connect_error());
    }
    mysqli_set_charset($conn, DB_CHARSET);

    return $conn;
}

function paw_normalize_post_id(mixed $postId): string
{
    return trim((string) $postId);
}

function paw_normalize_user_id(mixed $userId): string
{
    return trim((string) $userId);
}

/**
 * DB lưu tên file (vd: C1.1.jpg) hoặc đường dẫn đầy đủ assets/...
 * Trả về URL tương đối từ thư mục pages/ (có $assetPrefix, thường là ../).
 */
function paw_media_src(string $file, string $assetPrefix, string $folder): string
{
    $file = trim($file);
    if ($file === '') {
        return $assetPrefix . 'assets/icon/user.png';
    }

    if (str_starts_with($file, 'http://') || str_starts_with($file, 'https://')) {
        return $file;
    }

    if (str_starts_with($file, 'assets/')) {
        return $assetPrefix . $file;
    }

    if (str_contains($file, 'uploads')) {
        $normalized = str_replace('\\', '/', $file);
        if (!str_starts_with($normalized, 'assets/')) {
            $normalized = 'assets/uploads/' . basename($normalized);
        }

        return $assetPrefix . $normalized;
    }

    return $assetPrefix . 'assets/' . $folder . '/' . basename($file);
}

function paw_post_image_src(string $file, string $assetPrefix): string
{
    return paw_media_src($file, $assetPrefix, 'Posts');
}

function paw_profile_image_src(string $file, string $assetPrefix): string
{
    return paw_media_src($file, $assetPrefix, 'Profile');
}

/**
 * Ảnh bài đăng từ cột PhuongTien.duongDan:
 * - Bài mẫu: C1.1.jpg           → ./assets/Posts/C1.1.jpg
 * - User upload: ../uploads/x.jpg → ./assets/uploads/x.jpg
 */
function paw_feed_post_src(string $duongDan, string $rootDir = ''): string
{
    $duongDan = trim(str_replace('\\', '/', $duongDan));
    if ($duongDan === '') {
        return '';
    }

    if (preg_match('#^https?://#i', $duongDan)) {
        return $duongDan;
    }

    if (str_contains($duongDan, 'uploads/')) {
        return './assets/uploads/' . basename($duongDan);
    }

    if (str_starts_with($duongDan, 'assets/')) {
        return './' . ltrim($duongDan, './');
    }

    $name = basename($duongDan);
    $postsDir = ($rootDir !== '' ? rtrim($rootDir, '/\\') : '') . '/assets/Posts/';
    if ($rootDir !== '' && is_dir($postsDir)) {
        foreach (scandir($postsDir) ?: [] as $file) {
            if ($file !== '.' && $file !== '..' && strcasecmp($file, $name) === 0) {
                return './assets/Posts/' . $file;
            }
        }
    }

    return './assets/Posts/' . $name;
}

/** Avatar: DB lưu C1.jpg → ./assets/Profile/C1.jpg */
function paw_feed_avatar_src(string $anhDaiDien, string $rootDir = ''): string
{
    $name = basename(str_replace('\\', '/', trim($anhDaiDien)));
    if ($name === '' || $name === '.' || $name === '..') {
        return './assets/icon/user.png';
    }

    $profileDir = ($rootDir !== '' ? rtrim($rootDir, '/\\') : '') . '/assets/Profile/';
    if ($rootDir !== '' && is_dir($profileDir)) {
        if (is_file($profileDir . $name)) {
            return './assets/Profile/' . $name;
        }
        foreach (scandir($profileDir) ?: [] as $file) {
            if ($file !== '.' && $file !== '..' && strcasecmp($file, $name) === 0) {
                return './assets/Profile/' . $file;
            }
        }
    }

    return './assets/Profile/' . $name;
}

function time_ago(string $datetime): string
{
    $ts = strtotime($datetime);
    if ($ts === false) {
        return '';
    }
    $diff = time() - $ts;
    if ($diff < 60) {
        return 'Vừa xong';
    }
    if ($diff < 3600) {
        return floor($diff / 60) . ' phút trước';
    }
    if ($diff < 86400) {
        return floor($diff / 3600) . ' giờ trước';
    }
    if ($diff < 86400 * 7) {
        return floor($diff / 86400) . ' ngày trước';
    }
    return date('d/m/Y', $ts);
}
