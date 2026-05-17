<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

$pdo = Database::connection();
$comments = new CommentRepository($pdo);
$userId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $postId = (int) ($_GET['post_id'] ?? 0);
    if ($postId <= 0) {
        json_response(['ok' => false, 'error' => 'invalid_post_id'], 422);
    }

    $rows = $comments->listForPost($postId);
    json_response(['ok' => true, 'comments' => $rows]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode((string) file_get_contents('php://input'), true);
    if (!is_array($data)) {
        json_response(['ok' => false, 'error' => 'invalid_json'], 400);
    }

    $postId = (int) ($data['post_id'] ?? 0);
    $body = trim((string) ($data['body'] ?? ''));

    if ($postId <= 0) {
        json_response(['ok' => false, 'error' => 'invalid_post_id'], 422);
    }
    if ($body === '') {
        json_response(['ok' => false, 'error' => 'empty_body'], 422);
    }

    $comments->add($postId, $userId, $body);
    $rows = $comments->listForPost($postId);

    json_response(['ok' => true, 'comments' => $rows]);
}

json_response(['ok' => false, 'error' => 'method_not_allowed'], 405);
