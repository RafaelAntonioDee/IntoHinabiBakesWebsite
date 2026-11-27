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
                       u.phone_number,
                       os.status AS status_name,
                       ad.street_address, ad.barangay, ad.city
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN order_status os ON o.status = os.id
                LEFT JOIN user_address_details ad ON o.address_id = ad.id
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
                       u.phone_number,
                       os.status AS status_name,
                       ad.street_address, ad.barangay, ad.city, ad.recipient_name
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN order_status os ON o.status = os.id
                LEFT JOIN user_address_details ad ON o.address_id = ad.id
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

    /**
     * Transfer a user's Supabase cart into payments, orders, and order_items, then clear the cart.
     *
     * @return array{success: bool, order_id?: int, order_number?: string, error?: string}
     */
    public function createFromCart(int $userId, int $addressId, int $paymentTypeId = 1, float $deliveryFee = 0.00): array {
        $db = $this->getDb();
        $isPgsql = $db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql';

        try {
            $db->beginTransaction();

            $cartStmt = $db->prepare(
                "SELECT c.id, c.product_id, c.quantity, c.customer_note, p.price
                 FROM carts c
                 INNER JOIN products p ON c.product_id = p.id
                 WHERE c.user_id = :user_id
                 ORDER BY c.id ASC"
            );
            $cartStmt->execute([':user_id' => $userId]);
            $cartItems = $cartStmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($cartItems)) {
                $db->rollBack();
                return ['success' => false, 'error' => 'Your cart is empty.'];
            }

            $subtotal = 0.0;
            foreach ($cartItems as $item) {
                $subtotal += (float)$item['price'] * (int)$item['quantity'];
            }
            $subtotal = round($subtotal, 2);
            $totalAmount = round($subtotal + $deliveryFee, 2);
            $orderNumber = 'HB-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

            // 1. Create payment record (1: Cash on Delivery -> status 1, others -> status 3 Pending)
            $paymentStatusId = ($paymentTypeId === 1) ? 1 : 3;
            $paySql = "INSERT INTO payments (payment_type, payment_status) VALUES (:payment_type, :payment_status)";
            if ($isPgsql) {
                $paySql .= " RETURNING id";
            }
            $payStmt = $db->prepare($paySql);
            $payStmt->execute([
                ':payment_type'   => $paymentTypeId,
                ':payment_status' => $paymentStatusId,
            ]);
            $paymentId = $isPgsql ? (int)$payStmt->fetchColumn() : (int)$db->lastInsertId();

            // 2. Create order record (status 1 = Pending)
            $ordSql = "INSERT INTO orders (order_number, user_id, address_id, status, subtotal, delivery_fee, total_amount, payment_id, created_at)
                       VALUES (:order_number, :user_id, :address_id, 1, :subtotal, :delivery_fee, :total_amount, :payment_id, CURRENT_TIMESTAMP)";
            if ($isPgsql) {
                $ordSql .= " RETURNING id";
            }
            $ordStmt = $db->prepare($ordSql);
            $ordStmt->execute([
                ':order_number' => $orderNumber,
                ':user_id'      => $userId,
                ':address_id'   => $addressId,
                ':subtotal'     => $subtotal,
                ':delivery_fee' => $deliveryFee,
                ':total_amount' => $totalAmount,
                ':payment_id'   => $paymentId,
            ]);
            $orderId = $isPgsql ? (int)$ordStmt->fetchColumn() : (int)$db->lastInsertId();

            // 3. Transfer cart items to order_items
            $itemStmt = $db->prepare(
                "INSERT INTO order_items (order_id, product_id, quantity, customer_note)
                 VALUES (:order_id, :product_id, :quantity, :customer_note)"
            );
            foreach ($cartItems as $item) {
                $note = trim((string)($item['customer_note'] ?? ''));
                $itemStmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
                $itemStmt->bindValue(':product_id', (int)$item['product_id'], PDO::PARAM_INT);
                $itemStmt->bindValue(':quantity', (int)$item['quantity'], PDO::PARAM_INT);
                $itemStmt->bindValue(':customer_note', $note === '' ? null : $note, $note === '' ? PDO::PARAM_NULL : PDO::PARAM_STR);
                $itemStmt->execute();
            }

            // 4. Clear user's cart
            $clearStmt = $db->prepare("DELETE FROM carts WHERE user_id = :user_id");
            $clearStmt->execute([':user_id' => $userId]);

            $db->commit();

            return [
                'success'      => true,
                'order_id'     => $orderId,
                'order_number' => $orderNumber,
            ];
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("Error creating order from cart for user {$userId}: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to place order: ' . $e->getMessage()];
        }
    }

    /**
     * Get all orders belonging to a specific user
     */
    public function findByUser(int|string $userId): array {
        $sql = "SELECT o.*, os.status AS status_name
                FROM orders o
                LEFT JOIN order_status os ON o.status = os.id
                WHERE o.user_id = :user_id
                ORDER BY o.id DESC";
        try {
            $stmt = $this->getDb()->prepare($sql);
            $stmt->execute([':user_id' => (int)$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Find a single order owned by a specific user
     */
    public function findByIdAndUser(int|string $orderId, int|string $userId): ?array {
        $sql = "SELECT o.*, os.status AS status_name
                FROM orders o
                LEFT JOIN order_status os ON o.status = os.id
                WHERE o.id = :id AND o.user_id = :user_id
                LIMIT 1";
        try {
            $stmt = $this->getDb()->prepare($sql);
            $stmt->execute([':id' => (int)$orderId, ':user_id' => (int)$userId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }
}
