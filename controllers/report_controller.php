<?php

declare(strict_types=1);

require_once __DIR__ . '/../modules/db_module.php';
require_once __DIR__ . '/../modules/helpers.php';
require_once __DIR__ . '/../modules/auth.php';
require_once __DIR__ . '/../models/ReportModel.php';
require_once __DIR__ . '/../models/PostModel.php';
require_once __DIR__ . '/../models/UserModel.php';

auth_require_login_json();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Chỉ hỗ trợ POST.'], 405);
}

$action = trim($_POST['action'] ?? '');
$lyDo = trim($_POST['lyDoBaoCao'] ?? '');
$reporterId = auth_user_id();

if ($lyDo === '') {
    json_response(['success' => false, 'message' => 'Vui lòng chọn lý do báo cáo.'], 400);
}

$link = null;
taoKetNoi($link);
$reportModel = new ReportModel();

try {
    if ($action === 'report-post') {
        $maBaiDang = trim($_POST['maBaiDang'] ?? '');
        if ($maBaiDang === '') {
            json_response(['success' => false, 'message' => 'Thiếu mã bài đăng.'], 400);
        }
        $postModel = new PostModel();
        if (!$postModel->postExists($link, $maBaiDang)) {
            json_response(['success' => false, 'message' => 'Bài đăng không tồn tại.'], 404);
        }
        if (!$reportModel->reportPost($link, $maBaiDang, $reporterId, $lyDo)) {
            json_response(['success' => false, 'message' => 'Không gửi được báo cáo.'], 500);
        }
        json_response(['success' => true, 'message' => 'Đã gửi báo cáo bài đăng. Cảm ơn bạn.']);
    }

    if ($action === 'report-account') {
        $maNguoiBiBaoCao = trim($_POST['maNguoiBiBaoCao'] ?? '');
        if ($maNguoiBiBaoCao === '') {
            json_response(['success' => false, 'message' => 'Thiếu tài khoản bị báo cáo.'], 400);
        }
        if ($maNguoiBiBaoCao === $reporterId) {
            json_response(['success' => false, 'message' => 'Không thể báo cáo chính mình.'], 400);
        }
        $userModel = new UserModel();
        if (!$userModel->getProfileById($link, $maNguoiBiBaoCao)) {
            json_response(['success' => false, 'message' => 'Tài khoản không tồn tại.'], 404);
        }
        if (!$reportModel->reportAccount($link, $maNguoiBiBaoCao, $reporterId, $lyDo)) {
            json_response(['success' => false, 'message' => 'Không gửi được báo cáo.'], 500);
        }
        json_response(['success' => true, 'message' => 'Đã gửi báo cáo tài khoản. Cảm ơn bạn.']);
    }

    json_response(['success' => false, 'message' => 'Action không hợp lệ.'], 400);
} catch (Throwable $ex) {
    json_response(['success' => false, 'message' => 'Lỗi server. Kiểm tra bảng BaoCaoBaiDang / BaoCaoTaiKhoan đã tạo chưa.'], 500);
} finally {
    giaiPhongBoNho($link, null);
}
