<?php

declare(strict_types=1);

final class InteractionRepository
{
    public function __construct(private PDO $db)
    {
    }

    /** @return array{liked:bool,like_count:int} */
    public function toggleLike(int $postId, int $userId): array
    {
        $del = $this->db->prepare(
            'DELETE FROM post_likes WHERE post_id = :pid AND user_id = :uid'
        );
        $del->execute(['pid' => $postId, 'uid' => $userId]);

        if ($del->rowCount() > 0) {
            return ['liked' => false, 'like_count' => $this->countLikes($postId)];
        }

        $ins = $this->db->prepare(
            'INSERT INTO post_likes (post_id, user_id) VALUES (:pid, :uid)'
        );
        $ins->execute(['pid' => $postId, 'uid' => $userId]);

        return ['liked' => true, 'like_count' => $this->countLikes($postId)];
    }

    public function countLikes(int $postId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) AS c FROM post_likes WHERE post_id = :pid'
        );
        $stmt->execute(['pid' => $postId]);
        $row = $stmt->fetch();

        return (int) ($row['c'] ?? 0);
    }

    /** @return array{saved:bool} */
    public function toggleSave(int $postId, int $userId): array
    {
        $del = $this->db->prepare(
            'DELETE FROM post_saves WHERE post_id = :pid AND user_id = :uid'
        );
        $del->execute(['pid' => $postId, 'uid' => $userId]);

        if ($del->rowCount() > 0) {
            return ['saved' => false];
        }

        $ins = $this->db->prepare(
            'INSERT INTO post_saves (post_id, user_id) VALUES (:pid, :uid)'
        );
        $ins->execute(['pid' => $postId, 'uid' => $userId]);

        return ['saved' => true];
    }
}
