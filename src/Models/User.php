<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model {
    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?array {
        $stmt = $this->getDb()->prepare("SELECT id, full_name, email, password, created_at FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Find user by ID
     */
    public function findById(int|string $id): ?array {
        $stmt = $this->getDb()->prepare("SELECT id, full_name, email, created_at FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Create a new user
     */
    public function create(string $fullName, string $email, string $passwordHash): bool {
        $stmt = $this->getDb()->prepare("INSERT INTO users (full_name, email, password) VALUES (:name, :email, :password)");
        return $stmt->execute([
            ':name'     => $fullName,
            ':email'    => $email,
            ':password' => $passwordHash,
        ]);
    }
}
