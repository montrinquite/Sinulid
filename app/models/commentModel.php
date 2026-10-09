<?php
require_once __DIR__ . '/Model.php';

class CommentModel extends Model {
    public function create(int $postId, int $userId, string $content): bool {
        $stmt = $this -> db() -> prepare(
            'INSERT INTO comments (post_id, user_id, content) VALUES (:post_id, :user_id, :content)'
        );
        $stmt -> execute([':post_id' => $postId, ':user_id' => $userId, ':content' => $content]);
        return $stmt -> rowCount() > 0;
    }

    public function findById(int $id): ?array {
        $stmt = $this -> db() -> prepare(
            'SELECT c.*, u.username FROM comments c JOIN users u ON u.id = c.user_id WHERE c.id = :id'
        );
        $stmt -> execute([':id' => $id]);
        return $stmt -> fetch() ?: null;
    }

    public function getByPost(int $postId): array {
        $stmt = $this -> db() -> prepare(
            'SELECT c.id, c.post_id, c.user_id, c.content, c.created_at, u.username FROM comments c JOIN users u ON u.id = c.user_id WHERE c.post_id = :post_id ORDER BY c.created_at ASC'
        );
        $stmt -> execute([':post_id' => $postId]);
        return $stmt -> fetchAll();
    }

    public function countByPost(int $postId): int {
        $stmt = $this -> db() -> prepare(
            'SELECT COUNT(*) FROM comments WHERE post_id = :post_id'
        );
        $stmt -> execute([':post_id' => $postId]);
        return (int) $stmt -> fetchColumn();
    }

    public function update(int $id, int $userId, string $content): bool {
        $stmt = $this -> db() -> prepare(
            'UPDATE comments SET content = :content WHERE id = :id AND user_id = :user_id'
        );
        $stmt -> execute([':content' => $content, ':id' => $id, ':user_id' => $userId]);
        return $stmt -> rowCount() > 0;
    }

    public function delete(int $id, int $userId,): bool {
        $stmt = $this -> db() -> prepare(
            'DELETE FROM comments WHERE id = :id AND user_id = :user_id'
        );
        $stmt -> execute([':id' => $id, ':user_id' => $userId]);
        return $stmt -> rowCount() > 0;
    }
}