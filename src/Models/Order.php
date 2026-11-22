<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Order extends Model {
    /**
     * Create a new order and return its ID
     */
    public function create(array $data): int|string|false {
        $db = $this->getDb();

        $stmt = $db->prepare(
            "INSERT INTO orders (user_id, address_id, subtotal, delivery_fee, total, payment_method, notes, status)
             VALUES (:user_id, :address_id, :subtotal, :delivery_fee, :total, :payment_method, :notes, :status)"
        );

        $success = $stmt->execute([
            ':user_id'        => $data['user_id'],
            ':address_id'     => $data['address_id'] ?? null,
            ':subtotal'       => $data['subtotal'],
            ':delivery_fee'   => $data['delivery_fee'] ?? 0,
            ':total'          => $data['total'],
            ':payment_method' => $data['payment_method'] ?? 'cod',
            ':notes'          => $data['notes'] ?? null,
            ':status'         => $data['status'] ?? 'pending',
        ]);

        return $success ? $db->lastInsertId() : false;
    }

    /**
     * Find an order by ID
     */
    public function findById(int|string $id): ?array {
        $stmt = $this->getDb()->prepare("SELECT * FROM orders WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Find an order by ID ensuring it belongs to the given user
     */
    public function findByIdAndUser(int|string $id, int|string $userId): ?array {
        $stmt = $this->getDb()->prepare(
            "SELECT * FROM orders WHERE id = :id AND user_id = :user_id LIMIT 1"
        );
        $stmt->execute([':id' => $id, ':user_id' => $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Get all orders for a user (most recent first)
     */
    public function findByUser(int|string $userId): array {
        $stmt = $this->getDb()->prepare(
            "SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC"
        );
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Update order status
     */
    public function updateStatus(int|string $id, string $status): bool {
        $stmt = $this->getDb()->prepare("UPDATE orders SET status = :status WHERE id = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    /**
     * Cancel an order (only if still pending)
     */
    public function cancel(int|string $id, int|string $userId): bool {
        $stmt = $this->getDb()->prepare(
            "UPDATE orders SET status = 'cancelled' WHERE id = :id AND user_id = :user_id AND status = 'pending'"
        );
        $stmt->execute([':id' => $id, ':user_id' => $userId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Get all orders (admin) with optional status filter
     */
    public function all(?string $status = null): array {
        if ($status) {
            $stmt = $this->getDb()->prepare(
                "SELECT * FROM orders WHERE status = :status ORDER BY created_at DESC"
            );
            $stmt->execute([':status' => $status]);
        } else {
            $stmt = $this->getDb()->query("SELECT * FROM orders ORDER BY created_at DESC");
        }
        return $stmt->fetchAll();
    }
}
