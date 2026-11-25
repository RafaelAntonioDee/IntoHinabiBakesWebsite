<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class Order extends Model {
    /**
     * Get all orders with customer details, address, status, and payment
     */
    public function getAllOrders(?int $statusId = null, int $limit = 100): array {
        $sql = "SELECT o.*, 
                       u.full_name AS customer_name, 
                       u.email AS customer_email,
                       os.status AS status_name,
                       ad.street_address, ad.barangay, ad.city, ad.phone_number
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN order_status os ON o.status = os.id
                LEFT JOIN address_details ad ON o.address_id = ad.id
                WHERE 1=1";
        $params = [];

        if ($statusId !== null && $statusId > 0) {
            $sql .= " AND o.status = :status";
            $params[':status'] = $statusId;
        }

        $sql .= " ORDER BY o.id DESC LIMIT " . (int)$limit;

        try {
            $stmt = $this->getDb()->prepare($sql);
            $stmt->execute($params);
            $orders = $stmt->fetchAll() ?: [];

            // Attach items to each order
            foreach ($orders as &$order) {
                $order['items'] = $this->getOrderItems($order['id']);
            }

            return $orders;
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get items for a specific order
     */
    public function getOrderItems(int $orderId): array {
        $sql = "SELECT oi.*, p.name AS product_name, p.price, p.image_url, pc.category_name
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                LEFT JOIN product_categories pc ON p.category_id = pc.id
                WHERE oi.order_id = :order_id";
        try {
            $stmt = $this->getDb()->prepare($sql);
            $stmt->execute([':order_id' => $orderId]);
            return $stmt->fetchAll() ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get single order by ID
     */
    public function findById(int $orderId): ?array {
        $sql = "SELECT o.*, 
                       u.full_name AS customer_name, 
                       u.email AS customer_email,
                       os.status AS status_name,
                       ad.street_address, ad.barangay, ad.city, ad.phone_number, ad.recipient_name
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN order_status os ON o.status = os.id
                LEFT JOIN address_details ad ON o.address_id = ad.id
                WHERE o.id = :id
                LIMIT 1";
        try {
            $stmt = $this->getDb()->prepare($sql);
            $stmt->execute([':id' => $orderId]);
            $order = $stmt->fetch();
            if ($order) {
                $order['items'] = $this->getOrderItems($orderId);
                return $order;
            }
            return null;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Update order status (1: Pending, 2: Baking, 3: Ready for Delivery, 4: Completed)
     */
    public function updateStatus(int $orderId, int $statusId): bool {
        $sql = "UPDATE orders SET status = :status WHERE id = :id";
        try {
            $stmt = $this->getDb()->prepare($sql);
            return $stmt->execute([
                ':status' => $statusId,
                ':id'     => $orderId,
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Kitchen Queue Summary: Total product quantities needed for Pending (1) & Baking (2) orders
     */
    public function getKitchenBakingSummary(): array {
        $sql = "SELECT p.id AS product_id, 
                       p.name AS product_name, 
                       pc.category_name,
                       COALESCE(SUM(oi.quantity), 0) AS total_needed,
                       COUNT(DISTINCT o.id) AS order_count
                FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                JOIN products p ON oi.product_id = p.id
                LEFT JOIN product_categories pc ON p.category_id = pc.id
                WHERE o.status IN (1, 2)
                GROUP BY p.id, p.name, pc.category_name
                ORDER BY total_needed DESC";
        try {
            $stmt = $this->getDb()->query($sql);
            return $stmt->fetchAll() ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Overall order statistics for Baker & Admin Dashboards
     */
    public function getOrderStats(): array {
        $sql = "SELECT 
                    COUNT(*) AS total_orders,
                    COALESCE(SUM(total_amount), 0) AS total_revenue,
                    COUNT(CASE WHEN status = 1 THEN 1 END) AS pending_count,
                    COUNT(CASE WHEN status = 2 THEN 1 END) AS baking_count,
                    COUNT(CASE WHEN status = 3 THEN 1 END) AS ready_count,
                    COUNT(CASE WHEN status = 4 THEN 1 END) AS completed_count
                FROM orders";
        try {
            $stmt = $this->getDb()->query($sql);
            return $stmt->fetch() ?: [
                'total_orders' => 0,
                'total_revenue' => 0,
                'pending_count' => 0,
                'baking_count' => 0,
                'ready_count' => 0,
                'completed_count' => 0,
            ];
        } catch (PDOException $e) {
            return [
                'total_orders' => 0,
                'total_revenue' => 0,
                'pending_count' => 0,
                'baking_count' => 0,
                'ready_count' => 0,
                'completed_count' => 0,
            ];
        }
    }

    /**
     * Get all possible order statuses
     */
    public function getAllStatuses(): array {
        try {
            $stmt = $this->getDb()->query("SELECT id, status FROM order_status ORDER BY id ASC");
            return $stmt->fetchAll() ?: [
                ['id' => 1, 'status' => 'Pending'],
                ['id' => 2, 'status' => 'Baking'],
                ['id' => 3, 'status' => 'Ready for Delivery'],
                ['id' => 4, 'status' => 'Completed'],
            ];
        } catch (PDOException $e) {
            return [
                ['id' => 1, 'status' => 'Pending'],
                ['id' => 2, 'status' => 'Baking'],
                ['id' => 3, 'status' => 'Ready for Delivery'],
                ['id' => 4, 'status' => 'Completed'],
            ];
        }
    }
}
