<?php

declare(strict_types=1);

require_once __DIR__ . '/homepage_helpers.php';

class CommentModel
{
    public static function postExists(mysqli $link, string $postId): bool
    {
        $postEsc = hp_esc($link, $postId);
        $sql = "SELECT maBaiDang FROM BaiDang WHERE maBaiDang = '{$postEsc}' LIMIT 1";
        $result = chayTruyVanTraVeDL($link, $sql);
        $exists = $result && mysqli_num_rows($result) > 0;
        if ($result) {
            mysqli_free_result($result);
        }

        return $exists;
    }

    /** @return array<string,mixed>|null */
    public static function addComment(mysqli $link, string $postId, string $userId, string $body): ?array
    {
        $postEsc = hp_esc($link, $postId);
        $userEsc = hp_esc($link, $userId);
        $bodyEsc = hp_esc($link, $body);
        $commentId = hp_esc($link, hp_new_id('BL'));

        $insert = "
            INSERT INTO BinhLuan (maBinhLuan, maBaiDang, maNguoiDung, noiDungBinhLuan)
            VALUES ('{$commentId}', '{$postEsc}', '{$userEsc}', '{$bodyEsc}')
        ";
        if (!chayTruyVanKhongTraVeDL($link, $insert)) {
            return null;
        }

        $sql = "
            SELECT
                bl.maBinhLuan,
                bl.noiDungBinhLuan,
                bl.thoiGian,
                u.tenDangNhap,
                h.anhDaiDien
            FROM BinhLuan bl
            INNER JOIN NguoiDung u ON u.maNguoiDung = bl.maNguoiDung
            LEFT JOIN HoSo h ON h.maNguoiDung = bl.maNguoiDung
            WHERE bl.maBinhLuan = '{$commentId}'
            LIMIT 1
        ";
        $result = chayTruyVanTraVeDL($link, $sql);
        if (!$result || mysqli_num_rows($result) === 0) {
            if ($result) {
                mysqli_free_result($result);
            }

            return null;
        }

        $row = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

        return $row ?: null;
    }
}
