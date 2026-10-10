<?php
declare(strict_types=1);

class UserModel extends Model
{
    private PDO $db;
    public function __construct() { $this->db = Database::connection(); }

    public function findById(int $id): array|false
    {
        $s = $this->db->prepare('SELECT id, username, email, full_name, bio, profile_image, created_at FROM users WHERE id = ?');
        $s->execute([$id]);
        return $s->fetch();
    }

    public function findByLogin(string $identifier): array|false
    {
        $s = $this->db->prepare('SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1');
        $s->execute([$identifier, strtolower($identifier)]);
        return $s->fetch();
    }

    public function findByUsername(string $username): array|false
    {
        $s = $this->db->prepare('SELECT id, username, email, full_name, bio, profile_image, created_at FROM users WHERE username = ?');
        $s->execute([$username]);
        return $s->fetch();
    }

    public function emailExists(string $email): bool
    {
        $s = $this->db->prepare('SELECT 1 FROM users WHERE email = ?');
        $s->execute([$email]);
        return (bool)$s->fetchColumn();
    }

    public function usernameExists(string $username): bool
    {
        $s = $this->db->prepare('SELECT 1 FROM users WHERE username = ?');
        $s->execute([$username]);
        return (bool)$s->fetchColumn();
    }

    public function create(string $username, string $email, string $passwordHash, string $fullName): int
    {
        $s = $this->db->prepare('INSERT INTO users (username, email, password, full_name) VALUES (?, ?, ?, ?)');
        $s->execute([$username, $email, $passwordHash, $fullName]);
        return (int)$this->db->lastInsertId();
    }

    public function updateProfile(int $id, string $fullName, ?string $bio, ?string $image = null, bool $clearImage = false): void
    {
        if ($clearImage) {
            $s = $this->db->prepare('UPDATE users SET full_name = ?, bio = ?, profile_image = NULL WHERE id = ?');
            $s->execute([$fullName, $bio, $id]);
        } elseif ($image !== null) {
            $s = $this->db->prepare('UPDATE users SET full_name = ?, bio = ?, profile_image = ? WHERE id = ?');
            $s->execute([$fullName, $bio, $image, $id]);
        } else {
            $s = $this->db->prepare('UPDATE users SET full_name = ?, bio = ? WHERE id = ?');
            $s->execute([$fullName, $bio, $id]);
        }
    }

    public function search(string $q, int $limit = 20): array
    {
        $like = '%' . $q . '%';
        $s = $this->db->prepare(
            'SELECT id, username, full_name, bio, profile_image FROM users
             WHERE full_name LIKE :q1 OR username LIKE :q2
             ORDER BY (username = :exact) DESC, full_name ASC LIMIT :lim'
        );
        $s->bindValue(':q1', $like);
        $s->bindValue(':q2', $like);
        $s->bindValue(':exact', $q);
        $s->bindValue(':lim', $limit, PDO::PARAM_INT);
        $s->execute();
        return $s->fetchAll();
    }

    public function suggested(int $excludeId, int $limit = 4): array
    {
        $s = $this->db->prepare(
            'SELECT id, username, full_name, bio, profile_image FROM users WHERE id <> :id ORDER BY created_at DESC LIMIT :lim'
        );
        $s->bindValue(':id', $excludeId, PDO::PARAM_INT);
        $s->bindValue(':lim', $limit, PDO::PARAM_INT);
        $s->execute();
        return $s->fetchAll();
    }
}