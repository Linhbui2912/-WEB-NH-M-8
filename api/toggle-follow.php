<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Chỉ hỗ trợ POST.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$followerId = trim($_POST['maNguoiTheoDoi'] ?? '');
$targetId = trim($_POST['maNguoiDuocTheoDoi'] ?? '');
$action = trim($_POST['action'] ?? 'toggle');

if ($followerId === '' || $targetId === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin người dùng.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($followerId === $targetId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Không thể theo dõi chính mình.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $pdo = getDB();

    $stmt = $pdo->prepare('
        SELECT 1 FROM TheoDoi
        WHERE maNguoiTheoDoi = :follower AND maNguoiDuocTheoDoi = :target
        LIMIT 1
    ');
    $stmt->execute(['follower' => $followerId, 'target' => $targetId]);
    $alreadyFollowing = (bool) $stmt->fetchColumn();

    $shouldFollow = $action === 'follow'
        ? true
        : ($action === 'unfollow' ? false : !$alreadyFollowing);

    if ($shouldFollow && !$alreadyFollowing) {
        $stmt = $pdo->prepare('
            INSERT INTO TheoDoi (maNguoiTheoDoi, maNguoiDuocTheoDoi)
            VALUES (:follower, :target)
        ');
        $stmt->execute(['follower' => $followerId, 'target' => $targetId]);
    } elseif (!$shouldFollow && $alreadyFollowing) {
        $stmt = $pdo->prepare('
            DELETE FROM TheoDoi
            WHERE maNguoiTheoDoi = :follower AND maNguoiDuocTheoDoi = :target
        ');
        $stmt->execute(['follower' => $followerId, 'target' => $targetId]);
    }

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM TheoDoi WHERE maNguoiDuocTheoDoi = :id');
    $stmt->execute(['id' => $targetId]);
    $targetFollowerCount = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM TheoDoi WHERE maNguoiTheoDoi = :id');
    $stmt->execute(['id' => $followerId]);
    $followerFollowingCount = (int) $stmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'following' => $shouldFollow,
        'targetFollowerCount' => $targetFollowerCount,
        'followerFollowingCount' => $followerFollowingCount,
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $ex) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi kết nối CSDL.'], JSON_UNESCAPED_UNICODE);
}
