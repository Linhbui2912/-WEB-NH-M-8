<?php

declare(strict_types=1);

require_once __DIR__ . '/../modules/db_module.php';
require_once __DIR__ . '/../modules/helpers.php';
require_once __DIR__ . '/../modules/auth.php';
require_once __DIR__ . '/../models/SearchModel.php';

auth_start();
if (auth_user_id() === null) {
    // Tính đường dẫn tuyệt đối về trang đăng nhập
    $base = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/');
    header('Location: ' . $base . '/views/dangnhap.php?msg=login-required');
    exit;
}

$viewerId = auth_user_id();
$keyword  = trim((string) ($_GET['q'] ?? ''));
$isAjax   = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Tính project base URL (absolute từ domain root)
// Ví dụ: /PawsConnect  hoặc / nếu ở root
$projectUrl = project_base_url();


$results  = [];
$searched = false;

if ($keyword !== '') {
    $searched = true;
    $link = null;
    taoKetNoi($link);
    try {
        $results = SearchModel::searchByDisplayName($link, $keyword);
    } finally {
        giaiPhongBoNho($link, null);
    }

    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        $payload = array_map(static function (array $r) use ($projectUrl): array {
            return [
                'maNguoiDung' => $r['maNguoiDung'],
                'tenDangNhap' => $r['tenDangNhap'],
                'tenHienThi'  => $r['tenHienThi'],
                'avatar'      => profile_image_url($r['anhDaiDien']),
                'profileUrl'  => $projectUrl . '/views/profile.php?user='
                                 . rawurlencode($r['tenDangNhap']),
            ];
        }, $results);
        echo json_encode(['ok' => true, 'users' => $payload], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

$activeNav   = 'search';
$assetPrefix = '../';
require __DIR__ . '/../views/search.php';