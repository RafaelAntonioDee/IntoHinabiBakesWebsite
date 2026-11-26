<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model {
    /**
     * Find user by email (including role info)
     */
    public function findByEmail(string $email): ?array {
        $sql = "SELECT u.id, u.full_name, u.email, u.phone_number, u.password, 
                       COALESCE(u.role, 1) AS role_id, 
                       COALESCE(r.role_name, 'Customer') AS role_name, 
                       u.created_at 
                FROM users u 
                LEFT JOIN roles r ON u.role = r.id 
                WHERE LOWER(u.email) = LOWER(:email) 
                LIMIT 1";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute([':email' => trim($email)]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Find user by ID (including role info)
     */
    public function findById(int|string $id): ?array {
        $sql = "SELECT u.id, u.full_name, u.email, u.phone_number, 
                       COALESCE(u.role, 1) AS role_id, 
                       COALESCE(r.role_name, 'Customer') AS role_name, 
                       u.created_at 
                FROM users u 
                LEFT JOIN roles r ON u.role = r.id 
                WHERE u.id = :id 
                LIMIT 1";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute([':id' => (int)$id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Create a new user with default Customer role (1)
     */
    public function create(string $fullName, string $email, string $passwordHash, int $roleId = 1, ?string $phoneNumber = null): bool {
        $stmt = $this->getDb()->prepare("INSERT INTO users (full_name, email, phone_number, password, role) VALUES (:name, :email, :phone_number, :password, :role)");
        return $stmt->execute([
            ':name'         => $fullName,
            ':email'        => $email,
            ':phone_number' => $phoneNumber,
            ':password'     => $passwordHash,
            ':role'         => $roleId,
        ]);
    }

    /**
     * Validate a Philippine mobile (09XXXXXXXXX / +639XXXXXXXXX) or landline number
     */
    public static function isValidPhoneNumber(string $phone): bool {
        $phone = trim($phone);
        if ($phone === '' || !preg_match('/^[0-9+\-\s()]+$/', $phone)) {
            return false;
        }
        $clean = preg_replace('/[\s\-()]/', '', $phone);
        $isMobile09 = (bool)preg_match('/^09\d{9}$/', $clean);
        $isMobile63 = (bool)preg_match('/^\+?639\d{9}$/', $clean);
        $isLandline = (bool)preg_match('/^0[2-8]\d{7,8}$/', $clean);

        if (!$isMobile09 && !$isMobile63 && !$isLandline) {
            return false;
        }

        $digitsOnly = preg_replace('/\D/', '', $clean);
        if (preg_match('/^(\d)\1+$/', substr($digitsOnly, -9))) {
            return false;
        }
        return true;
    }

    /**
     * Update a user's full name
     */
    public function updateFullName(int $userId, string $fullName): bool {
        $stmt = $this->getDb()->prepare("UPDATE users SET full_name = :full_name WHERE id = :id");
        return $stmt->execute([
            ':full_name' => trim($fullName),
            ':id'        => $userId,
        ]);
    }

    /**
     * Update a user's phone number
     */
    public function updatePhoneNumber(int $userId, string $phoneNumber): bool {
        $stmt = $this->getDb()->prepare("UPDATE users SET phone_number = :phone_number WHERE id = :id");
        return $stmt->execute([
            ':phone_number' => trim($phoneNumber),
            ':id'           => $userId,
        ]);
    }

    /**
     * Update a user's email address
     */
    public function updateEmail(int $userId, string $email): bool {
        $stmt = $this->getDb()->prepare("UPDATE users SET email = :email WHERE id = :id");
        return $stmt->execute([
            ':email' => trim($email),
            ':id'    => $userId,
        ]);
    }

    /**
     * Get all users for admin management with optional search & role filtering
     */
    public function getAllUsers(?string $search = null, ?int $roleFilter = null): array {
        $sql = "SELECT u.id, u.full_name, u.email, u.phone_number, 
                       COALESCE(u.role, 1) AS role_id, 
                       COALESCE(r.role_name, 'Customer') AS role_name, 
                       u.created_at 
                FROM users u 
                LEFT JOIN roles r ON u.role = r.id 
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (LOWER(u.full_name) LIKE :search OR LOWER(u.email) LIKE :search)";
            $params[':search'] = '%' . strtolower(trim($search)) . '%';
        }

        if (!empty($roleFilter)) {
            $sql .= " AND u.role = :role";
            $params[':role'] = (int)$roleFilter;
        }

        $sql .= " ORDER BY u.id DESC";

        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Update user role
     */
    public function updateRole(int $userId, int $newRoleId): bool {
        $stmt = $this->getDb()->prepare("UPDATE users SET role = :role WHERE id = :id");
        return $stmt->execute([
            ':role' => $newRoleId,
            ':id'   => $userId,
        ]);
    }

    /**
     * Delete user by ID
     */
    public function delete(int $userId): bool {
        $stmt = $this->getDb()->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $userId]);
    }

    /**
     * Get user count breakdown by role
     */
    public function getUserStats(): array {
        $sql = "SELECT 
                    COUNT(*) AS total_users,
                    COUNT(CASE WHEN role = 1 OR role IS NULL THEN 1 END) AS total_customers,
                    COUNT(CASE WHEN role = 2 THEN 1 END) AS total_bakers,
                    COUNT(CASE WHEN role = 3 THEN 1 END) AS total_admins
                FROM users";
        $stmt = $this->getDb()->query($sql);
        return $stmt->fetch() ?: [
            'total_users' => 0,
            'total_customers' => 0,
            'total_bakers' => 0,
            'total_admins' => 0,
        ];
    }
}
