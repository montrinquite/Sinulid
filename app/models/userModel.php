<?php
require_once __DIR__ . '/Model.php';

class userModel extends Model {
    public function create(string $username, string $email, string $passwordHash): int {
        $stmt = $this -> db() -> prepare(
            'INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :hash)'
        );
        $stmt -> execute(['username' => $username, 'email' => $email, 'hash' => $passwordHash]);
        return (int) $this -> db() -> lastInsertId(); 
    }

    public function findById(int $id): ?array {
        $stmt = $this -> db() -> prepare(
            'SELECT id, email, created_at FROM users WHERE id = :id'
            );
        $stmt -> execute([':id' => $id]);
        return $stmt -> fetch() ?: null;
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this -> db() -> prepare(
            'SELECT * FROM users WHERE email = :email'
            );
        $stmt -> execute([':email' => $email]);
        return $stmt -> fetch() ?: null;
    }

    public function findByUsername(string $username): ?array {
        $stmt = $this -> db() -> prepare(
            'SELECT * FROM users WHERE username = :username'
        );
        $stmt -> execute([':username'=> $username]);
        return $stmt -> fetch() ?: null;
    }

    public function emailExists(string $email): bool {
        $stmt = $this -> db() -> prepare(
            'SELECT 1 FROM users WHERE email = :email LIMIT 1'
        );
        $stmt -> execute([':email' => $email]);
        return (bool) $stmt -> fetchColumn();
    }
    public function usernameExists(string $username): bool {
        $stmt = $this -> db() -> prepare(
            'SELECT 1 FROM users WHERE username = :username LIMIT 1'
        );
        $stmt -> execute([':username' => $username]);
        return (bool) $stmt -> fetchColumn();
    }

    public function update( string $id, string $username, string $email): bool {
        $stmt = $this -> db() -> prepare(
            'UPDATE users SET username = :username, email = :email WHERE id = :id'
        );
        return $stmt -> execute([':username' => $username, ':email' => $email, ':id' => $id]);
    }

    public function updatePassword(int $id, string $passwordHash): bool {
        $stmt = $this -> db() -> prepare(
            'UPDATE users SET password_hash = :hash WHERE id = :id'
        );
        return $stmt -> execute ([':hash' => $passwordHash, ':id' => $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this -> db() -> prepare(
            'DELETE FROM users WHERE id = :id'
        );
        return $stmt -> execute([':id' => $id]);
    }
}