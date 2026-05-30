<?php

declare(strict_types=1);

require_once __DIR__ . '/../modules/db_module.php';
require_once __DIR__ . '/../modules/helpers.php';
require_once __DIR__ . '/../modules/auth.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/PostModel.php';

auth_require_login_json();

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$viewerId = auth_user_id();
$link = null;
taoKetNoi($link);

$userModel = new UserModel();
$postModel = new PostModel();

try {
    switch ($action) {
        case 'post-detail':
            handlePostDetail($link, $postModel, $viewerId);
            break;
        case 'add-comment':
            handleAddComment($link, $postModel, $viewerId);
            break;
        case 'toggle-follow':
            handleToggleFollow($link, $userModel, $viewerId);
            break;
        case 'follow-list':
            handleFollowList($link, $userModel, $viewerId);
            break;
        case 'toggle-like':
            handleToggleLike($link, $postModel, $viewerId);
            break;
        default:
            json_response(['success' => false, 'message' => 'Action không hợp lệ.'], 400);
    }
} catch (Throwable $ex) {
    json_response(['success' => false, 'message' => 'Lỗi server.'], 500);
} finally {
    giaiPhongBoNho($link, null);
}

function handlePostDetail(mysqli $link, PostModel $postModel, string $viewerId): void
{
    $maBaiDang = trim($_GET['maBaiDang'] ?? '');
    if ($maBaiDang === '') {
        json_response(['success' => false, 'message' => 'Thiếu mã bài đăng.'], 400);
    }

    $post = $postModel->getPostDetail($link, $maBaiDang, $viewerId);
    if (!$post) {
        json_response(['success' => false, 'message' => 'Không tìm thấy bài đăng.'], 404);
    }

    $comments = $postModel->getComments($link, $maBaiDang);
    $commentList = array_map(static function (array $c): array {
        return [
            'maBinhLuan' => $c['maBinhLuan'],
            'tenDangNhap' => $c['tenDangNhap'],
            'avatar' => profile_image_url($c['anhDaiDien']),
            'noiDung' => $c['noiDungBinhLuan'],
            'thoiGian' => formatTimeAgo($c['thoiGian']),
        ];
    }, $comments);

    $liked = !empty($post['phanUngCuaToi']);

    json_response([
        'success' => true,
        'post' => [
            'maBaiDang' => $post['maBaiDang'],
            'noiDung' => $post['noiDung'],
            'thoiGian' => formatTimeAgo($post['thoiGianDang']),
            'anhBaiDang' => post_image_url($post['duongDan']),
            'tenDangNhap' => $post['tenDangNhap'],
            'avatar' => profile_image_url($post['anhDaiDien']),
            'profileUrl' => profile_page_url($post['tenDangNhap']),
            'soPhanUng' => (int) $post['soPhanUng'],
            'daThich' => $liked,
        ],
        'comments' => $commentList,
    ]);
}

function handleAddComment(mysqli $link, PostModel $postModel, string $viewerId): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_response(['success' => false, 'message' => 'Chỉ hỗ trợ POST.'], 405);
    }

    $maBaiDang = trim($_POST['maBaiDang'] ?? '');
    $noiDung = trim($_POST['noiDung'] ?? '');

    if ($maBaiDang === '' || $noiDung === '') {
        json_response(['success' => false, 'message' => 'Vui lòng nhập nội dung bình luận.'], 400);
    }
    if (mb_strlen($noiDung) > 500) {
        json_response(['success' => false, 'message' => 'Bình luận tối đa 500 ký tự.'], 400);
    }
    if (!$postModel->postExists($link, $maBaiDang)) {
        json_response(['success' => false, 'message' => 'Bài đăng không tồn tại.'], 404);
    }

    $row = $postModel->addComment($link, $maBaiDang, $viewerId, $noiDung);
    if (!$row) {
        json_response(['success' => false, 'message' => 'Không thể lưu bình luận.'], 500);
    }

    json_response([
        'success' => true,
        'comment' => [
            'tenDangNhap' => $row['tenDangNhap'],
            'avatar' => profile_image_url($row['anhDaiDien']),
            'noiDung' => $row['noiDungBinhLuan'],
            'thoiGian' => 'Vừa xong',
        ],
    ]);
}

function handleToggleFollow(mysqli $link, UserModel $userModel, string $viewerId): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_response(['success' => false, 'message' => 'Chỉ hỗ trợ POST.'], 405);
    }

    $followerId = trim($_POST['maNguoiTheoDoi'] ?? $viewerId);
    $targetId = trim($_POST['maNguoiDuocTheoDoi'] ?? '');
    $action = trim($_POST['action'] ?? 'toggle');

    if ($targetId === '') {
        json_response(['success' => false, 'message' => 'Thiếu thông tin người dùng.'], 400);
    }
    if ($followerId === $targetId) {
        json_response(['success' => false, 'message' => 'Không thể theo dõi chính mình.'], 400);
    }

    $following = $userModel->toggleFollow($link, $followerId, $targetId, $action);

    json_response([
        'success' => true,
        'following' => $following,
        'targetFollowerCount' => $userModel->countFollowers($link, $targetId),
        'followerFollowingCount' => $userModel->countFollowing($link, $followerId),
    ]);
}

function handleFollowList(mysqli $link, UserModel $userModel, string $viewerId): void
{
    $profileId = trim($_GET['profile'] ?? '');
    $type = trim($_GET['type'] ?? 'followers');

    if ($profileId === '') {
        json_response(['success' => false, 'message' => 'Thiếu mã hồ sơ.'], 400);
    }
    if (!in_array($type, ['followers', 'following'], true)) {
        json_response(['success' => false, 'message' => 'Loại danh sách không hợp lệ.'], 400);
    }

    $rows = $userModel->getFollowList($link, $profileId, $type, $viewerId);
    $users = array_map(static function (array $row): array {
        return [
            'maNguoiDung' => $row['maNguoiDung'],
            'tenDangNhap' => $row['tenDangNhap'],
            'tenHienThi' => $row['tenHienThi'],
            'avatar' => profile_image_url($row['anhDaiDien']),
            'profileUrl' => profile_page_url($row['tenDangNhap']),
            'viewerFollows' => (bool) $row['viewerFollows'],
            'followsViewer' => (bool) $row['followsViewer'],
        ];
    }, $rows);

    json_response([
        'success' => true,
        'type' => $type,
        'title' => $type === 'followers' ? 'Người theo dõi' : 'Đang theo dõi',
        'users' => $users,
    ]);
}

function handleToggleLike(mysqli $link, PostModel $postModel, string $viewerId): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_response(['success' => false, 'message' => 'Chỉ hỗ trợ POST.'], 405);
    }

    $maBaiDang = trim($_POST['maBaiDang'] ?? '');
    if ($maBaiDang === '' || !$postModel->postExists($link, $maBaiDang)) {
        json_response(['success' => false, 'message' => 'Bài đăng không hợp lệ.'], 400);
    }

    $liked = $postModel->toggleLike($link, $maBaiDang, $viewerId);
    json_response([
        'success' => true,
        'liked' => $liked,
        'soPhanUng' => $postModel->countPhanUng($link, $maBaiDang),
    ]);
}
