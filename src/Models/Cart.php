<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Cart extends Model {
    private const TABLE = 'carts';

    /**
     * Get all cart items for a user joined with product details.
     */
    public function getByUserId(int|string $userId): array {
        $sql = "SELECT c.id,
                       c.user_id,
                       c.product_id,
                       c.quantity,
                       c.customer_note,
                       c.sub_total,
                       c.date_created,
                       c.date_updated,
                       p.name,
                       p.price,
                       p.image_url,
                       p.is_active,
                       p.availability,
                       COALESCE(TRIM(pc.category_name), 'Cookies') AS category_name
                FROM " . self::TABLE . " c
                INNER JOIN products p ON c.product_id = p.id
                LEFT JOIN product_categories pc ON p.category_id = pc.id
                WHERE c.user_id = :user_id
                ORDER BY c.date_created ASC, c.id ASC";

        try {
            $stmt = $this->getDb()->prepare($sql);
            $stmt->execute([':user_id' => (int)$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Exception $e) {
            error_log("Error fetching cart for user {$userId}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Add a product to the user's cart (or increment quantity if same product + note exists).
     *
     * @return array{success: bool, item?: array, error?: string}
     */
    public function addItem(int|string $userId, int $productId, int $quantity = 1, ?string $customerNote = null): array {
        $db = $this->getDb();
        $userId = (int)$userId;
        $quantity = max(1, $quantity);
        $note = trim((string)($customerNote ?? ''));
        $noteParam = ($note === '') ? null : $note;

        try {
            $prodStmt = $db->prepare("SELECT id, name, price, is_active, availability FROM products WHERE id = :id LIMIT 1");
            $prodStmt->execute([':id' => $productId]);
            $product = $prodStmt->fetch(PDO::FETCH_ASSOC);

            if (!$product || empty($product['is_active'])) {
                return ['success' => false, 'error' => 'Product not found.'];
            }
            if (empty($product['availability'])) {
                return ['success' => false, 'error' => 'This item is currently sold out.'];
            }

            $unitPrice = (float)$product['price'];

            $findStmt = $db->prepare(
                "SELECT id, quantity FROM " . self::TABLE . "
                 WHERE user_id = :user_id
                   AND product_id = :product_id
                   AND COALESCE(customer_note, '') = :note
                 LIMIT 1"
            );
            $findStmt->execute([
                ':user_id'    => $userId,
                ':product_id' => $productId,
                ':note'       => $note,
            ]);
            $existing = $findStmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                $newQty = (int)$existing['quantity'] + $quantity;
                $newSubTotal = round($unitPrice * $newQty, 2);
                $updStmt = $db->prepare(
                    "UPDATE " . self::TABLE . "
                     SET quantity = :quantity,
                         sub_total = :sub_total,
                         date_updated = CURRENT_TIMESTAMP
                     WHERE id = :id AND user_id = :user_id"
                );
                $updStmt->execute([
                    ':quantity'  => $newQty,
                    ':sub_total' => $newSubTotal,
                    ':id'        => (int)$existing['id'],
                    ':user_id'   => $userId,
                ]);
                $cartId = (int)$existing['id'];
            } else {
                $subTotal = round($unitPrice * $quantity, 2);
                $sql = "INSERT INTO " . self::TABLE . "
                            (user_id, product_id, quantity, customer_note, sub_total, date_created, date_updated)
                        VALUES
                            (:user_id, :product_id, :quantity, :customer_note, :sub_total, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
                $isPgsql = $db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'pgsql';
                if ($isPgsql) {
                    $sql .= " RETURNING id";
                }
                $insStmt = $db->prepare($sql);
                $insStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
                $insStmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
                $insStmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
                $insStmt->bindValue(':customer_note', $noteParam, $noteParam === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
                $insStmt->bindValue(':sub_total', $subTotal);
                $insStmt->execute();
                $cartId = $isPgsql ? (int)$insStmt->fetchColumn() : (int)$db->lastInsertId();
            }

            return [
                'success' => true,
                'cart_id' => $cartId,
                'product' => $product,
            ];
        } catch (\Exception $e) {
            error_log("Error adding item to cart for user {$userId}: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to add item to cart.'];
        }
    }

    /**
     * Update cart item quantity (removes item if quantity <= 0).
     */
    public function updateQuantity(int|string $userId, int $cartIdOrProductId, int $quantity, bool $byCartId = true): bool {
        $db = $this->getDb();
        $userId = (int)$userId;
        $column = $byCartId ? 'c.id' : 'c.product_id';

        try {
            $stmt = $db->prepare(
                "SELECT c.id, p.price
                 FROM " . self::TABLE . " c
                 INNER JOIN products p ON c.product_id = p.id
                 WHERE {$column} = :target_id AND c.user_id = :user_id
                 LIMIT 1"
            );
            $stmt->execute([
                ':target_id' => $cartIdOrProductId,
                ':user_id'   => $userId,
            ]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                return false;
            }

            if ($quantity <= 0) {
                $del = $db->prepare("DELETE FROM " . self::TABLE . " WHERE id = :id AND user_id = :user_id");
                return $del->execute([':id' => (int)$row['id'], ':user_id' => $userId]);
            }

            $subTotal = round((float)$row['price'] * $quantity, 2);
            $upd = $db->prepare(
                "UPDATE " . self::TABLE . "
                 SET quantity = :quantity,
                     sub_total = :sub_total,
                     date_updated = CURRENT_TIMESTAMP
                 WHERE id = :id AND user_id = :user_id"
            );
            return $upd->execute([
                ':quantity'  => $quantity,
                ':sub_total' => $subTotal,
                ':id'        => (int)$row['id'],
                ':user_id'   => $userId,
            ]);
        } catch (\Exception $e) {
            error_log("Error updating cart for user {$userId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove a single item from the user's cart.
     */
    public function removeItem(int|string $userId, int $cartIdOrProductId, bool $byCartId = true): bool {
        $column = $byCartId ? 'id' : 'product_id';
        try {
            $stmt = $this->getDb()->prepare("DELETE FROM " . self::TABLE . " WHERE {$column} = :target_id AND user_id = :user_id");
            return $stmt->execute([
                ':target_id' => $cartIdOrProductId,
                ':user_id'   => (int)$userId,
            ]);
        } catch (\Exception $e) {
            error_log("Error removing cart item for user {$userId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear all items from the user's cart.
     */
    public function clearByUserId(int|string $userId): bool {
        try {
            $stmt = $this->getDb()->prepare("DELETE FROM " . self::TABLE . " WHERE user_id = :user_id");
            return $stmt->execute([':user_id' => (int)$userId]);
        } catch (\Exception $e) {
            error_log("Error clearing cart for user {$userId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Calculate cart subtotal and total item quantity for a user.
     *
     * @return array{subtotal: float, itemCount: int}
     */
    public function calculateTotals(array $items): array {
        $subtotal = 0.0;
        $itemCount = 0;

        foreach ($items as $item) {
            $qty = (int)($item['quantity'] ?? 0);
            $lineTotal = isset($item['sub_total'])
                ? (float)$item['sub_total']
                : ((float)($item['price'] ?? 0) * $qty);
            $subtotal += $lineTotal;
            $itemCount += $qty;
        }

        return [
            'subtotal'  => round($subtotal, 2),
            'itemCount' => $itemCount,
        ];
    }
}
