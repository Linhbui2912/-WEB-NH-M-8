<?php

declare(strict_types=1);

require_once __DIR__ . '/../modules/helpers.php';

final class ReportModel
{
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
