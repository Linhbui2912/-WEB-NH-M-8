<?php

declare(strict_types=1);

require_once __DIR__ . '/../modules/db_module.php';
require_once __DIR__ . '/../modules/helpers.php';
require_once __DIR__ . '/../modules/auth.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/PostModel.php';

final class ProfileController
{
    public static function show(): void
    {
        auth_require_login();
        $viewerId = auth_user_id();
        $profileUserId = isset($_GET['id']) ? trim((string) $_GET['id']) : null;
        $username = isset($_GET['user']) ? trim((string) $_GET['user']) : null;

        $error = null;
        $profile = null;
        $posts = [];
        $postCount = 0;
        $followerCount = 0;
        $followingCount = 0;
        $isFollowing = false;
        $isOwnProfile = false;

        $link = null;
        taoKetNoi($link);

        try {
            $userModel = new UserModel();
            $postModel = new PostModel();

            if (!$profileUserId && ($username === null || $username === '')) {
                $username = $userModel->getUsernameById($link, $viewerId) ?? 'bob';
            }

            if ($profileUserId) {
                $profile = $userModel->getProfileById($link, $profileUserId);
            } elseif ($username !== null && $username !== '') {
                $profile = $userModel->getProfileByUsername($link, $username);
            }

            if (!$profile) {
                $error = 'Không tìm thấy hồ sơ người dùng.';
            } else {
                $profileUserId = $profile['maNguoiDung'];
                $isOwnProfile = $viewerId === $profileUserId;
                $postCount = $userModel->countPosts($link, $profileUserId);
                $followerCount = $userModel->countFollowers($link, $profileUserId);
                $followingCount = $userModel->countFollowing($link, $profileUserId);
                if (!$isOwnProfile) {
                    $isFollowing = $userModel->isFollowing($link, $viewerId, $profileUserId);
                }
                $posts = $postModel->getPostsByUser($link, $profileUserId, $isOwnProfile);
            }
        } catch (Throwable $ex) {
            $error = 'Không kết nối được CSDL. Kiểm tra VPN và cấu hình modules/config.php.';
        }

        giaiPhongBoNho($link, null);

        $activeNav = 'profile';
        $viewerIdForView = $viewerId;
        require __DIR__ . '/../views/profile_view.php';
        // Cuối file profile_controller.php - chỉ define class, không gọi gì
// Class ProfileController được define ở trên
    }
}
