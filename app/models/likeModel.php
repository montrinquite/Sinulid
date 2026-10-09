<?php
require_once __DIR__ . '/Model.php';

class LikeModel extends Model {

    public function add(int $postId, int $userId): bool {
        $stmt = $this->db()->prepare(
            'INSERT IGNORE INTO likes (post_id, user_id) VALUES (:post_id, :user_id)'
        );
        $stmt -> execute([':post_id' => $postId, ':user_id' => $userId]);
        return $stmt -> rowCount() > 0;
    }

    public function remove(int $postId, int $userId): bool {
        $stmt = $this -> db() -> prepare(
            'DELETE FROM likes WHERE post_id = :post_id AND user_id = :user_id'
        );
        $stmt -> execute([':post_id' => $postId, ':user_id' => $userId]);
        return $stmt -> rowCount() > 0;
    }

    public function hasLiked(int $postId, int $userId): bool {
        $stmt = $this -> db() -> prepare(
            'SELECT 1 FROM likes WHERE :post_id = post_id AND :user_id = user_id'
        );
        $stmt -> execute([':post_id' => $postId, ':user_id' => $userId]);
        return $stmt -> fetchColumn();
    }

    public function countByPost(int $postId): int {
        $stmt = $this -> db() -> prepare(
            'SELECT COUNT (*) FROM likes WHERE post_id = :post_id'
        );
        $stmt -> execute([':post_id' => $postId]);
        return (int) $stmt -> fetchColumn();
    }

    public function getUserByPost(int $postId): array {
        $stmt = $this -> db() -> prepare(
            'SELECT u.id, u.username FROM likes l JOIN users u ON u.id = l.user_id WHERE l.post_id = :post_id ORDER BY l.created_at DESC'
        );
        $stmt -> execute([':post_id' => $postId]);
        return $stmt -> fetchAll();
    }
}