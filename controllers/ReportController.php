<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../modules/db_module.php';
require_once __DIR__ . '/../models/homepage_helpers.php';
require_once __DIR__ . '/../models/ReportModel.php';
require_once __DIR__ . '/../models/CommentModel.php';

$userId = hp_require_login_json();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    hp_json(['ok' => true, 'reasons' => ReportModel::REASONS]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    hp_json(['ok' => false, 'error' => 'method_not_allowed'], 405);
}

$payload = json_decode((string) file_get_contents('php://input'), true);
if (!is_array($payload)) {
    $payload = $_POST;
}

$postId = trim((string) ($payload['post_id'] ?? ''));
$reason = trim((string) ($payload['reason'] ?? $payload['lyDoBaoCao'] ?? ''));

if ($postId === '') {
    hp_json(['ok' => false, 'error' => 'invalid_post_id'], 422);
}
if (!ReportModel::isValidReason($reason)) {
    hp_json(['ok' => false, 'error' => 'invalid_reason'], 422);
}

$link = null;
taoKetNoi($link);

try {
    if (!CommentModel::postExists($link, $postId)) {
        hp_json(['ok' => false, 'error' => 'not_found'], 404);
    }

    if (!ReportModel::submit($link, $postId, $userId, $reason)) {
        hp_json(['ok' => false, 'error' => 'db_error'], 500);
    }

    hp_json(['ok' => true, 'message' => 'Đã gửi báo cáo. Admin sẽ xem xét.']);
} finally {
    giaiPhongBoNho($link, true);
}
