<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$maBaiDang = trim($_POST['maBaiDang'] ?? '');
$noiDung = trim($_POST['noiDung'] ?? '');
$maNguoiDung = trim($_POST['maNguoiDung'] ?? 'U002');

if ($maBaiDang === '' || $noiDung === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Vui lòng nhập nội dung bình luận.']);
    exit;
}

if (mb_strlen($noiDung) > 500) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Bình luận tối đa 500 ký tự.']);
    exit;
}

try {
    $pdo = getDB();

    $stmt = $pdo->prepare('SELECT maBaiDang FROM BaiDang WHERE maBaiDang = :id LIMIT 1');
    $stmt->execute(['id' => $maBaiDang]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Bài đăng không tồn tại.']);
        exit;
    }

    $maBinhLuan = 'BL' . strtoupper(substr(uniqid(), -8));

    $stmt = $pdo->prepare('
        INSERT INTO BinhLuan (maBinhLuan, maBaiDang, maNguoiDung, noiDungBinhLuan)
        VALUES (:maBinhLuan, :maBaiDang, :maNguoiDung, :noiDung)
    ');
    $stmt->execute([
        'maBinhLuan' => $maBinhLuan,
        'maBaiDang' => $maBaiDang,
        'maNguoiDung' => $maNguoiDung,
        'noiDung' => $noiDung,
    ]);

    $stmt = $pdo->prepare('
        SELECT bl.maBinhLuan, bl.noiDungBinhLuan, bl.thoiGian,
               nd.tenDangNhap, hs.tenHienThi, hs.anhDaiDien
        FROM BinhLuan bl
        INNER JOIN NguoiDung nd ON nd.maNguoiDung = bl.maNguoiDung
        LEFT JOIN HoSo hs ON hs.maNguoiDung = nd.maNguoiDung
        WHERE bl.maBinhLuan = :id
        LIMIT 1
    ');
    $stmt->execute(['id' => $maBinhLuan]);
    $comment = $stmt->fetch();

    echo json_encode([
        'success' => true,
        'comment' => [
            'maBinhLuan' => $comment['maBinhLuan'],
            'tenDangNhap' => $comment['tenDangNhap'],
            'tenHienThi' => $comment['tenHienThi'],
            'avatar' => profileImageUrl($comment['anhDaiDien']),
            'noiDung' => $comment['noiDungBinhLuan'],
            'thoiGian' => 'Vừa xong',
        ],
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $ex) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Không thể lưu bình luận.']);
}
