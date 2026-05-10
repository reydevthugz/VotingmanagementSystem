<?php
namespace App\Models;

class User extends BaseModel
{
    public function findByEmail(string $email): ?array
    {
        $sql = 'SELECT user_id, fullname, email, password, role, status FROM users WHERE email = :email LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function updatePasswordHash(int $userId, string $hashedPassword): void
    {
        $sql = 'UPDATE users SET password = :password, updated_at = NOW() WHERE user_id = :user_id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'password' => $hashedPassword,
            'user_id' => $userId,
        ]);
    }
}
