<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../modules/db_module.php';
require_once __DIR__ . '/../models/homepage_helpers.php';
require_once __DIR__ . '/../models/CommentModel.php';
require_once __DIR__ . '/../models/PostDetailModel.php';

$userId = hp_require_login_json();

$link = null;
taoKetNoi($link);

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $postId = trim((string) ($_GET['post_id'] ?? ''));
        if ($postId === '') {
            hp_json(['ok' => false, 'error' => 'invalid_post_id'], 422);
        }

        $comments = PostDetailModel::fetchComments($link, $postId);
        $items = [];
        foreach ($comments as $c) {
            $items[] = [
                'username' => $c['tenDangNhap'],
                'body' => $c['noiDungBinhLuan'],
                'avatar' => hp_avatar_url((string) ($c['anhDaiDien'] ?? ''), '../'),
                'time' => hp_time_ago((string) ($c['thoiGian'] ?? '')),
            ];
        }

        hp_json(['ok' => true, 'comments' => $items]);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        hp_json(['ok' => false, 'error' => 'method_not_allowed'], 405);
    }

    $payload = json_decode((string) file_get_contents('php://input'), true);
    if (!is_array($payload)) {
        $payload = $_POST;
    }

    $postId = trim((string) ($payload['post_id'] ?? $payload['maBaiDang'] ?? ''));
    $body = trim((string) ($payload['body'] ?? $payload['noiDung'] ?? ''));

    if ($postId === '') {
        hp_json(['ok' => false, 'error' => 'invalid_post_id'], 422);
    }
    if ($body === '') {
        hp_json(['ok' => false, 'error' => 'empty_body'], 422);
    }
    if (mb_strlen($body) > 500) {
        hp_json(['ok' => false, 'error' => 'too_long'], 422);
    }

    if (!CommentModel::postExists($link, $postId)) {
        hp_json(['ok' => false, 'error' => 'not_found'], 404);
    }

    $row = CommentModel::addComment($link, $postId, $userId, $body);
    if ($row === null) {
        hp_json(['ok' => false, 'error' => 'db_error'], 500);
    }

    $comments = PostDetailModel::fetchComments($link, $postId);
    $items = [];
    foreach ($comments as $c) {
        $items[] = [
            'username' => $c['tenDangNhap'],
            'body' => $c['noiDungBinhLuan'],
            'avatar' => hp_avatar_url((string) ($c['anhDaiDien'] ?? ''), '../'),
            'time' => hp_time_ago((string) ($c['thoiGian'] ?? '')),
        ];
    }

    hp_json([
        'ok' => true,
        'comment' => [
            'username' => $row['tenDangNhap'],
            'body' => $row['noiDungBinhLuan'],
            'avatar' => hp_avatar_url((string) ($row['anhDaiDien'] ?? ''), '../'),
            'time' => 'Vừa xong',
        ],
        'comments' => $items,
    ]);
} finally {
    giaiPhongBoNho($link, true);
}
