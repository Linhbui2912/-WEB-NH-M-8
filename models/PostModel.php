<?php

declare(strict_types=1);

final class PostModel
{
    /** @return list<array<string,mixed>> */
    public function getPostsByUser(mysqli $link, string $userId, bool $ownProfile): array
    {
        $visibility = $ownProfile ? '' : " AND bd.cheDoHienThi = 'cong_khai'";
        $sql = "SELECT bd.maBaiDang, bd.noiDung, bd.thoiGianDang, pt.duongDan,
                (SELECT COUNT(*) FROM BinhLuan bl WHERE bl.maBaiDang = bd.maBaiDang) AS soBinhLuan,
                (SELECT COUNT(*) FROM PhanUng pu WHERE pu.maBaiDang = bd.maBaiDang) AS soPhanUng
                FROM BaiDang bd
                INNER JOIN PhuongTien pt ON pt.maBaiDang = bd.maBaiDang AND pt.loaiPhuongTien = 'image'
                WHERE bd.maNguoiDung = ?{$visibility}
                ORDER BY bd.thoiGianDang DESC";

        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 's', $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $rows;
    }

    public function getPostDetail(mysqli $link, string $maBaiDang, string $viewerId): ?array
    {
        $stmt = mysqli_prepare(
            $link,
            "SELECT bd.maBaiDang, bd.noiDung, bd.thoiGianDang, bd.maNguoiDung,
                    nd.tenDangNhap, hs.tenHienThi, hs.anhDaiDien, pt.duongDan,
                    (SELECT COUNT(*) FROM PhanUng pu WHERE pu.maBaiDang = bd.maBaiDang) AS soPhanUng,
                    (SELECT COUNT(*) FROM BinhLuan bl WHERE bl.maBaiDang = bd.maBaiDang) AS soBinhLuan,
                    (SELECT COUNT(*) FROM PhanUng pu
                WHERE pu.maBaiDang = bd.maBaiDang AND pu.maNguoiDung = ?) AS phanUngCuaToi
             FROM BaiDang bd
             INNER JOIN NguoiDung nd ON nd.maNguoiDung = bd.maNguoiDung
             LEFT JOIN HoSo hs ON hs.maNguoiDung = nd.maNguoiDung
             LEFT JOIN PhuongTien pt ON pt.maBaiDang = bd.maBaiDang AND pt.loaiPhuongTien = 'image'
             WHERE bd.maBaiDang = ?
             LIMIT 1"
        );
        mysqli_stmt_bind_param($stmt, 'ss', $viewerId, $maBaiDang);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result) ?: null;
        mysqli_stmt_close($stmt);
        return $row;
    }

    /** @return list<array<string,mixed>> */
    public function getComments(mysqli $link, string $maBaiDang): array
    {
        $stmt = mysqli_prepare(
            $link,
            "SELECT bl.maBinhLuan, bl.noiDungBinhLuan, bl.thoiGian,
                    nd.tenDangNhap, hs.tenHienThi, hs.anhDaiDien
             FROM BinhLuan bl
             INNER JOIN NguoiDung nd ON nd.maNguoiDung = bl.maNguoiDung
             LEFT JOIN HoSo hs ON hs.maNguoiDung = nd.maNguoiDung
             WHERE bl.maBaiDang = ?
             ORDER BY bl.thoiGian ASC"
        );
        mysqli_stmt_bind_param($stmt, 's', $maBaiDang);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $rows;
    }

    public function addComment(mysqli $link, string $maBaiDang, string $maNguoiDung, string $noiDung): ?array
    {
        $maBinhLuan = 'BL' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        $stmt = mysqli_prepare(
            $link,
            'INSERT INTO BinhLuan (maBinhLuan, maBaiDang, maNguoiDung, noiDungBinhLuan) VALUES (?, ?, ?, ?)'
        );
        mysqli_stmt_bind_param($stmt, 'ssss', $maBinhLuan, $maBaiDang, $maNguoiDung, $noiDung);
        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return null;
        }
        mysqli_stmt_close($stmt);

        $stmt = mysqli_prepare(
            $link,
            "SELECT bl.maBinhLuan, bl.noiDungBinhLuan, bl.thoiGian,
                    nd.tenDangNhap, hs.tenHienThi, hs.anhDaiDien
             FROM BinhLuan bl
             INNER JOIN NguoiDung nd ON nd.maNguoiDung = bl.maNguoiDung
             LEFT JOIN HoSo hs ON hs.maNguoiDung = nd.maNguoiDung
             WHERE bl.maBinhLuan = ?
             LIMIT 1"
        );
        mysqli_stmt_bind_param($stmt, 's', $maBinhLuan);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result) ?: null;
        mysqli_stmt_close($stmt);
        return $row;
    }

    public function postExists(mysqli $link, string $maBaiDang): bool
    {
        $stmt = mysqli_prepare($link, 'SELECT 1 FROM BaiDang WHERE maBaiDang = ? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 's', $maBaiDang);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $exists = (bool) mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $exists;
    }

    /** Toggle yêu thích (PhanUng loai yeu_thich / thích) */
    public function toggleLike(mysqli $link, string $maBaiDang, string $maNguoiDung): bool
    {
        $stmt = mysqli_prepare(
            $link,
            "SELECT 1 FROM PhanUng 
                WHERE maBaiDang = ? AND maNguoiDung = ?
                LIMIT 1"
        );
        mysqli_stmt_bind_param($stmt, 'ss', $maBaiDang, $maNguoiDung);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $exists = (bool) mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($exists) {
            $stmt = mysqli_prepare(
                $link,
                "DELETE FROM PhanUng 
                    WHERE maBaiDang = ? AND maNguoiDung = ?"
            );
            mysqli_stmt_bind_param($stmt, 'ss', $maBaiDang, $maNguoiDung);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return false;
        }

        $stmt = mysqli_prepare(
            $link,
            "INSERT INTO PhanUng (maBaiDang, maNguoiDung) VALUES (?, ?)"
        );
        mysqli_stmt_bind_param($stmt, 'ss', $maBaiDang, $maNguoiDung);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return true;
    }

    public function countPhanUng(mysqli $link, string $maBaiDang): int
    {
        $stmt = mysqli_prepare($link, 'SELECT COUNT(*) AS c FROM PhanUng WHERE maBaiDang = ?');
        mysqli_stmt_bind_param($stmt, 's', $maBaiDang);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return (int) ($row['c'] ?? 0);
    }
}
