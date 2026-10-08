<?php
require_once __DIR__ . '/Model.php';

class postModel extends Model {
    public function create(int $userId, string $content) {
        $stmt = $this -> db() -> prepare(
            'INSERT INTO posts (user_id, content)'
        );
        $stmt -> execute([':user_id' => $userId, 'content'=> $content]);
        return (int) $this -> db() -> lastInsertId();
    }

    public function findById(int $id): ?array {
        $stmt = $this -> db() -> prepare($this -> baseSelect() . 'WHERE p.id = :id');
        $stmt -> execute ([':id' => $id]);
        return $stmt -> fetch() ?: null;
    }

    public function getAll(int $limit = 10, int $offset = 0): array {
        $stmt = $this -> db() -> prepare(
            $this -> baseSelect() . 'ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset'
        );
        $stmt -> bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt -> bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt -> execute();
        return $stmt -> fetchAll();
    }

    public function getByUser(int $userId, int $limit = 10, int $offset = 0): array {
        $stmt = $this -> db() -> prepare(
            $this -> baseSelect() . 'WHERE p.user_id = :user_id ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset'
        );
        $stmt -> bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt -> bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt -> bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt -> execute();
        return $stmt -> fetchAll();
    }

    public function count(): int {
        return (int) $this -> db() -> query('SELECT COUNT * FROM posts') -> fetchColumn();
    }

    public function update(int $id, int $userId, string $content): bool {
        $stmt = $this -> db() -> prepare(
            'UPDATE posts SET content = :content WHERE id = :id AND user_id = :user_id'
        );
        $stmt -> execute([':content' => $content, ':id' => $id, ':user_id' => $userId]);
        return $stmt -> rowCount() > 0;
    }

    public function delete(int $id, int $userId): bool {
        $stmt = $this -> db() -> prepare(
            'DELETE FROM posts WHERE id = :id AND user_id = :user_id'
        );
        $stmt -> execute([':id' => $id, ':user_id' => $userId]);
        return $stmt -> rowCount() > 0;
    }

    public function getOwnerId(int $id): ?int {
        $stmt = $this -> db() -> prepare('SELECT user_id FROM posts WHERE id = :id');
        $stmt -> execute([':id' =>  $id]);
        $v = $stmt -> fetchColumn();
        return $v === false? null : (int) $v;
    }

    private function baseSelect(): string {
        return 'SELECT p.id, p.user_id, p.content, p.create_at, u.username, (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id) AS like_count, (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS comment_count FROM post p JOIN users u ON u.id = p.user_id';
    }
}