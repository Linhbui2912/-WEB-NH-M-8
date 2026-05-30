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

function formatTimeAgo(string $datetime): string
{
    $time = strtotime($datetime);
    if ($time === false) {
        return '';
    }
    $diff = time() - $time;
    if ($diff < 60) {
        return 'Vừa xong';
    }
    if ($diff < 3600) {
        return (string) floor($diff / 60) . ' phút trước';
    }
    if ($diff < 86400) {
        return (string) floor($diff / 3600) . ' giờ trước';
    }
    if ($diff < 604800) {
        return (string) floor($diff / 86400) . ' ngày trước';
    }
    return date('d/m/Y', $time);
}

/** URL assets từ views/ hoặc controllers/ */
function asset_url(string $path): string
{
    $path = ltrim(str_replace('\\', '/', $path), '/');
    return '../assets/' . $path;
}

function profile_image_url(?string $file): string
{
    if (!$file || trim($file) === '') {
        return asset_url('icon/user.png');
    }
    $file = basename(str_replace('\\', '/', $file));
    $full = dirname(__DIR__) . '/assets/Profile/' . $file;
    if (is_file($full)) {
        return asset_url('Profile/' . rawurlencode($file));
    }
    $full = dirname(__DIR__) . '/assets/uploads/' . $file;
    if (is_file($full)) {
        return asset_url('uploads/' . rawurlencode($file));
    }
    return asset_url('icon/user.png');
}

function post_image_url(?string $file): string
{
    if (!$file || trim($file) === '') {
        return asset_url('icon/paw.png');
    }
    $normalized = str_replace('\\', '/', trim($file));
    if (str_contains($normalized, 'uploads/')) {
        return asset_url('uploads/' . rawurlencode(basename($normalized)));
    }
    $name = basename($normalized);
    $full = dirname(__DIR__) . '/assets/Posts/' . $name;
    if (is_file($full)) {
        return asset_url('Posts/' . rawurlencode($name));
    }
    return asset_url('Posts/' . rawurlencode($name));
}

function generate_id(string $prefix, int $length = 8): string
{
    return $prefix . strtoupper(substr(bin2hex(random_bytes(16)), 0, $length));
}

function profile_page_url(?string $username = null, ?string $userId = null): string
{
    if ($username !== null && $username !== '') {
        return 'profile.php?user=' . rawurlencode($username);
    }
    if ($userId !== null && $userId !== '') {
        return 'profile.php?id=' . rawurlencode($userId);
    }
    return 'profile.php';
}
/**
 * Trả về base URL của project (không có trailing slash)
 * Hoạt động đúng dù chạy từ localhost:3000 hay subfolder
 */
function project_base_url(): string
{
    $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host;
}