<?php

declare(strict_types=1);

require_once __DIR__ . '/homepage_helpers.php';

class ReportModel
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

    public static function submit(mysqli $link, string $postId, string $reporterId, string $reason): bool
    {
        $postEsc = hp_esc($link, $postId);
        $userEsc = hp_esc($link, $reporterId);
        $reasonEsc = hp_esc($link, $reason);
        $reportId = hp_esc($link, hp_new_id('BC'));

        $sql = "
            INSERT INTO BaoCaoBaiDang (maBaoCao, maBaiDang, maNguoiBaoCao, trangThai, lyDoBaoCao)
            VALUES ('{$reportId}', '{$postEsc}', '{$userEsc}', 'cho_duyet', '{$reasonEsc}')
        ";

        return (bool) chayTruyVanKhongTraVeDL($link, $sql);
    }
}
