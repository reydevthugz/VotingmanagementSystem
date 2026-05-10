<?php
namespace App\Models;

class User
{
    public function findByEmail(string $email): ?array
    {
        return MockStorage::findUserByEmail($email);
    }

    public function updatePasswordHash(int $userId, string $hashedPassword): void
    {
        MockStorage::updateUserPasswordHash($userId, $hashedPassword);
    }
}
