<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../modules/db_module.php';
require_once __DIR__ . '/../models/homepage_helpers.php';
require_once __DIR__ . '/../models/LikeModel.php';

$userId = hp_require_login_json();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    hp_json(['ok' => false, 'error' => 'method_not_allowed'], 405);
}

$payload = json_decode((string) file_get_contents('php://input'), true);
if (!is_array($payload)) {
    hp_json(['ok' => false, 'error' => 'invalid_json'], 400);
}

$postId = trim((string) ($payload['post_id'] ?? ''));
if ($postId === '') {
    hp_json(['ok' => false, 'error' => 'invalid_post_id'], 422);
}

$link = null;
taoKetNoi($link);

try {
    $result = LikeModel::toggle($link, $postId, $userId);
    hp_json([
        'ok' => true,
        'liked' => $result['liked'],
        'like_count' => $result['like_count'],
    ]);
} finally {
    giaiPhongBoNho($link, true);
}
