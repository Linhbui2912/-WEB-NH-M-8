<?php

declare(strict_types=1);

require_once __DIR__ . '/homepage_helpers.php';

class FeedModel
{
    /** @return list<array<string,mixed>> */
    public static function fetchPublicFeed(mysqli $link, string $viewerId): array
    {
        $viewerEsc = hp_esc($link, $viewerId);
        $sql = "
            SELECT
                b.maBaiDang AS id,
                b.maNguoiDung AS user_id,
                (
                    SELECT pt.duongDan
                    FROM PhuongTien pt
                    WHERE pt.maBaiDang = b.maBaiDang AND pt.loaiPhuongTien = 'image'
                    ORDER BY pt.maPhuongTien
                    LIMIT 1
                ) AS post_file,
                h.anhDaiDien AS avatar_file,
                b.noiDung AS caption,
                u.tenDangNhap AS username,
                (
                    SELECT COUNT(*) FROM PhanUng pu2 WHERE pu2.maBaiDang = b.maBaiDang
                ) AS paw_count,
                EXISTS(
                    SELECT 1 FROM PhanUng pu
                    WHERE pu.maBaiDang = b.maBaiDang AND pu.maNguoiDung = '{$viewerEsc}'
                ) AS liked
            FROM BaiDang b
            INNER JOIN NguoiDung u ON b.maNguoiDung = u.maNguoiDung
            LEFT JOIN HoSo h ON h.maNguoiDung = b.maNguoiDung
            WHERE b.cheDoHienThi = 'cong_khai'
              AND EXISTS (
                    SELECT 1 FROM PhuongTien pt
                    WHERE pt.maBaiDang = b.maBaiDang
                      AND pt.loaiPhuongTien = 'image'
                      AND pt.duongDan IS NOT NULL
                      AND pt.duongDan <> ''
              )
            ORDER BY b.maBaiDang DESC
        ";

        $result = chayTruyVanTraVeDL($link, $sql);
        $posts = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                if (hp_post_image_url((string) ($row['post_file'] ?? '')) === '') {
                    continue;
                }
                $posts[] = $row;
            }
            mysqli_free_result($result);
        }

        return $posts;
    }
}
