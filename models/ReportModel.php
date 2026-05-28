<?php
declare(strict_types=1);

require_once __DIR__ . '/../modules/helpers.php';
require_once __DIR__ . '/homepage_helpers.php';

final class ReportModel
{
    /** @var list<string> */
    public const REASONS = [
        'Vấn đề liên quan đến người dưới 18 tuổi',
        'Bắt nạt, lạm dụng, ngược đãi',
        'Có hành vi tự hại',
        'Nội dung kích động thù ghét',
        'Vi phạm Quyền sở hữu trí tuệ',
    ];

    public static function isValidReason(string $reason): bool
    {
        return in_array($reason, self::REASONS, true);
    }

    // Của bạn (homepage) - báo cáo bài đăng
    public static function submit(mysqli $link, string $postId, string $reporterId, string $reason): bool
    {
        $postEsc     = hp_esc($link, $postId);
        $userEsc     = hp_esc($link, $reporterId);
        $reasonEsc   = hp_esc($link, $reason);
        $reportId    = hp_esc($link, hp_new_id('BC'));
        $sql = "
            INSERT INTO BaoCaoBaiDang (maBaoCao, maBaiDang, maNguoiBaoCao, trangThai, lyDoBaoCao)
            VALUES ('{$reportId}', '{$postEsc}', '{$userEsc}', 'cho_duyet', '{$reasonEsc}')
        ";
        return (bool) chayTruyVanKhongTraVeDL($link, $sql);
    }

    // Của bạn bè (profile) - báo cáo bài đăng qua prepared statement
    public function reportPost(mysqli $link, string $maBaiDang, string $maNguoiBaoCao, string $lyDo): bool
    {
        $maBaoCao = generate_id('BC', 12);
        $stmt = mysqli_prepare(
            $link,
            "INSERT INTO BaoCaoBaiDang (maBaoCao, maBaiDang, maNguoiBaoCao, trangThai, lyDoBaoCao)
             VALUES (?, ?, ?, 'cho_duyet', ?)"
        );
        mysqli_stmt_bind_param($stmt, 'ssss', $maBaoCao, $maBaiDang, $maNguoiBaoCao, $lyDo);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    // Của bạn bè (profile) - báo cáo tài khoản
    public function reportAccount(mysqli $link, string $maNguoiBiBaoCao, string $maNguoiBaoCao, string $lyDo): bool
    {
        if ($maNguoiBiBaoCao === $maNguoiBaoCao) {
            return false;
        }
        $maBaoCao = generate_id('BC', 12);
        $stmt = mysqli_prepare(
            $link,
            "INSERT INTO BaoCaoTaiKhoan (maBaoCao, maNguoiBiBaoCao, maNguoiBaoCao, trangThai, lyDoBaoCao)
             VALUES (?, ?, ?, 'cho_duyet', ?)"
        );
        mysqli_stmt_bind_param($stmt, 'ssss', $maBaoCao, $maNguoiBiBaoCao, $maNguoiBaoCao, $lyDo);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }
}