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
}