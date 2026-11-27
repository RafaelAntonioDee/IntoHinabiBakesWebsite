<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Cart;

class CartController extends Controller {
    private Cart $cartModel;

    public function __construct() {
        $this->cartModel = new Cart();
    }

    /**
     * Display the cart page
     */
    public function index(): void {
        if (!Session::check()) {
            flash('error', 'Please log in to view your cart.');
            $this->redirect('/login');
            return;
        }

        $userId = Session::id();
        $items = $this->cartModel->getByUserId($userId);
        $totals = $this->cartModel->calculateTotals($items);

        $this->view('pages/cart', [
            'pageTitle' => 'Your Cart - Into Hinabi Bakes',
            'cart'      => $items,
            'subtotal'  => $totals['subtotal'],
            'itemCount' => $totals['itemCount'],
        ]);
    }

    /**
     * Add a product to the user's Supabase cart (POST / AJAX)
     */
    public function add(): void {
        $userId = Session::id();
        if (!$userId) {
            if ($this->isAjax()) {
                $this->json([
                    'success'        => false,
                    'error'          => 'Please log in first to add items to your cart.',
                    'requires_login' => true,
                ], 401);
            }
            flash('error', 'Please log in first to add items to your cart.');
            $this->redirect('/login');
            return;
        }

        $input = $this->getJsonOrPostInput();
        $productId = (int)($input['product_id'] ?? 0);
        $quantity = max(1, (int)($input['quantity'] ?? 1));
        $customerNote = trim((string)($input['customer_note'] ?? $input['note'] ?? ''));

        if ($productId <= 0) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => 'Invalid product.'], 400);
            }
            flash('error', 'Invalid product.');
            $this->redirect('/products');
            return;
        }

        $result = $this->cartModel->addItem($userId, $productId, $quantity, $customerNote);
        if (!$result['success']) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => $result['error'] ?? 'Could not add item to cart.'], 400);
            }
            flash('error', $result['error'] ?? 'Could not add item to cart.');
            $this->redirect('/products');
            return;
        }

        $items = $this->cartModel->getByUserId($userId);
        $totals = $this->cartModel->calculateTotals($items);
        $productName = $result['product']['name'] ?? 'Item';

        if ($this->isAjax()) {
            $this->json([
                'success'   => true,
                'message'   => "{$productName} added to cart!",
                'cart_id'   => $result['cart_id'] ?? null,
                'items'     => $items,
                'subtotal'  => $totals['subtotal'],
                'itemCount' => $totals['itemCount'],
            ]);
        }

        flash('success', e($productName) . ' added to cart!');
        $this->redirect('/cart');
    }

    /**
     * Update item quantity in the user's Supabase cart (POST / AJAX)
     */
    public function update(): void {
        $userId = Session::id();
        if (!$userId) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => 'Please log in first.', 'requires_login' => true], 401);
            }
            $this->redirect('/login');
            return;
        }

        $input = $this->getJsonOrPostInput();
        $cartId = (int)($input['cart_id'] ?? 0);
        $productId = (int)($input['product_id'] ?? 0);
        $quantity = (int)($input['quantity'] ?? 0);

        $updated = false;
        if ($cartId > 0) {
            $updated = $this->cartModel->updateQuantity($userId, $cartId, $quantity, true);
        } elseif ($productId > 0) {
            $updated = $this->cartModel->updateQuantity($userId, $productId, $quantity, false);
        }

        if ($this->isAjax()) {
            $items = $this->cartModel->getByUserId($userId);
            $totals = $this->cartModel->calculateTotals($items);
            $this->json([
                'success'   => $updated,
                'message'   => $updated ? ($quantity <= 0 ? 'Item removed from cart.' : 'Cart updated.') : 'Item not found.',
                'items'     => $items,
                'subtotal'  => $totals['subtotal'],
                'itemCount' => $totals['itemCount'],
            ], $updated ? 200 : 404);
        }

        if ($updated) {
            flash('success', $quantity <= 0 ? 'Item removed from cart.' : 'Cart updated.');
        } else {
            flash('error', 'Could not update cart item.');
        }
        $this->redirect('/cart');
    }

    /**
     * Remove a single item from the user's Supabase cart (POST / AJAX)
     */
    public function remove(): void {
        $userId = Session::id();
        if (!$userId) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => 'Please log in first.', 'requires_login' => true], 401);
            }
            $this->redirect('/login');
            return;
        }

        $input = $this->getJsonOrPostInput();
        $cartId = (int)($input['cart_id'] ?? 0);
        $productId = (int)($input['product_id'] ?? 0);

        $removed = false;
        if ($cartId > 0) {
            $removed = $this->cartModel->removeItem($userId, $cartId, true);
        } elseif ($productId > 0) {
            $removed = $this->cartModel->removeItem($userId, $productId, false);
        }

        if ($this->isAjax()) {
            $items = $this->cartModel->getByUserId($userId);
            $totals = $this->cartModel->calculateTotals($items);
            $this->json([
                'success'   => $removed,
                'message'   => $removed ? 'Item removed from cart.' : 'Item not found.',
                'items'     => $items,
                'subtotal'  => $totals['subtotal'],
                'itemCount' => $totals['itemCount'],
            ], $removed ? 200 : 404);
        }

        flash($removed ? 'success' : 'error', $removed ? 'Item removed from cart.' : 'Could not remove item.');
        $this->redirect('/cart');
    }

    /**
     * Clear all items from the user's Supabase cart (POST / AJAX)
     */
    public function clear(): void {
        $userId = Session::id();
        if (!$userId) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => 'Please log in first.', 'requires_login' => true], 401);
            }
            $this->redirect('/login');
            return;
        }

        $this->cartModel->clearByUserId($userId);

        if ($this->isAjax()) {
            $this->json([
                'success'   => true,
                'message'   => 'Cart cleared.',
                'items'     => [],
                'subtotal'  => 0,
                'itemCount' => 0,
            ]);
        }

        flash('success', 'Cart cleared.');
        $this->redirect('/cart');
    }

    /**
     * Return the user's cart as JSON (for AJAX requests)
     */
    public function getCartJson(): void {
        $userId = Session::id();
        if (!$userId) {
            $this->json([
                'items'     => [],
                'subtotal'  => 0,
                'itemCount' => 0,
            ]);
            return;
        }

        $items = $this->cartModel->getByUserId($userId);
        $totals = $this->cartModel->calculateTotals($items);

        $this->json([
            'items'     => $items,
            'subtotal'  => $totals['subtotal'],
            'itemCount' => $totals['itemCount'],
        ]);
    }

    private function getJsonOrPostInput(): array {
        $raw = file_get_contents('php://input');
        $decoded = $raw ? json_decode($raw, true) : null;
        return is_array($decoded) ? $decoded : $_POST;
    }
}
