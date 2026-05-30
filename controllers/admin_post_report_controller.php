<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../modules/db_module.php';
require_once __DIR__ . '/../models/homepage_helpers.php';
require_once __DIR__ . '/../models/admin_post_report_model.php';

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['maNguoiDung']) || ($_SESSION['maQuyen'] ?? '') !== '1') {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => 'forbidden']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = trim((string) ($_GET['action'] ?? ''));

$link = null;
taoKetNoi($link);

try {
    // ── GET: chi tiết 1 bài đăng ──────────────────────────────────────────
    if ($method === 'GET' && $action === 'detail') {
        $maBaiDang = trim((string) ($_GET['maBaiDang'] ?? ''));
        if ($maBaiDang === '') {
            hp_json(['ok' => false, 'error' => 'missing_maBaiDang'], 422);
        }

        $post = PostReportModel::fetchPostDetail($link, $maBaiDang);
        if ($post === null) {
            hp_json(['ok' => false, 'error' => 'not_found'], 404);
        }

        $assetPrefix = '../';
        $comments = [];
        foreach ($post['comments'] as $c) {
            $comments[] = [
                'tenDangNhap' => $c['tenDangNhap'],
                'avatar'      => hp_avatar_url((string) ($c['anhDaiDien'] ?? ''), $assetPrefix),
                'noiDung'     => $c['noiDungBinhLuan'],
                'thoiGian'    => hp_time_ago((string) ($c['thoiGian'] ?? '')),
            ];
        }

        hp_json([
            'ok'   => true,
            'post' => [
                'maBaiDang'  => $post['maBaiDang'],
                'noiDung'    => $post['noiDung'],
                'tenDangNhap'=> $post['tenDangNhap'],
                'avatar'     => hp_avatar_url((string) ($post['anhDaiDien'] ?? ''), $assetPrefix),
                'anhBaiDang' => hp_post_image_url((string) ($post['post_file'] ?? ''), $assetPrefix),
                'paw_count'  => (int) ($post['paw_count'] ?? 0),
                'thoiGian'   => hp_time_ago((string) ($post['thoiGianDang'] ?? '')),
                'comments'   => $comments,
            ],
        ]);
    }

    // ── POST: xóa bài đăng hoặc từ chối báo cáo ──────────────────────────
    if ($method === 'POST') {
        $payload = json_decode((string) file_get_contents('php://input'), true);
        if (!is_array($payload)) {
            hp_json(['ok' => false, 'error' => 'invalid_json'], 400);
        }

        $act       = trim((string) ($payload['action'] ?? ''));
        $maBaoCao  = trim((string) ($payload['maBaoCao'] ?? ''));
        $maBaiDang = trim((string) ($payload['maBaiDang'] ?? ''));

        if ($maBaoCao === '') {
            hp_json(['ok' => false, 'error' => 'missing_maBaoCao'], 422);
        }

        if ($act === 'delete') {
            if ($maBaiDang === '') {
                hp_json(['ok' => false, 'error' => 'missing_maBaiDang'], 422);
            }
            $ok = PostReportModel::deletePost($link, $maBaiDang, $maBaoCao);
            hp_json(['ok' => $ok]);
        }

        if ($act === 'reject') {
            $ok = PostReportModel::rejectReport($link, $maBaoCao);
            hp_json(['ok' => $ok]);
        }

        hp_json(['ok' => false, 'error' => 'unknown_action'], 422);
    }

    hp_json(['ok' => false, 'error' => 'method_not_allowed'], 405);

} finally {
    giaiPhongBoNho($link, true);
}