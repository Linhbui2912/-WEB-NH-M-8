<?php

declare(strict_types=1);

require_once __DIR__ . '/homepage_helpers.php';

class LikeModel
{
    public static function isLiked(mysqli $link, string $postId, string $userId): bool
    {
        $postEsc = hp_esc($link, $postId);
        $userEsc = hp_esc($link, $userId);
        $sql = "
            SELECT 1 FROM PhanUng
            WHERE maBaiDang = '{$postEsc}' AND maNguoiDung = '{$userEsc}'
            LIMIT 1
        ";
        $result = chayTruyVanTraVeDL($link, $sql);
        $liked = $result && mysqli_num_rows($result) > 0;
        if ($result) {
            mysqli_free_result($result);
        }

        return $liked;
    }

    public static function countLikes(mysqli $link, string $postId): int
    {
        $postEsc = hp_esc($link, $postId);
        $sql = "SELECT COUNT(*) AS c FROM PhanUng WHERE maBaiDang = '{$postEsc}'";
        $result = chayTruyVanTraVeDL($link, $sql);
        $count = 0;
        if ($result && ($row = mysqli_fetch_assoc($result))) {
            $count = (int) ($row['c'] ?? 0);
            mysqli_free_result($result);
        }

        return $count;
    }

    /** @return array{liked:bool,like_count:int} */
    public static function toggle(mysqli $link, string $postId, string $userId): array
    {
        if (self::isLiked($link, $postId, $userId)) {
            $postEsc = hp_esc($link, $postId);
            $userEsc = hp_esc($link, $userId);
            chayTruyVanKhongTraVeDL(
                $link,
                "DELETE FROM PhanUng WHERE maBaiDang = '{$postEsc}' AND maNguoiDung = '{$userEsc}'"
            );

            return ['liked' => false, 'like_count' => self::countLikes($link, $postId)];
        }

        $postEsc = hp_esc($link, $postId);
        $userEsc = hp_esc($link, $userId);
        chayTruyVanKhongTraVeDL(
            $link,
            "INSERT INTO PhanUng (maBaiDang, maNguoiDung) VALUES ('{$postEsc}', '{$userEsc}')"
        );

        return ['liked' => true, 'like_count' => self::countLikes($link, $postId)];
    }
}
