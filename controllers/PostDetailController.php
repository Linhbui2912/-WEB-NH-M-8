<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../modules/db_module.php';
require_once __DIR__ . '/../models/homepage_helpers.php';
require_once __DIR__ . '/../models/PostDetailModel.php';

$userId = hp_require_login_json();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    hp_json(['ok' => false, 'error' => 'method_not_allowed'], 405);
}

$postId = trim((string) ($_GET['post_id'] ?? $_GET['maBaiDang'] ?? ''));
if ($postId === '') {
    hp_json(['ok' => false, 'error' => 'invalid_post_id'], 422);
}

$link = null;
taoKetNoi($link);

try {
    $post = PostDetailModel::fetchPost($link, $postId, $userId);
    if ($post === null) {
        hp_json(['ok' => false, 'error' => 'not_found'], 404);
    }

    $comments = PostDetailModel::fetchComments($link, $postId);
    $assetPrefix = '../';

    $commentPayload = [];
    foreach ($comments as $c) {
        $commentPayload[] = [
            'maBinhLuan' => $c['maBinhLuan'],
            'tenDangNhap' => $c['tenDangNhap'],
            'avatar' => hp_avatar_url((string) ($c['anhDaiDien'] ?? ''), $assetPrefix),
            'noiDung' => $c['noiDungBinhLuan'],
            'thoiGian' => hp_time_ago((string) ($c['thoiGian'] ?? '')),
        ];
    }

    hp_json([
        'ok' => true,
        'post' => [
            'maBaiDang' => $post['maBaiDang'],
            'maNguoiDung' => $post['maNguoiDung'],
            'noiDung' => $post['noiDung'],
            'tenDangNhap' => $post['tenDangNhap'],
            'avatar' => hp_avatar_url((string) ($post['anhDaiDien'] ?? ''), $assetPrefix),
            'anhBaiDang' => hp_post_image_url((string) ($post['post_file'] ?? ''), $assetPrefix),
            'profileUrl' => '../views/profile.php?id=' . rawurlencode((string) $post['maNguoiDung']),
            'paw_count' => (int) ($post['paw_count'] ?? 0),
            'liked' => ((int) ($post['liked'] ?? 0)) === 1,
        ],
        'comments' => $commentPayload,
    ]);
} finally {
    giaiPhongBoNho($link, true);
}
