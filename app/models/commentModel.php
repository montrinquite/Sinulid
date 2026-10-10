<?php
declare(strict_types=1);

class CommentModel extends Model
{
    private PDO $db;
    public function __construct() { $this->db = Database::connection(); }

    public function postExists(int $postId): bool
    {
        $s = $this->db->prepare('SELECT 1 FROM posts WHERE id = ?');
        $s->execute([$postId]);
        return (bool)$s->fetchColumn();
    }

    public function getByPost(int $postId): array
    {
        $s = $this->db->prepare(
            'SELECT c.id, c.post_id, c.user_id, c.content, c.created_at, c.updated_at,
                    u.username, u.full_name, u.profile_image
             FROM comments c JOIN users u ON u.id = c.user_id
             WHERE c.post_id = ? ORDER BY c.created_at ASC, c.id ASC'
        );
        $s->execute([$postId]);
        return $s->fetchAll();
    }

    public function find(int $id): array|false
    {
        $s = $this->db->prepare(
            'SELECT c.id, c.post_id, c.user_id, c.content, c.created_at, c.updated_at,
                    u.username, u.full_name, u.profile_image
             FROM comments c JOIN users u ON u.id = c.user_id WHERE c.id = ?'
        );
        $s->execute([$id]);
        return $s->fetch();
    }

    public function countByPost(int $postId): int
    {
        $s = $this->db->prepare('SELECT COUNT(*) FROM comments WHERE post_id = ?');
        $s->execute([$postId]);
        return (int)$s->fetchColumn();
    }

    public function create(int $postId, int $userId, string $content): int
    {
        $s = $this->db->prepare('INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)');
        $s->execute([$postId, $userId, $content]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, string $content): void
    {
        $s = $this->db->prepare('UPDATE comments SET content = ? WHERE id = ?');
        $s->execute([$content, $id]);
    }

    public function delete(int $id): void
    {
        $s = $this->db->prepare('DELETE FROM comments WHERE id = ?');
        $s->execute([$id]);
    }
}