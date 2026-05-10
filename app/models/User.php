<?php

namespace App\Models;

class User extends BaseModel
{
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function updatePasswordHash(int $userId, string $hashedPassword): void
    {
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $userId]);
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO users (fullname, username, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data['fullname'],
            $data['username'],
            $data['password'],
            $data['role'] ?? 'student'
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function all(): array
    {
        $stmt = $this->db->query("SELECT * FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET fullname = ?, username = ?, role = ? WHERE id = ?");
        return $stmt->execute([
            $data['fullname'],
            $data['username'],
            $data['role'],
            $id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
