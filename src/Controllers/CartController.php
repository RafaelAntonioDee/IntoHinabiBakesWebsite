<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Product;

class CartController extends Controller {
    /**
     * Display the cart page
     */
    public function index(): void {
        $cart = $this->getCart();
        $totals = $this->calculateTotals($cart);

        $this->view('pages/cart', [
            'pageTitle' => 'Your Cart - Into Hinabi Bakes',
            'cart'      => $cart,
            'subtotal'  => $totals['subtotal'],
            'itemCount' => $totals['itemCount'],
        ]);
    }

    /**
     * Add a product to the cart (POST)
     */
    public function add(): void {
        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity  = max(1, (int) ($_POST['quantity'] ?? 1));

        if ($productId <= 0) {
            flash('error', 'Invalid product.');
            $this->redirect('/products');
            return;
        }

        // Look up product details to store in cart
        $productModel = new Product();
        $allProducts = $productModel->all();
        $product = null;
        foreach ($allProducts as $p) {
            if ((int) $p['id'] === $productId) {
                $product = $p;
                break;
            }
        }

        if (!$product) {
            flash('error', 'Product not found.');
            $this->redirect('/products');
            return;
        }

        // Initialize cart in session
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // If product already in cart, increase quantity
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = [
                'product_id' => $productId,
                'name'       => $product['name'],
                'price'      => (float) $product['price'],
                'image_url'  => $product['image_url'] ?? '',
                'unit'       => $product['unit'] ?? 'each',
                'quantity'   => $quantity,
            ];
        }

        flash('success', e($product['name']) . ' added to cart!');
        $this->redirect('/cart');
    }

    /**
     * Update item quantity in the cart (POST)
     */
    public function update(): void {
        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity  = (int) ($_POST['quantity'] ?? 0);

        if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$productId]);
                flash('success', 'Item removed from cart.');
            } else {
                $_SESSION['cart'][$productId]['quantity'] = $quantity;
                flash('success', 'Cart updated.');
            }
        }

        $this->redirect('/cart');
    }

    /**
     * Remove a single item from the cart (POST)
     */
    public function remove(): void {
        $productId = (int) ($_POST['product_id'] ?? 0);

        if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
            $name = $_SESSION['cart'][$productId]['name'];
            unset($_SESSION['cart'][$productId]);
            flash('success', e($name) . ' removed from cart.');
        }

        $this->redirect('/cart');
    }

    /**
     * Clear the entire cart (POST)
     */
    public function clear(): void {
        $_SESSION['cart'] = [];
        flash('success', 'Cart cleared.');
        $this->redirect('/cart');
    }

    /**
     * Return the cart as JSON (for AJAX requests)
     */
    public function getCartJson(): void {
        $cart = $this->getCart();
        $totals = $this->calculateTotals($cart);

        $this->json([
            'items'     => array_values($cart),
            'subtotal'  => $totals['subtotal'],
            'itemCount' => $totals['itemCount'],
        ]);
    }

    // ─── Helpers ─────────────────────────────────────────────

    /**
     * Get current cart contents from session
     */
    private function getCart(): array {
        return $_SESSION['cart'] ?? [];
    }

    /**
     * Calculate subtotal and total item count
     */
    private function calculateTotals(array $cart): array {
        $subtotal  = 0;
        $itemCount = 0;

        foreach ($cart as $item) {
            $subtotal  += $item['price'] * $item['quantity'];
            $itemCount += $item['quantity'];
        }

        return [
            'subtotal'  => round($subtotal, 2),
            'itemCount' => $itemCount,
        ];
    }
}
