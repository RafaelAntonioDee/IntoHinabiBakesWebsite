<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;

class CheckoutController extends Controller {
    /**
     * Display the checkout page
     */
    public function index(): void {
        if (!Session::check()) {
            flash('error', 'Please log in to proceed to checkout.');
            $this->redirect('/login');
            return;
        }

        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            flash('error', 'Your cart is empty.');
            $this->redirect('/products');
            return;
        }

        $userId = Session::id();
        $addressModel = new Address();
        $addresses = $addressModel->findByUser($userId);

        $subtotal = $this->calculateSubtotal($cart);
        $deliveryFee = $this->calculateDeliveryFee($subtotal);
        $total = round($subtotal + $deliveryFee, 2);

        $this->view('pages/checkout', [
            'pageTitle'   => 'Checkout - Into Hinabi Bakes',
            'cart'        => $cart,
            'addresses'   => $addresses,
            'subtotal'    => $subtotal,
            'deliveryFee' => $deliveryFee,
            'total'       => $total,
        ]);
    }

    /**
     * Process the checkout form submission and place the order
     */
    public function placeOrder(): void {
        if (!Session::check()) {
            flash('error', 'Please log in to place an order.');
            $this->redirect('/login');
            return;
        }

        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            flash('error', 'Your cart is empty.');
            $this->redirect('/products');
            return;
        }

        $userId        = Session::id();
        $addressId     = $_POST['address_id'] ?? null;
        $paymentMethod = $_POST['payment_method'] ?? 'cod';
        $notes         = trim($_POST['notes'] ?? '');

        // ── Handle "new address" submitted inline during checkout ──
        if (empty($addressId) || $addressId === 'new') {
            $addressId = $this->createInlineAddress($userId);
            if (!$addressId) {
                flash('error', 'Please provide a valid delivery address.');
                $this->redirect('/checkout');
                return;
            }
        }

        // Verify the address belongs to this user
        $addressModel = new Address();
        $address = $addressModel->findByIdAndUser($addressId, $userId);
        if (!$address) {
            flash('error', 'Invalid delivery address selected.');
            $this->redirect('/checkout');
            return;
        }

        // ── Calculate totals ──
        $subtotal    = $this->calculateSubtotal($cart);
        $deliveryFee = $this->calculateDeliveryFee($subtotal);
        $total       = round($subtotal + $deliveryFee, 2);

        // ── Create the order ──
        try {
            $orderModel = new Order();
            $orderId = $orderModel->create([
                'user_id'        => $userId,
                'address_id'     => $addressId,
                'subtotal'       => $subtotal,
                'delivery_fee'   => $deliveryFee,
                'total'          => $total,
                'payment_method' => $paymentMethod,
                'notes'          => $notes,
                'status'         => 'pending',
            ]);

            if (!$orderId) {
                flash('error', 'Failed to place order. Please try again.');
                $this->redirect('/checkout');
                return;
            }

            // ── Save order items ──
            $orderItemModel = new OrderItem();
            $items = [];
            foreach ($cart as $item) {
                $items[] = [
                    'product_id'   => $item['product_id'],
                    'product_name' => $item['name'],
                    'quantity'     => $item['quantity'],
                    'unit_price'   => $item['price'],
                ];
            }

            $orderItemModel->createMany($orderId, $items);

            // ── Clear cart ──
            $_SESSION['cart'] = [];

            flash('success', 'Order #' . $orderId . ' placed successfully! Thank you for your purchase.');
            $this->redirect('/orders/' . $orderId);
        } catch (\Throwable $e) {
            $msg = env('APP_DEBUG', false)
                ? 'Checkout error: ' . $e->getMessage()
                : 'Something went wrong. Please try again.';
            flash('error', $msg);
            $this->redirect('/checkout');
        }
    }

    // ─── Helpers ─────────────────────────────────────────────

    /**
     * Calculate cart subtotal from session cart
     */
    private function calculateSubtotal(array $cart): float {
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        return round($subtotal, 2);
    }

    /**
     * Calculate delivery fee (free delivery for orders over a threshold)
     */
    private function calculateDeliveryFee(float $subtotal): float {
        $freeDeliveryThreshold = 50.00;
        $standardFee = 5.00;

        return ($subtotal >= $freeDeliveryThreshold) ? 0.00 : $standardFee;
    }

    /**
     * Create a new address from inline checkout form fields
     */
    private function createInlineAddress(int|string $userId): int|string|false {
        $recipientName = trim($_POST['recipient_name'] ?? $_POST['full_name'] ?? '');
        $streetAddress = trim($_POST['street_address'] ?? '');
        $barangay      = trim($_POST['barangay'] ?? '');
        $city          = trim($_POST['city'] ?? '');
        $postalCode    = trim($_POST['postal_code'] ?? $_POST['zip_code'] ?? '');
        $deliveryNotes = trim($_POST['delivery_notes'] ?? $_POST['notes'] ?? '');

        if ((empty($streetAddress) && empty($barangay)) || empty($city)) {
            return false;
        }

        $addressModel = new Address();
        return $addressModel->create([
            'user_id'        => $userId,
            'recipient_name' => $recipientName,
            'street_address' => $streetAddress,
            'barangay'       => $barangay,
            'city'           => $city,
            'postal_code'    => $postalCode,
            'delivery_notes' => $deliveryNotes,
            'is_default'     => false,
        ]);
    }
}
