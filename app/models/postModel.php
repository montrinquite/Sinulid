<?php
declare(strict_types=1);

class PostModel extends Model
{
    private PDO $db;
    public function __construct() { $this->db = Database::connection(); }

    private const SELECT = 'SELECT p.id, p.user_id, p.content, p.image, p.created_at, p.updated_at,
            u.username, u.full_name, u.profile_image,
            (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id) AS like_count,
            (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS comment_count,
            EXISTS(SELECT 1 FROM likes lm WHERE lm.post_id = p.id AND lm.user_id = :viewer) AS liked_by_me
        FROM posts p JOIN users u ON u.id = p.user_id';

    public function feed(int $viewerId, int $limit, int $offset, string $sort = 'latest', ?int $authorId = null, ?string $keyword = null): array
    {
        $where = [];
        if ($authorId !== null) $where[] = 'p.user_id = :author';
        if ($keyword !== null && $keyword !== '') $where[] = 'p.content LIKE :kw';
        $order = $sort === 'top' ? 'like_count DESC, p.created_at DESC, p.id DESC' : 'p.created_at DESC, p.id DESC';

        $sql = self::SELECT . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . " ORDER BY {$order} LIMIT :lim OFFSET :off";
        $s = $this->db->prepare($sql);
        $s->bindValue(':viewer', $viewerId, PDO::PARAM_INT);
        if ($authorId !== null) $s->bindValue(':author', $authorId, PDO::PARAM_INT);
        if ($keyword !== null && $keyword !== '') $s->bindValue(':kw', '%' . $keyword . '%');
        $s->bindValue(':lim', $limit, PDO::PARAM_INT);
        $s->bindValue(':off', $offset, PDO::PARAM_INT);
        $s->execute();
        return $s->fetchAll();
    }

    public function find(int $id, int $viewerId): array|false
    {
        $s = $this->db->prepare(self::SELECT . ' WHERE p.id = :id');
        $s->bindValue(':viewer', $viewerId, PDO::PARAM_INT);
        $s->bindValue(':id', $id, PDO::PARAM_INT);
        $s->execute();
        return $s->fetch();
    }

    public function exists(int $id): bool
    {
        $s = $this->db->prepare('SELECT 1 FROM posts WHERE id = ?');
        $s->execute([$id]);
        return (bool)$s->fetchColumn();
    }

    public function countByUser(int $userId): int
    {
        $s = $this->db->prepare('SELECT COUNT(*) FROM posts WHERE user_id = ?');
        $s->execute([$userId]);
        return (int)$s->fetchColumn();
    }

    public function create(int $userId, string $content, ?string $image): int
    {
        $s = $this->db->prepare('INSERT INTO posts (user_id, content, image) VALUES (?, ?, ?)');
        $s->execute([$userId, $content, $image]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, string $content, ?string $image = null, bool $clearImage = false): void
    {
        if ($clearImage) {
            $s = $this->db->prepare('UPDATE posts SET content = ?, image = NULL WHERE id = ?');
            $s->execute([$content, $id]);
        } elseif ($image !== null) {
            $s = $this->db->prepare('UPDATE posts SET content = ?, image = ? WHERE id = ?');
            $s->execute([$content, $image, $id]);
        } else {
            $s = $this->db->prepare('UPDATE posts SET content = ? WHERE id = ?');
            $s->execute([$content, $id]);
        }
    }

    public function delete(int $id): void
    {
        $this->db->beginTransaction();
        try {
            $this->db->prepare('DELETE FROM likes WHERE post_id = ?')->execute([$id]);
            $this->db->prepare('DELETE FROM comments WHERE post_id = ?')->execute([$id]);
            $this->db->prepare('DELETE FROM posts WHERE id = ?')->execute([$id]);
            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}