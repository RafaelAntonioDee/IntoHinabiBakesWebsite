<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Address extends Model {
    private const TABLE = 'user_address_details';
    public const MAX_ADDRESSES_PER_USER = 3;

    /**
     * Get all saved addresses for a user (default first, then oldest to newest).
     */
    public function getAllByUserId(int|string $userId): array {
        try {
            $sql = "SELECT id, user_id, recipient_name, street_address, barangay, city, postal_code, delivery_notes, latitude, longitude, is_default
                    FROM " . self::TABLE . "
                    WHERE user_id = :user_id
                    ORDER BY is_default DESC, id ASC";
            $stmt = $this->getDb()->prepare($sql);
            $stmt->execute([':user_id' => (int)$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Exception $e) {
            error_log("Error fetching addresses for user {$userId}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get primary/default address by User ID.
     */
    public function getByUserId(int|string $userId): ?array {
        try {
            $sql = "SELECT id, user_id, recipient_name, street_address, barangay, city, postal_code, delivery_notes, latitude, longitude, is_default
                    FROM " . self::TABLE . "
                    WHERE user_id = :user_id
                    ORDER BY is_default DESC, id ASC
                    LIMIT 1";
            $stmt = $this->getDb()->prepare($sql);
            $stmt->execute([':user_id' => (int)$userId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (\Exception $e) {
            error_log("Error fetching address for user {$userId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Find a specific address owned by the given user.
     */
    public function findByIdForUser(int|string $addressId, int|string $userId): ?array {
        try {
            $sql = "SELECT id, user_id, recipient_name, street_address, barangay, city, postal_code, delivery_notes, latitude, longitude, is_default
                    FROM " . self::TABLE . "
                    WHERE id = :id AND user_id = :user_id
                    LIMIT 1";
            $stmt = $this->getDb()->prepare($sql);
            $stmt->execute([
                ':id'      => (int)$addressId,
                ':user_id' => (int)$userId,
            ]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (\Exception $e) {
            error_log("Error finding address {$addressId} for user {$userId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Count how many addresses a user currently has.
     */
    public function countByUserId(int|string $userId): int {
        try {
            $sql = "SELECT COUNT(*) FROM " . self::TABLE . " WHERE user_id = :user_id";
            $stmt = $this->getDb()->prepare($sql);
            $stmt->execute([':user_id' => (int)$userId]);
            return (int)$stmt->fetchColumn();
        } catch (\Exception $e) {
            error_log("Error counting addresses for user {$userId}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Create a new delivery address for a user (enforces max 3 addresses).
     *
     * @return array{success: bool, address?: array, error?: string}
     */
    public function createForUser(int|string $userId, array $data): array {
        $db = $this->getDb();
        $userId = (int)$userId;

        $count = $this->countByUserId($userId);
        if ($count >= self::MAX_ADDRESSES_PER_USER) {
            return [
                'success' => false,
                'error'   => 'You can only save up to ' . self::MAX_ADDRESSES_PER_USER . ' delivery addresses.',
            ];
        }

        $normalized = $this->normalizeAddressData($db, $userId, $data);
        $isDefault = ($count === 0) || !empty($data['is_default']);

        try {
            $db->beginTransaction();

            if ($isDefault && $count > 0) {
                $resetStmt = $db->prepare("UPDATE " . self::TABLE . " SET is_default = false WHERE user_id = :user_id");
                $resetStmt->execute([':user_id' => $userId]);
            }

            $sql = "INSERT INTO " . self::TABLE . "
                        (user_id, recipient_name, street_address, barangay, city, postal_code, delivery_notes, latitude, longitude, is_default)
                    VALUES
                        (:user_id, :recipient_name, :street_address, :barangay, :city, :postal_code, :delivery_notes, :latitude, :longitude, :is_default)";

            $isPgsql = $db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql';
            if ($isPgsql) {
                $sql .= " RETURNING id, user_id, recipient_name, street_address, barangay, city, postal_code, delivery_notes, latitude, longitude, is_default";
            }

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':recipient_name', $normalized['recipient_name']);
            $stmt->bindValue(':street_address', $normalized['street_address']);
            $stmt->bindValue(':barangay', $normalized['barangay']);
            $stmt->bindValue(':city', $normalized['city']);
            $stmt->bindValue(':postal_code', $normalized['postal_code']);
            $stmt->bindValue(':delivery_notes', $normalized['delivery_notes']);
            $stmt->bindValue(':latitude', $normalized['latitude'], $normalized['latitude'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':longitude', $normalized['longitude'], $normalized['longitude'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':is_default', $isDefault, PDO::PARAM_BOOL);
            $stmt->execute();

            if ($isPgsql) {
                $created = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $newId = (int)$db->lastInsertId();
                $created = $this->findByIdForUser($newId, $userId);
            }

            $db->commit();

            return [
                'success' => true,
                'address' => $created ?: [],
            ];
        } catch (\Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("Error creating address for user {$userId}: " . $e->getMessage());
            return [
                'success' => false,
                'error'   => 'Failed to save address to database.',
            ];
        }
    }

    /**
     * Update an existing delivery address owned by a user.
     *
     * @return array{success: bool, address?: array, error?: string}
     */
    public function updateForUser(int|string $addressId, int|string $userId, array $data): array {
        $db = $this->getDb();
        $userId = (int)$userId;
        $addressId = (int)$addressId;

        $existing = $this->findByIdForUser($addressId, $userId);
        if (!$existing) {
            return [
                'success' => false,
                'error'   => 'Address not found.',
            ];
        }

        $normalized = $this->normalizeAddressData($db, $userId, $data);
        $makeDefault = array_key_exists('is_default', $data) ? !empty($data['is_default']) : (bool)$existing['is_default'];

        try {
            $db->beginTransaction();

            if ($makeDefault) {
                $resetStmt = $db->prepare("UPDATE " . self::TABLE . " SET is_default = false WHERE user_id = :user_id AND id != :id");
                $resetStmt->execute([':user_id' => $userId, ':id' => $addressId]);
            }

            $sql = "UPDATE " . self::TABLE . "
                    SET recipient_name = :recipient_name,
                        street_address = :street_address,
                        barangay       = :barangay,
                        city           = :city,
                        postal_code    = :postal_code,
                        delivery_notes = :delivery_notes,
                        latitude       = :latitude,
                        longitude      = :longitude,
                        is_default     = :is_default
                    WHERE id = :id AND user_id = :user_id";

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':recipient_name', $normalized['recipient_name']);
            $stmt->bindValue(':street_address', $normalized['street_address']);
            $stmt->bindValue(':barangay', $normalized['barangay']);
            $stmt->bindValue(':city', $normalized['city']);
            $stmt->bindValue(':postal_code', $normalized['postal_code']);
            $stmt->bindValue(':delivery_notes', $normalized['delivery_notes']);
            $stmt->bindValue(':latitude', $normalized['latitude'], $normalized['latitude'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':longitude', $normalized['longitude'], $normalized['longitude'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':is_default', $makeDefault, PDO::PARAM_BOOL);
            $stmt->bindValue(':id', $addressId, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            $db->commit();

            return [
                'success' => true,
                'address' => $this->findByIdForUser($addressId, $userId) ?: [],
            ];
        } catch (\Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("Error updating address {$addressId} for user {$userId}: " . $e->getMessage());
            return [
                'success' => false,
                'error'   => 'Failed to update address.',
            ];
        }
    }

    /**
     * Set a specific address as the user's default address.
     */
    public function setDefaultForUser(int|string $addressId, int|string $userId): bool {
        $db = $this->getDb();
        $userId = (int)$userId;
        $addressId = (int)$addressId;

        if (!$this->findByIdForUser($addressId, $userId)) {
            return false;
        }

        try {
            $db->beginTransaction();
            $resetStmt = $db->prepare("UPDATE " . self::TABLE . " SET is_default = false WHERE user_id = :user_id");
            $resetStmt->execute([':user_id' => $userId]);

            $setStmt = $db->prepare("UPDATE " . self::TABLE . " SET is_default = true WHERE id = :id AND user_id = :user_id");
            $setStmt->execute([':id' => $addressId, ':user_id' => $userId]);
            $db->commit();
            return true;
        } catch (\Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("Error setting default address {$addressId} for user {$userId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a user's saved address and promote another to default if needed.
     */
    public function deleteForUser(int|string $addressId, int|string $userId): bool {
        $db = $this->getDb();
        $userId = (int)$userId;
        $addressId = (int)$addressId;

        $existing = $this->findByIdForUser($addressId, $userId);
        if (!$existing) {
            return false;
        }

        try {
            $db->beginTransaction();

            $stmt = $db->prepare("DELETE FROM " . self::TABLE . " WHERE id = :id AND user_id = :user_id");
            $stmt->execute([':id' => $addressId, ':user_id' => $userId]);

            if (!empty($existing['is_default'])) {
                $next = $this->getByUserId($userId);
                if ($next) {
                    $promote = $db->prepare("UPDATE " . self::TABLE . " SET is_default = true WHERE id = :id AND user_id = :user_id");
                    $promote->execute([':id' => (int)$next['id'], ':user_id' => $userId]);
                }
            }

            $db->commit();
            return true;
        } catch (\Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("Error deleting address {$addressId} for user {$userId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Backward-compatible save helper: updates specified ID or creates a new address.
     */
    public function saveForUser(int|string $userId, array $data): bool {
        if (!empty($data['id'])) {
            $res = $this->updateForUser((int)$data['id'], $userId, $data);
            return $res['success'];
        }
        $res = $this->createForUser($userId, $data);
        return $res['success'];
    }

    /**
     * Normalize incoming address payload to match user_address_details columns.
     */
    private function normalizeAddressData(PDO $db, int $userId, array $data): array {
        $recipientName = trim((string)($data['recipient_name'] ?? ''));
        if ($recipientName === '') {
            $userStmt = $db->prepare("SELECT full_name FROM users WHERE id = :id LIMIT 1");
            $userStmt->execute([':id' => $userId]);
            $user = $userStmt->fetch(PDO::FETCH_ASSOC);
            $recipientName = $user['full_name'] ?? 'Customer';
        }

        $lat = $data['lat'] ?? $data['latitude'] ?? null;
        $lng = $data['lng'] ?? $data['longitude'] ?? null;

        return [
            'recipient_name' => $recipientName,
            'street_address' => trim((string)($data['street'] ?? $data['street_address'] ?? '')),
            'barangay'       => trim((string)($data['barangay'] ?? '')),
            'city'           => trim((string)($data['city'] ?? '')),
            'postal_code'    => trim((string)($data['postal_code'] ?? '')),
            'delivery_notes' => trim((string)($data['notes'] ?? $data['delivery_notes'] ?? '')),
            'latitude'       => is_numeric($lat) ? (string)(float)$lat : null,
            'longitude'      => is_numeric($lng) ? (string)(float)$lng : null,
        ];
    }
}
