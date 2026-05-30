<?php

declare(strict_types=1);

final class SearchModel
{
    /**
     * Tìm kiếm người dùng theo tenHienThi (partial match).
     * Chỉ trả về tài khoản role = 2 và đang hoạt_động.
     *
     * @return list<array<string,mixed>>
     */
    public static function searchByDisplayName(mysqli $link, string $keyword): array
    {
        if (trim($keyword) === '') {
            return [];
        }

        $stmt = mysqli_prepare(
            $link,
            "SELECT nd.maNguoiDung,
                    nd.tenDangNhap,
                    hs.tenHienThi,
                    hs.anhDaiDien
             FROM NguoiDung nd
             INNER JOIN HoSo hs ON hs.maNguoiDung = nd.maNguoiDung
             WHERE nd.trangThai = 'hoat_dong'
               AND nd.maQuyen = 2
               AND hs.tenHienThi LIKE ?
             ORDER BY hs.tenHienThi ASC
             LIMIT 30"
        );

        $like = '%' . $keyword . '%';
        mysqli_stmt_bind_param($stmt, 's', $like);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        mysqli_stmt_close($stmt);

        return $rows;
    }
}