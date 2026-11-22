<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Address extends Model {
    /**
     * Find all addresses belonging to a user
     */
    public function findByUser(int|string $userId): array {
        $stmt = $this->getDb()->prepare(
            "SELECT * FROM addresses WHERE user_id = :user_id ORDER BY is_default DESC, created_at DESC"
        );
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Find a single address by ID
     */
    public function findById(int|string $id): ?array {
        $stmt = $this->getDb()->prepare("SELECT * FROM addresses WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Find a single address by ID ensuring it belongs to the given user
     */
    public function findByIdAndUser(int|string $id, int|string $userId): ?array {
        $stmt = $this->getDb()->prepare(
            "SELECT * FROM addresses WHERE id = :id AND user_id = :user_id LIMIT 1"
        );
        $stmt->execute([':id' => $id, ':user_id' => $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Get the user's default address (or most recent)
     */
    public function getDefault(int|string $userId): ?array {
        $stmt = $this->getDb()->prepare(
            "SELECT * FROM addresses WHERE user_id = :user_id ORDER BY is_default DESC, created_at DESC LIMIT 1"
        );
        $stmt->execute([':user_id' => $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Create a new address
     */
    public function create(array $data): int|string|false {
        $db = $this->getDb();

        // If this is marked as default, unset any existing default first
        if (!empty($data['is_default'])) {
            $this->clearDefaults($data['user_id']);
        }

        $stmt = $db->prepare(
            "INSERT INTO addresses (user_id, label, full_name, phone, street_address, barangay, city, province, zip_code, is_default)
             VALUES (:user_id, :label, :full_name, :phone, :street_address, :barangay, :city, :province, :zip_code, :is_default)"
        );

        $success = $stmt->execute([
            ':user_id'        => $data['user_id'],
            ':label'          => $data['label'] ?? 'Home',
            ':full_name'      => $data['full_name'],
            ':phone'          => $data['phone'],
            ':street_address' => $data['street_address'],
            ':barangay'       => $data['barangay'] ?? null,
            ':city'           => $data['city'],
            ':province'       => $data['province'],
            ':zip_code'       => $data['zip_code'],
            ':is_default'     => !empty($data['is_default']) ? 1 : 0,
        ]);

        return $success ? $db->lastInsertId() : false;
    }

    /**
     * Update an existing address
     */
    public function update(int|string $id, array $data): bool {
        if (!empty($data['is_default']) && isset($data['user_id'])) {
            $this->clearDefaults($data['user_id']);
        }

        $stmt = $this->getDb()->prepare(
            "UPDATE addresses
             SET label = :label,
                 full_name = :full_name,
                 phone = :phone,
                 street_address = :street_address,
                 barangay = :barangay,
                 city = :city,
                 province = :province,
                 zip_code = :zip_code,
                 is_default = :is_default
             WHERE id = :id"
        );

        return $stmt->execute([
            ':id'             => $id,
            ':label'          => $data['label'] ?? 'Home',
            ':full_name'      => $data['full_name'],
            ':phone'          => $data['phone'],
            ':street_address' => $data['street_address'],
            ':barangay'       => $data['barangay'] ?? null,
            ':city'           => $data['city'],
            ':province'       => $data['province'],
            ':zip_code'       => $data['zip_code'],
            ':is_default'     => !empty($data['is_default']) ? 1 : 0,
        ]);
    }

    /**
     * Delete an address
     */
    public function delete(int|string $id): bool {
        $stmt = $this->getDb()->prepare("DELETE FROM addresses WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Clear default flag on all addresses for a user
     */
    private function clearDefaults(int|string $userId): void {
        $stmt = $this->getDb()->prepare("UPDATE addresses SET is_default = 0 WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
    }
}
