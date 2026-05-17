<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/api_init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['ok' => false, 'error' => 'method_not_allowed'], 405);
}

$data = json_decode((string) file_get_contents('php://input'), true);
if (!is_array($data)) {
    json_response(['ok' => false, 'error' => 'invalid_json'], 400);
}

$postId = paw_normalize_post_id($data['post_id'] ?? '');
$userId = paw_normalize_user_id($_SESSION['user_id']);

if ($postId === '' || $userId === '') {
    json_response(['ok' => false, 'error' => 'invalid_post_id'], 422);
}

try {
    $conn = paw_db();
    $stmt = mysqli_prepare(
        $conn,
        'DELETE FROM PhanUng
         WHERE maBaiDang = ? AND maNguoiDung = ? AND loaiPhanUng = \'luu\''
    );
    if (!$stmt) {
        throw new RuntimeException(mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, 'ss', $postId, $userId);
    mysqli_stmt_execute($stmt);
    $removed = mysqli_stmt_affected_rows($stmt) > 0;
    mysqli_stmt_close($stmt);

    if ($removed) {
        json_response(['ok' => true, 'saved' => false]);
    }

    $stmt = mysqli_prepare(
        $conn,
        'INSERT INTO PhanUng (maBaiDang, maNguoiDung, loaiPhanUng) VALUES (?, ?, \'luu\')'
    );
    if (!$stmt) {
        throw new RuntimeException(mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, 'ss', $postId, $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    json_response(['ok' => true, 'saved' => true]);
} catch (Throwable $e) {
    json_response(['ok' => false, 'error' => 'db_error'], 500);
}
