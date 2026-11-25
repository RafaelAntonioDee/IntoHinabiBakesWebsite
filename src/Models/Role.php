<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Role extends Model {
    /**
     * Get all roles
     */
    public function getAll(): array {
        $stmt = $this->getDb()->query("SELECT id, role_name FROM roles ORDER BY id ASC");
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Get role by ID
     */
    public function findById(int $id): ?array {
        $stmt = $this->getDb()->prepare("SELECT id, role_name FROM roles WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $role = $stmt->fetch();
        return $role ?: null;
    }
}
