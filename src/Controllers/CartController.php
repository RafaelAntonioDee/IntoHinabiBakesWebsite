<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Order;
use Throwable;

class CartController extends Controller {
    /**
     * GET /cart
     */
    public function index(): void {
        $this->view('pages/cart', [
            'pageTitle' => 'Your Cart - Into Hinabi Bakes',
        ]);
    }

    /**
     * GET /checkout 
     */
    public function checkout(): void {
        if (!Session::check()) {
            flash('error', 'Please log in to proceed with checkout.');
            redirect('/login');
        }

        $user = [
            'full_name' => Session::name(),
        ];

        $this->view('pages/checkout', [
            'pageTitle' => 'Checkout - Into Hinabi Bakes',
            'user'      => $user,
        ]);
    }

    /**
     * POST /checkout 
     */
    public function placeOrder(): void {
        if (!Session::check()) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Please log in to place an order.'], 401);
            }
            flash('error', 'Please log in to place an order.');
            redirect('/login');
        }

        $recipientName = trim($_POST['recipient_name'] ?? '');
        $phoneNumber   = trim($_POST['phone_number'] ?? '');
        $streetAddress = trim($_POST['street_address'] ?? '');
        $barangay      = trim($_POST['barangay'] ?? '');
        $city          = trim($_POST['city'] ?? '');
        $notes         = trim($_POST['notes'] ?? '');
        $paymentMethod = trim($_POST['payment_method'] ?? 'cod');
        $cartItemsJson = $_POST['cart_items'] ?? '[]';
        $totalAmount   = floatval($_POST['total_amount'] ?? 0);

        // Validation
        $errors = [];
        if (empty($recipientName)) $errors[] = 'Recipient name is required.';
        if (empty($phoneNumber))   $errors[] = 'Phone number is required.';
        if (empty($streetAddress)) $errors[] = 'Street address is required.';
        if (empty($barangay))      $errors[] = 'Barangay is required.';
        if (empty($city))          $errors[] = 'City is required.';

        $cartItems = json_decode($cartItemsJson, true);
        if (empty($cartItems) || !is_array($cartItems)) {
            $errors[] = 'Your cart is empty.';
        }

        if (!array_key_exists($paymentMethod, Order::PAYMENT_TYPES)) {
            $errors[] = 'Please choose a valid payment method.';
        }

        // Merge cart lines into [product_id => quantity]
        $items = [];
        if (empty($errors)) {
            foreach ($cartItems as $line) {
                $productId = (int)($line['id'] ?? 0);
                $qty       = (int)($line['qty'] ?? 0);
                if ($productId <= 0 || $qty <= 0 || $qty > 999) {
                    $errors[] = 'Your cart contains an invalid item. Please update your cart.';
                    break;
                }
                $items[$productId] = ($items[$productId] ?? 0) + $qty;
            }
        }

        if (!empty($errors)) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => implode(' ', $errors)], 422);
            }
            flash('error', implode(' ', $errors));
            redirect('/checkout');
        }

        try {
            $result = (new Order())->createOrder(
                (int)Session::id(),
                [
                    'recipient_name' => $recipientName,
                    'phone_number'   => $phoneNumber,
                    'street_address' => $streetAddress,
                    'barangay'       => $barangay,
                    'city'           => $city,
                    'delivery_notes' => $notes !== '' ? $notes : null,
                ],
                $items,
                $paymentMethod,
                $notes
            );
        } catch (Throwable $e) {
            error_log('Checkout failed: ' . $e->getMessage());
            $result = ['success' => false, 'message' => 'Unable to reach the order system right now. Please try again shortly.'];
        }

        if (!$result['success']) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => $result['message']], 422);
            }
            flash('error', $result['message']);
            redirect('/checkout');
        }

        $message = 'Your order ' . $result['order_number'] . ' has been received and is now Pending. Our bakers will start preparing your treats soon.';

        if ($this->isAjax()) {
            $this->json([
                'success'      => true,
                'message'      => $message,
                'order_number' => $result['order_number'],
                'total'        => number_format($result['total'], 2, '.', ''),
                'redirect'     => url('/'),
            ]);
        }

        flash('success', $message);
        redirect('/');
    }

    /**
     * Check if the current request is an AJAX request
     */
    protected function isAjax(): bool {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));
    }
}
