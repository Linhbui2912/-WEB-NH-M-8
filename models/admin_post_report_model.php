<?php

declare(strict_types=1);

class PostReportModel
{
    /**
     * Lấy danh sách tất cả báo cáo bài đăng kèm thông tin người báo cáo
     * @return list<array<string,mixed>>
     */
    public static function fetchAll(mysqli $link): array
    {
        $sql = "
            SELECT
                bc.maBaoCao,
                bc.maBaiDang,
                bc.trangThai,
                bc.lyDoBaoCao,
                bc.thoiGianBaoCao,
                u.tenDangNhap AS tenNguoiBaoCao
            FROM BaoCaoBaiDang bc
            INNER JOIN NguoiDung u ON u.maNguoiDung = bc.maNguoiBaoCao
            ORDER BY bc.thoiGianBaoCao DESC
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

    /**
     * Lấy chi tiết 1 bài đăng để hiển thị card (ảnh, nội dung, bình luận, lượt paw)
     * @return array<string,mixed>|null
     */
    public static function fetchPostDetail(mysqli $link, string $maBaiDang): ?array
    {
        $esc = mysqli_real_escape_string($link, $maBaiDang);

        // Thông tin bài đăng
        $sql = "
            SELECT
                b.maBaiDang,
                b.noiDung,
                b.thoiGianDang,
                u.tenDangNhap,
                h.anhDaiDien,
                (
                    SELECT pt.duongDan
                    FROM PhuongTien pt
                    WHERE pt.maBaiDang = b.maBaiDang AND pt.loaiPhuongTien = 'image'
                    ORDER BY pt.maPhuongTien
                    LIMIT 1
                ) AS post_file,
                (SELECT COUNT(*) FROM PhanUng pu WHERE pu.maBaiDang = b.maBaiDang) AS paw_count
            FROM BaiDang b
            INNER JOIN NguoiDung u ON u.maNguoiDung = b.maNguoiDung
            LEFT JOIN HoSo h ON h.maNguoiDung = b.maNguoiDung
            WHERE b.maBaiDang = '{$esc}'
            LIMIT 1
        ";
        $result = chayTruyVanTraVeDL($link, $sql);
        if (!$result || mysqli_num_rows($result) === 0) {
            return null;
        }
        $post = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

        // Bình luận
        $sqlCmt = "
            SELECT
                bl.noiDungBinhLuan,
                bl.thoiGian,
                u.tenDangNhap,
                h.anhDaiDien
            FROM BinhLuan bl
            INNER JOIN NguoiDung u ON u.maNguoiDung = bl.maNguoiDung
            LEFT JOIN HoSo h ON h.maNguoiDung = bl.maNguoiDung
            WHERE bl.maBaiDang = '{$esc}'
            ORDER BY bl.thoiGian ASC
        ";
        $resCmt = chayTruyVanTraVeDL($link, $sqlCmt);
        $comments = [];
        if ($resCmt) {
            while ($row = mysqli_fetch_assoc($resCmt)) {
                $comments[] = $row;
            }
            mysqli_free_result($resCmt);
        }
        $post['comments'] = $comments;
        return $post;
    }

    /**
     * Xóa bài đăng và cập nhật trạng thái báo cáo → 'da_xoa_bai_dang'
     */
    public static function deletePost(mysqli $link, string $maBaiDang, string $maBaoCao): bool
    {
        $escPost = mysqli_real_escape_string($link, $maBaiDang);
        $escBC   = mysqli_real_escape_string($link, $maBaoCao);

        // Cập nhật trạng thái trước khi xóa (vì ON DELETE CASCADE sẽ xóa luôn báo cáo)
        $sqlUpdate = "
            UPDATE BaoCaoBaiDang
            SET trangThai = 'da_xoa_bai_dang'
            WHERE maBaoCao = '{$escBC}'
        ";
        chayTruyVanKhongTraVeDL($link, $sqlUpdate);

        // Xóa bài đăng (cascade tự xóa PhuongTien, BinhLuan, PhanUng)
        $sqlDelete = "DELETE FROM BaiDang WHERE maBaiDang = '{$escPost}'";
        return (bool) chayTruyVanKhongTraVeDL($link, $sqlDelete);
    }

    /**
     * Từ chối báo cáo → cập nhật trạng thái 'tu_choi_bao_cao', bài đăng giữ nguyên
     */
    public static function rejectReport(mysqli $link, string $maBaoCao): bool
    {
        $escBC = mysqli_real_escape_string($link, $maBaoCao);
        $sql = "
            UPDATE BaoCaoBaiDang
            SET trangThai = 'tu_choi_bao_cao'
            WHERE maBaoCao = '{$escBC}'
        ";
        return (bool) chayTruyVanKhongTraVeDL($link, $sql);
    }
}