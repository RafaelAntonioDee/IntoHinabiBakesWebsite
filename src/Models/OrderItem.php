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
        $note = trim((string)($data['customer_note'] ?? ''));

        $sql = "INSERT INTO order_items (order_id, product_id, quantity, customer_note)
                VALUES (:order_id, :product_id, :quantity, :customer_note)";
        $isPgsql = $db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql';
        if ($isPgsql) {
            $sql .= " RETURNING id";
        }

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':order_id', (int)$data['order_id'], PDO::PARAM_INT);
        $stmt->bindValue(':product_id', (int)$data['product_id'], PDO::PARAM_INT);
        $stmt->bindValue(':quantity', max(1, (int)$data['quantity']), PDO::PARAM_INT);
        $stmt->bindValue(':customer_note', $note === '' ? null : $note, $note === '' ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $success = $stmt->execute();

        if (!$success) {
            return false;
        }
        return $isPgsql ? (int)$stmt->fetchColumn() : $db->lastInsertId();
    }

    /**
     * Bulk-insert multiple items for an order
     */
    public function createMany(int|string $orderId, array $items): bool {
        $db = $this->getDb();

        $stmt = $db->prepare(
            "INSERT INTO order_items (order_id, product_id, quantity, customer_note)
             VALUES (:order_id, :product_id, :quantity, :customer_note)"
        );

        foreach ($items as $item) {
            $note = trim((string)($item['customer_note'] ?? ''));
            $stmt->bindValue(':order_id', (int)$orderId, PDO::PARAM_INT);
            $stmt->bindValue(':product_id', (int)$item['product_id'], PDO::PARAM_INT);
            $stmt->bindValue(':quantity', max(1, (int)$item['quantity']), PDO::PARAM_INT);
            $stmt->bindValue(':customer_note', $note === '' ? null : $note, $note === '' ? PDO::PARAM_NULL : PDO::PARAM_STR);
            if (!$stmt->execute()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get all items for a given order joined with product info
     */
    public function findByOrder(int|string $orderId): array {
        $stmt = $this->getDb()->prepare(
            "SELECT oi.*, p.name AS product_name, p.price, p.image_url
             FROM order_items oi
             LEFT JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = :order_id
             ORDER BY oi.id ASC"
        );
        $stmt->execute([':order_id' => (int)$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
