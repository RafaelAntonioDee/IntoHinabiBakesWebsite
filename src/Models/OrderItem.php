<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class OrderItem extends Model {
    /**
     * Add an item to an order
     */
    public function create(array $data): int|string|false {
        $db = $this->getDb();

        $stmt = $db->prepare(
            "INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price, subtotal)
             VALUES (:order_id, :product_id, :product_name, :quantity, :unit_price, :subtotal)"
        );

        $success = $stmt->execute([
            ':order_id'     => $data['order_id'],
            ':product_id'   => $data['product_id'],
            ':product_name' => $data['product_name'],
            ':quantity'     => $data['quantity'],
            ':unit_price'   => $data['unit_price'],
            ':subtotal'     => $data['subtotal'],
        ]);

        return $success ? $db->lastInsertId() : false;
    }

    /**
     * Bulk-insert multiple items for an order (more efficient than individual inserts)
     */
    public function createMany(int|string $orderId, array $items): bool {
        $db = $this->getDb();

        $stmt = $db->prepare(
            "INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price, subtotal)
             VALUES (:order_id, :product_id, :product_name, :quantity, :unit_price, :subtotal)"
        );

        foreach ($items as $item) {
            $success = $stmt->execute([
                ':order_id'     => $orderId,
                ':product_id'   => $item['product_id'],
                ':product_name' => $item['product_name'],
                ':quantity'     => $item['quantity'],
                ':unit_price'   => $item['unit_price'],
                ':subtotal'     => $item['unit_price'] * $item['quantity'],
            ]);

            if (!$success) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get all items for a given order
     */
    public function findByOrder(int|string $orderId): array {
        $stmt = $this->getDb()->prepare(
            "SELECT * FROM order_items WHERE order_id = :order_id ORDER BY id ASC"
        );
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll();
    }
}
