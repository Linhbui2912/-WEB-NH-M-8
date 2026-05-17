<?php

declare(strict_types=1);

final class PostRepository
{
    public function __construct(private PDO $db)
    {
    }

    /** @return list<array<string,mixed>> */
    public function fetchFeedForViewer(int $viewerId): array
    {
        $sql = <<<SQL
            SELECT
                p.id,
                p.user_id,
                p.image_path,
                p.caption,
                p.created_at,
                u.username,
                u.display_name,
                u.avatar_path,
                (SELECT COUNT(*) FROM post_likes pl WHERE pl.post_id = p.id) AS like_count,
                EXISTS(
                    SELECT 1 FROM post_likes pl
                    WHERE pl.post_id = p.id AND pl.user_id = :viewer_like
                ) AS liked,
                EXISTS(
                    SELECT 1 FROM post_saves ps
                    WHERE ps.post_id = p.id AND ps.user_id = :viewer_save
                ) AS saved
            FROM posts p
            INNER JOIN users u ON u.id = p.user_id
            ORDER BY p.created_at DESC
            SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'viewer_like' => $viewerId,
            'viewer_save' => $viewerId,
        ]);

        /** @var list<array<string,mixed>> */
        return $stmt->fetchAll();
    }

    /** @return list<array<string,mixed>> */
    public function fetchSavedForUser(int $userId): array
    {
        $sql = <<<SQL
            SELECT
                p.id,
                p.user_id,
                p.image_path,
                p.caption,
                p.created_at,
                u.username,
                u.display_name,
                u.avatar_path,
                EXISTS(
                    SELECT 1 FROM post_likes pl
                    WHERE pl.post_id = p.id AND pl.user_id = :viewer_like
                ) AS liked,
                1 AS saved
            FROM post_saves ps
            INNER JOIN posts p ON p.id = ps.post_id
            INNER JOIN users u ON u.id = p.user_id
            WHERE ps.user_id = :uid
            ORDER BY ps.created_at DESC
            SQL;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'uid' => $userId,
            'viewer_like' => $userId,
        ]);

        /** @var list<array<string,mixed>> */
        return $stmt->fetchAll();
    }

    public function create(int $userId, string $relativeImagePath, string $caption): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO posts (user_id, image_path, caption) VALUES (:uid, :img, :cap)'
        );
        $stmt->execute([
            'uid' => $userId,
            'img' => $relativeImagePath,
            'cap' => $caption,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /** @return array<string,mixed>|null */
    public function findUserProfile(int $userId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, username, display_name, avatar_path, bio FROM users WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $userId]);
        $row = $stmt->fetch();

        return $row !== false ? $row : null;
    }

    /** @return list<array<string,mixed>> */
    public function fetchPostsByUserId(int $userId): array
    {
        $stmt = $this->db->prepare(
            <<<SQL
                SELECT id, image_path, caption, created_at
                FROM posts
                WHERE user_id = :uid
                ORDER BY created_at DESC
                SQL
        );
        $stmt->execute(['uid' => $userId]);

        /** @var list<array<string,mixed>> */
        return $stmt->fetchAll();
    }
}
