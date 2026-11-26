<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Address extends Model {
    /**
     * Get primary/default address by User ID
     */
    public function getByUserId(int|string $userId): ?array {
        try {
            $sql = "SELECT * FROM address_details WHERE user_id = :user_id ORDER BY is_default DESC, id DESC LIMIT 1";
            $stmt = $this->getDb()->prepare($sql);
            $stmt->execute([':user_id' => (int)$userId]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (\Exception $e) {
            error_log("Error fetching address for user {$userId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Save or update delivery address for user
     */
    public function saveForUser(int|string $userId, array $data): bool {
        $db = $this->getDb();
        $userId = (int)$userId;

        // Check if user exists
        $userStmt = $db->prepare("SELECT full_name FROM users WHERE id = :id LIMIT 1");
        $userStmt->execute([':id' => $userId]);
        $user = $userStmt->fetch();
        $defaultRecipientName = $user['full_name'] ?? 'Customer';

        // Check if coordinates columns exist
        $hasCoords = false;
        try {
            $db->query("SELECT latitude, longitude FROM address_details LIMIT 1");
            $hasCoords = true;
        } catch (\Exception $e) {
            try {
                $db->exec("ALTER TABLE address_details ADD COLUMN latitude NUMERIC(10, 8) NULL, ADD COLUMN longitude NUMERIC(11, 8) NULL");
                $hasCoords = true;
            } catch (\Exception $ex) {
                $hasCoords = false;
            }
        }

        $existing = $this->getByUserId($userId);

        $street = trim($data['street'] ?? $data['street_address'] ?? '');
        $barangay = trim($data['barangay'] ?? '');
        $city = trim($data['city'] ?? '');
        $notes = trim($data['notes'] ?? $data['delivery_notes'] ?? '');
        $recipientName = trim($data['recipient_name'] ?? '') ?: $defaultRecipientName;
        $phoneNumber = trim($data['phone_number'] ?? '');
        $postalCode = trim($data['postal_code'] ?? '');
        $lat = isset($data['lat']) && is_numeric($data['lat']) ? (float)$data['lat'] : null;
        $lng = isset($data['lng']) && is_numeric($data['lng']) ? (float)$data['lng'] : null;

        try {
            if ($existing) {
                if ($hasCoords) {
                    $sql = "UPDATE address_details 
                            SET street_address = :street, 
                                barangay = :barangay, 
                                city = :city, 
                                delivery_notes = :notes,
                                latitude = :lat,
                                longitude = :lng,
                                is_default = true
                            WHERE id = :id AND user_id = :user_id";
                    $stmt = $db->prepare($sql);
                    return $stmt->execute([
                        ':street'   => $street,
                        ':barangay' => $barangay,
                        ':city'     => $city,
                        ':notes'    => $notes,
                        ':lat'      => $lat,
                        ':lng'      => $lng,
                        ':id'       => $existing['id'],
                        ':user_id'  => $userId,
                    ]);
                } else {
                    $sql = "UPDATE address_details 
                            SET street_address = :street, 
                                barangay = :barangay, 
                                city = :city, 
                                delivery_notes = :notes,
                                is_default = true
                            WHERE id = :id AND user_id = :user_id";
                    $stmt = $db->prepare($sql);
                    return $stmt->execute([
                        ':street'   => $street,
                        ':barangay' => $barangay,
                        ':city'     => $city,
                        ':notes'    => $notes,
                        ':id'       => $existing['id'],
                        ':user_id'  => $userId,
                    ]);
                }
            } else {
                if ($hasCoords) {
                    $sql = "INSERT INTO address_details (user_id, recipient_name, phone_number, street_address, barangay, city, postal_code, delivery_notes, latitude, longitude, is_default)
                            VALUES (:user_id, :recipient_name, :phone_number, :street, :barangay, :city, :postal_code, :notes, :lat, :lng, true)";
                    $stmt = $db->prepare($sql);
                    return $stmt->execute([
                        ':user_id'        => $userId,
                        ':recipient_name' => $recipientName,
                        ':phone_number'   => $phoneNumber,
                        ':street'         => $street,
                        ':barangay'       => $barangay,
                        ':city'           => $city,
                        ':postal_code'    => $postalCode,
                        ':notes'          => $notes,
                        ':lat'            => $lat,
                        ':lng'            => $lng,
                    ]);
                } else {
                    $sql = "INSERT INTO address_details (user_id, recipient_name, phone_number, street_address, barangay, city, postal_code, delivery_notes, is_default)
                            VALUES (:user_id, :recipient_name, :phone_number, :street, :barangay, :city, :postal_code, :notes, true)";
                    $stmt = $db->prepare($sql);
                    return $stmt->execute([
                        ':user_id'        => $userId,
                        ':recipient_name' => $recipientName,
                        ':phone_number'   => $phoneNumber,
                        ':street'         => $street,
                        ':barangay'       => $barangay,
                        ':city'           => $city,
                        ':postal_code'    => $postalCode,
                        ':notes'          => $notes,
                    ]);
                }
            }
        } catch (\Exception $e) {
            error_log("Error saving address for user {$userId}: " . $e->getMessage());
            return false;
        }
    }
}
