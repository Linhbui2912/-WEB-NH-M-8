<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/helpers.php';

$profileId = $_GET['profile'] ?? '';
$type = $_GET['type'] ?? 'followers';
$viewerId = $_GET['viewer'] ?? 'U002';

if ($profileId === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Thiếu mã hồ sơ.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!in_array($type, ['followers', 'following'], true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Loại danh sách không hợp lệ.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $pdo = getDB();

    if ($type === 'followers') {
        $sql = '
            SELECT nd.maNguoiDung, nd.tenDangNhap, hs.tenHienThi, hs.anhDaiDien,
                   EXISTS(
                       SELECT 1 FROM TheoDoi td2
                       WHERE td2.maNguoiTheoDoi = :viewer
                         AND td2.maNguoiDuocTheoDoi = nd.maNguoiDung
                   ) AS viewerFollows,
                   EXISTS(
                       SELECT 1 FROM TheoDoi td3
                       WHERE td3.maNguoiTheoDoi = nd.maNguoiDung
                         AND td3.maNguoiDuocTheoDoi = :viewer
                   ) AS followsViewer
            FROM TheoDoi td
            INNER JOIN NguoiDung nd ON nd.maNguoiDung = td.maNguoiTheoDoi
            LEFT JOIN HoSo hs ON hs.maNguoiDung = nd.maNguoiDung
            WHERE td.maNguoiDuocTheoDoi = :profile
            ORDER BY td.thoiGianTheoDoi DESC
        ';
    } else {
        $sql = '
            SELECT nd.maNguoiDung, nd.tenDangNhap, hs.tenHienThi, hs.anhDaiDien,
                   EXISTS(
                       SELECT 1 FROM TheoDoi td2
                       WHERE td2.maNguoiTheoDoi = :viewer
                         AND td2.maNguoiDuocTheoDoi = nd.maNguoiDung
                   ) AS viewerFollows,
                   EXISTS(
                       SELECT 1 FROM TheoDoi td3
                       WHERE td3.maNguoiTheoDoi = nd.maNguoiDung
                         AND td3.maNguoiDuocTheoDoi = :viewer
                   ) AS followsViewer
            FROM TheoDoi td
            INNER JOIN NguoiDung nd ON nd.maNguoiDung = td.maNguoiDuocTheoDoi
            LEFT JOIN HoSo hs ON hs.maNguoiDung = nd.maNguoiDung
            WHERE td.maNguoiTheoDoi = :profile
            ORDER BY td.thoiGianTheoDoi DESC
        ';
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['profile' => $profileId, 'viewer' => $viewerId]);
    $rows = $stmt->fetchAll();

    $users = array_map(static function (array $row): array {
        return [
            'maNguoiDung' => $row['maNguoiDung'],
            'tenDangNhap' => $row['tenDangNhap'],
            'tenHienThi' => $row['tenHienThi'],
            'avatar' => profileImageUrl($row['anhDaiDien']),
            'profileUrl' => 'profile.php?user=' . urlencode($row['tenDangNhap']),
            'viewerFollows' => (bool) $row['viewerFollows'],
            'followsViewer' => (bool) $row['followsViewer'],
        ];
    }, $rows);

    $title = $type === 'followers' ? 'Người theo dõi' : 'Đang theo dõi';

    echo json_encode([
        'success' => true,
        'type' => $type,
        'title' => $title,
        'users' => $users,
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $ex) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi kết nối CSDL.'], JSON_UNESCAPED_UNICODE);
}
