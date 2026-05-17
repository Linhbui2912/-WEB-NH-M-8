<?php

declare(strict_types=1);

final class CommentRepository
{
    public function __construct(private PDO $db)
    {
    }

    /** @return list<array<string,mixed>> */
    public function listForPost(int $postId): array
    {
        $stmt = $this->db->prepare(
            <<<SQL
                SELECT c.body, c.created_at, u.username
                FROM comments c
                INNER JOIN users u ON u.id = c.user_id
                WHERE c.post_id = :pid
                ORDER BY c.created_at ASC
                SQL
        );
        $stmt->execute(['pid' => $postId]);

        /** @var list<array<string,mixed>> */
        return $stmt->fetchAll();
    }

    public function add(int $postId, int $userId, string $body): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO comments (post_id, user_id, body) VALUES (:pid, :uid, :body)'
        );
        $stmt->execute([
            'pid' => $postId,
            'uid' => $userId,
            'body' => $body,
        ]);
    }
}
