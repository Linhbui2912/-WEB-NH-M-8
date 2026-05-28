<?php

declare(strict_types=1);

require_once __DIR__ . '/homepage_helpers.php';

class PostDetailModel
{
    /** @return array<string,mixed>|null */
    public static function fetchPost(mysqli $link, string $postId, string $viewerId): ?array
    {
        $postEsc = hp_esc($link, $postId);
        $viewerEsc = hp_esc($link, $viewerId);
        $sql = "
            SELECT
                b.maBaiDang,
                b.maNguoiDung,
                b.noiDung,
                u.tenDangNhap,
                h.tenHienThi,
                h.anhDaiDien,
                (
                    SELECT pt.duongDan
                    FROM PhuongTien pt
                    WHERE pt.maBaiDang = b.maBaiDang AND pt.loaiPhuongTien = 'image'
                    ORDER BY pt.maPhuongTien
                    LIMIT 1
                ) AS post_file,
                (
                    SELECT COUNT(*) FROM PhanUng pu WHERE pu.maBaiDang = b.maBaiDang
                ) AS paw_count,
                EXISTS(
                    SELECT 1 FROM PhanUng pu
                    WHERE pu.maBaiDang = b.maBaiDang AND pu.maNguoiDung = '{$viewerEsc}'
                ) AS liked
            FROM BaiDang b
            INNER JOIN NguoiDung u ON u.maNguoiDung = b.maNguoiDung
            LEFT JOIN HoSo h ON h.maNguoiDung = b.maNguoiDung
            WHERE b.maBaiDang = '{$postEsc}'
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

    /** @return list<array<string,mixed>> */
    public static function fetchComments(mysqli $link, string $postId): array
    {
        $postEsc = hp_esc($link, $postId);
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
            WHERE bl.maBaiDang = '{$postEsc}'
            ORDER BY bl.thoiGian ASC
        ";

        $result = chayTruyVanTraVeDL($link, $sql);
        $rows = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
            }
            mysqli_free_result($result);
        }

        return $rows;
    }
}
