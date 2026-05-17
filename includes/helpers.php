<?php

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Ánh xạ tên file CSDL → file thực tế trong SOURCE IMAGES (khi chưa có thư mục Profile/Posts). */
function mediaFileAliases(): array
{
    return [
        'C1.jpg' => 'meo_avatar.jpg',
        'C1.1.jpg' => 'meo.jpg',
        'C1.2.jpg' => 'meo2.webp',
        'C1.3.jpg' => 'meo3.jpg',
        'C1.4.jpg' => 'meo4.jpg',
        'C1.5.jpg' => 'meo5.jpg',
        'C1.6.jpg' => 'meo6.webp',
        'C1.7.jpg' => 'meo7.webp',
        'C1.8.jpg' => 'meo8.jpeg',
        'C2.jpg' => 'inu_avatar.jpg',
        'C2.1.jpg' => 'inushibademo.jpg',
        'C4.jpg' => 'meo3.jpg',
        'C5.jpg' => 'meo4.jpg',
        'C5.1.jpg' => 'meo5.jpg',
        'C5.2.jpg' => 'meo6.webp',
        'D1.jpg' => 'meo_avatar.jpg',
        'D1.1.jpg' => 'meomeodemo.jpg',
        'D1.2.jpg' => 'meo.jpg',
        'D3.jpg' => 'meo2.webp',
        'D3.1.jpg' => 'meo3.jpg',
        'D3.2.jpg' => 'meo4.jpg',
        'D3.3.jpg' => 'meo5.jpg',
        'D4.jpg' => 'meo6.webp',
        'D4.1.jpg' => 'meo7.webp',
        'D4.2.jpg' => 'meo8.jpeg',
        'D5.jpg' => 'meo9.jpg',
        'D6.jpg' => 'meo10.jpg',
        'D7.jpg' => 'meo11.jpeg',
    ];
}

function buildAssetWebPath(string $folder, string $filename): string
{
    $folder = str_replace('\\', '/', $folder);
    $segments = explode('/', $folder);
    $encodedFolder = implode('/', array_map('rawurlencode', $segments));

    return '../assets/' . $encodedFolder . '/' . rawurlencode($filename);
}

/**
 * Tìm file ảnh trong Profile, Posts hoặc SOURCE IMAGES (theo thứ tự).
 */
function resolveMediaUrl(?string $file, array $folders, string $fallbackWebPath): string
{
    if (!$file || trim($file) === '') {
        return $fallbackWebPath;
    }

    $file = basename(str_replace('\\', '/', $file));
    $aliases = mediaFileAliases();
    $candidates = [$file];
    if (isset($aliases[$file])) {
        $candidates[] = $aliases[$file];
    }

    $projectRoot = dirname(__DIR__);
    $assetsRoot = $projectRoot . DIRECTORY_SEPARATOR . 'assets';

    foreach ($candidates as $candidate) {
        foreach ($folders as $folder) {
            $folderPath = $assetsRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $folder);
            $fullPath = $folderPath . DIRECTORY_SEPARATOR . $candidate;
            if (is_file($fullPath)) {
                return buildAssetWebPath($folder, $candidate);
            }
        }
    }

    return $fallbackWebPath;
}

function profileImageUrl(?string $file): string
{
    return resolveMediaUrl(
        $file,
        ['Profile', 'SOURCE IMAGES', 'icon'],
        '../assets/icon/user.png'
    );
}

function postImageUrl(?string $file): string
{
    return resolveMediaUrl(
        $file,
        ['Posts', 'SOURCE IMAGES'],
        '../assets/icon/paw.png'
    );
}

function formatTimeAgo(string $datetime): string
{
    $time = strtotime($datetime);
    $diff = time() - $time;
    if ($diff < 60) {
        return 'Vừa xong';
    }
    if ($diff < 3600) {
        return floor($diff / 60) . ' phút trước';
    }
    if ($diff < 86400) {
        return floor($diff / 3600) . ' giờ trước';
    }
    if ($diff < 604800) {
        return floor($diff / 86400) . ' ngày trước';
    }
    return date('d/m/Y', $time);
}

function reactionLabel(string $type): string
{
    $map = [
        'thich' => 'đã thích',
        'yeu_thich' => 'yêu thích',
        'haha' => 'đã cười',
        'quan_tam' => 'quan tâm',
        'wow' => 'wow',
    ];
    return $map[$type] ?? 'đã phản ứng';
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
