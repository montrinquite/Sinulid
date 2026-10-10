<?php
declare(strict_types=1);

class LikeModel extends Model
{
    private PDO $db;
    public function __construct() { $this->db = Database::connection(); }

    public function toggle(int $postId, int $userId): bool
    {
        $del = $this->db->prepare('DELETE FROM likes WHERE post_id = ? AND user_id = ?');
        $del->execute([$postId, $userId]);
        if ($del->rowCount() > 0) return false;

        $this->db->prepare('INSERT IGNORE INTO likes (post_id, user_id) VALUES (?, ?)')->execute([$postId, $userId]);
        return true;
    }

    public function count(int $postId): int
    {
        $s = $this->db->prepare('SELECT COUNT(*) FROM likes WHERE post_id = ?');
        $s->execute([$postId]);
        return (int)$s->fetchColumn();
    }
}