<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/helpers.php';

$maBaiDang = $_GET['maBaiDang'] ?? '';
$viewerId = $_GET['viewer'] ?? 'U002';

if ($maBaiDang === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Thiếu mã bài đăng.']);
    exit;
}

try {
    $pdo = getDB();

    $stmt = $pdo->prepare('
        SELECT bd.maBaiDang, bd.noiDung, bd.thoiGianDang, bd.maNguoiDung, bd.cheDoHienThi,
               nd.tenDangNhap, hs.tenHienThi, hs.anhDaiDien,
               pt.duongDan,
               (SELECT COUNT(*) FROM PhanUng pu WHERE pu.maBaiDang = bd.maBaiDang) AS soPhanUng,
               (SELECT COUNT(*) FROM BinhLuan bl WHERE bl.maBaiDang = bd.maBaiDang) AS soBinhLuan,
               (SELECT loaiPhanUng FROM PhanUng pu
                WHERE pu.maBaiDang = bd.maBaiDang AND pu.maNguoiDung = :viewer LIMIT 1) AS phanUngCuaToi
        FROM BaiDang bd
        INNER JOIN NguoiDung nd ON nd.maNguoiDung = bd.maNguoiDung
        LEFT JOIN HoSo hs ON hs.maNguoiDung = nd.maNguoiDung
        LEFT JOIN PhuongTien pt ON pt.maBaiDang = bd.maBaiDang AND pt.loaiPhuongTien = \'image\'
        WHERE bd.maBaiDang = :maBaiDang
        LIMIT 1
    ');
    $stmt->execute(['maBaiDang' => $maBaiDang, 'viewer' => $viewerId]);
    $post = $stmt->fetch();

    if (!$post) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy bài đăng.']);
        exit;
    }

    $stmt = $pdo->prepare('
        SELECT bl.maBinhLuan, bl.noiDungBinhLuan, bl.thoiGian,
               nd.tenDangNhap, hs.tenHienThi, hs.anhDaiDien
        FROM BinhLuan bl
        INNER JOIN NguoiDung nd ON nd.maNguoiDung = bl.maNguoiDung
        LEFT JOIN HoSo hs ON hs.maNguoiDung = nd.maNguoiDung
        WHERE bl.maBaiDang = :maBaiDang
        ORDER BY bl.thoiGian ASC
    ');
    $stmt->execute(['maBaiDang' => $maBaiDang]);
    $comments = $stmt->fetchAll();

    $commentList = array_map(static function (array $c): array {
        return [
            'maBinhLuan' => $c['maBinhLuan'],
            'tenDangNhap' => $c['tenDangNhap'],
            'tenHienThi' => $c['tenHienThi'],
            'avatar' => profileImageUrl($c['anhDaiDien']),
            'noiDung' => $c['noiDungBinhLuan'],
            'thoiGian' => formatTimeAgo($c['thoiGian']),
        ];
    }, $comments);

    echo json_encode([
        'success' => true,
        'post' => [
            'maBaiDang' => $post['maBaiDang'],
            'noiDung' => $post['noiDung'],
            'thoiGian' => formatTimeAgo($post['thoiGianDang']),
            'anhBaiDang' => postImageUrl($post['duongDan']),
            'tenDangNhap' => $post['tenDangNhap'],
            'tenHienThi' => $post['tenHienThi'],
            'avatar' => profileImageUrl($post['anhDaiDien']),
            'profileUrl' => 'profile.php?user=' . urlencode($post['tenDangNhap']),
            'soPhanUng' => (int) $post['soPhanUng'],
            'soBinhLuan' => (int) $post['soBinhLuan'],
            'daThich' => !empty($post['phanUngCuaToi']),
            'loaiPhanUng' => $post['phanUngCuaToi'],
        ],
        'comments' => $commentList,
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $ex) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi kết nối CSDL.']);
}
