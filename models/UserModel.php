<?php

declare(strict_types=1);

require_once __DIR__ . '/../modules/db_module.php';

final class UserModel
{
    public function getProfileByUsername(mysqli $link, string $username): ?array
    {
        $stmt = mysqli_prepare(
            $link,
            "SELECT nd.maNguoiDung, nd.tenDangNhap, hs.tenHienThi, hs.anhDaiDien, hs.moTa
             FROM NguoiDung nd
             INNER JOIN HoSo hs ON hs.maNguoiDung = nd.maNguoiDung
             WHERE nd.tenDangNhap = ? AND nd.trangThai = 'hoat_dong'
             LIMIT 1"
        );
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result) ?: null;
        mysqli_stmt_close($stmt);
        return $row;
    }

    public function getProfileById(mysqli $link, string $userId): ?array
    {
        $stmt = mysqli_prepare(
            $link,
            "SELECT nd.maNguoiDung, nd.tenDangNhap, hs.tenHienThi, hs.anhDaiDien, hs.moTa
             FROM NguoiDung nd
             INNER JOIN HoSo hs ON hs.maNguoiDung = nd.maNguoiDung
             WHERE nd.maNguoiDung = ? AND nd.trangThai = 'hoat_dong'
             LIMIT 1"
        );
        mysqli_stmt_bind_param($stmt, 's', $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result) ?: null;
        mysqli_stmt_close($stmt);
        return $row;
    }

    public function getUsernameById(mysqli $link, string $userId): ?string
    {
        $stmt = mysqli_prepare(
            $link,
            "SELECT tenDangNhap FROM NguoiDung WHERE maNguoiDung = ? AND trangThai = 'hoat_dong' LIMIT 1"
        );
        mysqli_stmt_bind_param($stmt, 's', $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $row['tenDangNhap'] ?? null;
    }

    public function countPosts(mysqli $link, string $userId): int
    {
        $stmt = mysqli_prepare($link, 'SELECT COUNT(*) AS c FROM BaiDang WHERE maNguoiDung = ?');
        mysqli_stmt_bind_param($stmt, 's', $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return (int) ($row['c'] ?? 0);
    }

    public function countFollowers(mysqli $link, string $userId): int
    {
        $stmt = mysqli_prepare($link, 'SELECT COUNT(*) AS c FROM TheoDoi WHERE maNguoiDuocTheoDoi = ?');
        mysqli_stmt_bind_param($stmt, 's', $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return (int) ($row['c'] ?? 0);
    }

    public function countFollowing(mysqli $link, string $userId): int
    {
        $stmt = mysqli_prepare($link, 'SELECT COUNT(*) AS c FROM TheoDoi WHERE maNguoiTheoDoi = ?');
        mysqli_stmt_bind_param($stmt, 's', $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return (int) ($row['c'] ?? 0);
    }

    public function isFollowing(mysqli $link, string $followerId, string $targetId): bool
    {
        $stmt = mysqli_prepare(
            $link,
            'SELECT 1 FROM TheoDoi WHERE maNguoiTheoDoi = ? AND maNguoiDuocTheoDoi = ? LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'ss', $followerId, $targetId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $exists = (bool) mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $exists;
    }

    public function toggleFollow(mysqli $link, string $followerId, string $targetId, string $action): bool
    {
        $already = $this->isFollowing($link, $followerId, $targetId);
        $shouldFollow = $action === 'follow' ? true : ($action === 'unfollow' ? false : !$already);

        if ($shouldFollow && !$already) {
            $stmt = mysqli_prepare(
                $link,
                'INSERT INTO TheoDoi (maNguoiTheoDoi, maNguoiDuocTheoDoi) VALUES (?, ?)'
            );
            mysqli_stmt_bind_param($stmt, 'ss', $followerId, $targetId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        } elseif (!$shouldFollow && $already) {
            $stmt = mysqli_prepare(
                $link,
                'DELETE FROM TheoDoi WHERE maNguoiTheoDoi = ? AND maNguoiDuocTheoDoi = ?'
            );
            mysqli_stmt_bind_param($stmt, 'ss', $followerId, $targetId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        return $shouldFollow;
    }

    /** @return list<array<string,mixed>> */
    public function getFollowList(mysqli $link, string $profileId, string $type, string $viewerId): array
    {
        if ($type === 'followers') {
            $sql = "SELECT nd.maNguoiDung, nd.tenDangNhap, hs.tenHienThi, hs.anhDaiDien,
                    EXISTS(SELECT 1 FROM TheoDoi td2 WHERE td2.maNguoiTheoDoi = ? AND td2.maNguoiDuocTheoDoi = nd.maNguoiDung) AS viewerFollows,
                    EXISTS(SELECT 1 FROM TheoDoi td3 WHERE td3.maNguoiTheoDoi = nd.maNguoiDung AND td3.maNguoiDuocTheoDoi = ?) AS followsViewer
                    FROM TheoDoi td
                    INNER JOIN NguoiDung nd ON nd.maNguoiDung = td.maNguoiTheoDoi
                    LEFT JOIN HoSo hs ON hs.maNguoiDung = nd.maNguoiDung
                    WHERE td.maNguoiDuocTheoDoi = ?
                    ORDER BY td.thoiGianTheoDoi DESC";
        } else {
            $sql = "SELECT nd.maNguoiDung, nd.tenDangNhap, hs.tenHienThi, hs.anhDaiDien,
                    EXISTS(SELECT 1 FROM TheoDoi td2 WHERE td2.maNguoiTheoDoi = ? AND td2.maNguoiDuocTheoDoi = nd.maNguoiDung) AS viewerFollows,
                    EXISTS(SELECT 1 FROM TheoDoi td3 WHERE td3.maNguoiTheoDoi = nd.maNguoiDung AND td3.maNguoiDuocTheoDoi = ?) AS followsViewer
                    FROM TheoDoi td
                    INNER JOIN NguoiDung nd ON nd.maNguoiDung = td.maNguoiDuocTheoDoi
                    LEFT JOIN HoSo hs ON hs.maNguoiDung = nd.maNguoiDung
                    WHERE td.maNguoiTheoDoi = ?
                    ORDER BY td.thoiGianTheoDoi DESC";
        }

        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, 'sss', $viewerId, $viewerId, $profileId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $rows;
    }

    public function updateHoSo(mysqli $link, string $userId, string $tenHienThi, string $moTa, ?string $anhDaiDien): bool
    {
        if ($anhDaiDien !== null) {
            $stmt = mysqli_prepare(
                $link,
                'UPDATE HoSo SET tenHienThi = ?, moTa = ?, anhDaiDien = ? WHERE maNguoiDung = ?'
            );
            mysqli_stmt_bind_param($stmt, 'ssss', $tenHienThi, $moTa, $anhDaiDien, $userId);
        } else {
            $stmt = mysqli_prepare(
                $link,
                'UPDATE HoSo SET tenHienThi = ?, moTa = ? WHERE maNguoiDung = ?'
            );
            mysqli_stmt_bind_param($stmt, 'sss', $tenHienThi, $moTa, $userId);
        }
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }
}
