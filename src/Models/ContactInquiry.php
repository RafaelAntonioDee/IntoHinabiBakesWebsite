<?php

namespace App\Models;

use App\Core\Model;

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
}
