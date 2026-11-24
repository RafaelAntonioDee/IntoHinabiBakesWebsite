<?php

namespace App\Models;

use App\Core\Model;
use PDOException;

class ContactInquiry extends Model {
    /**
     * Store a new contact message
     */
    public function create(string $name, string $email, string $message): bool {
        $stmt = $this->getDb()->prepare("INSERT INTO contact_inquiries (name, email, message) VALUES (:name, :email, :message)");
        return $stmt->execute([
            ':name'    => $name,
            ':email'   => $email,
            ':message' => $message,
        ]);
    }

    /**
     * Get all contact inquiries
     */
    public function getAll(int $limit = 50): array {
        try {
            $stmt = $this->getDb()->query("SELECT * FROM contact_inquiries ORDER BY id DESC LIMIT " . (int)$limit);
            return $stmt->fetchAll() ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Count total inquiries
     */
    public function count(): int {
        try {
            $stmt = $this->getDb()->query("SELECT COUNT(*) FROM contact_inquiries");
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
}
