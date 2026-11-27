<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;

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

        $userId = Session::id();
        $cartModel = new Cart();
        $cart = $cartModel->getByUserId($userId);
        if (empty($cart)) {
            flash('error', 'Your cart is empty.');
            $this->redirect('/products');
            return;
        }

        $addressModel = new Address();
        $addresses = $addressModel->getAllByUserId($userId);

        $totals = $cartModel->calculateTotals($cart);
        $subtotal = $totals['subtotal'];
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
     * Process checkout and transfer Supabase cart items to orders + order_items
     */
    public function placeOrder(): void {
        if (!Session::check()) {
            flash('error', 'Please log in to place an order.');
            $this->redirect('/login');
            return;
        }

        $userId = Session::id();
        $cartModel = new Cart();
        $cart = $cartModel->getByUserId($userId);
        if (empty($cart)) {
            flash('error', 'Your cart is empty.');
            $this->redirect('/products');
            return;
        }

        $addressId = $_POST['address_id'] ?? null;
        $paymentMethodRaw = strtolower(trim((string)($_POST['payment_method'] ?? '1')));
        $paymentTypeId = match ($paymentMethodRaw) {
            '2', 'gcash' => 2,
            '3', 'bank'  => 3,
            default      => 1,
        };

        // Handle inline new address during checkout
        if (empty($addressId) || $addressId === 'new') {
            $addressId = $this->createInlineAddress($userId);
            if (!$addressId) {
                flash('error', 'Please provide a valid delivery address.');
                $this->redirect('/checkout');
                return;
            }
        }

        $addressModel = new Address();
        $address = $addressModel->findByIdForUser($addressId, $userId);
        if (!$address) {
            flash('error', 'Invalid delivery address selected.');
            $this->redirect('/checkout');
            return;
        }

        $totals = $cartModel->calculateTotals($cart);
        $deliveryFee = $this->calculateDeliveryFee($totals['subtotal']);

        $orderModel = new Order();
        $result = $orderModel->createFromCart($userId, (int)$addressId, $paymentTypeId, $deliveryFee);

        if (!$result['success']) {
            flash('error', $result['error'] ?? 'Failed to place order. Please try again.');
            $this->redirect('/checkout');
            return;
        }

        $orderId = (int)$result['order_id'];
        flash('success', 'Order ' . ($result['order_number'] ?? ('#' . $orderId)) . ' placed successfully!');
        $this->redirect('/orders/show?id=' . $orderId);
    }

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
