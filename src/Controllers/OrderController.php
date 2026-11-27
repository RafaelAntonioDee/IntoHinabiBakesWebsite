<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;

class OrderController extends Controller {
    /**
     * List all orders for the logged-in user
     */
    public function index(): void {
        if (!Session::check()) {
            flash('error', 'Please log in to view your orders.');
            $this->redirect('/login');
            return;
        }

        $userId = Session::id();
        $orderModel = new Order();
        $orders = $orderModel->findByUser($userId);

        $this->view('pages/orders', [
            'pageTitle' => 'My Orders - Into Hinabi Bakes',
            'orders'    => $orders,
        ]);
    }

    /**
     * Show a single order's details
     */
    public function show(): void {
        if (!Session::check()) {
            flash('error', 'Please log in to view order details.');
            $this->redirect('/login');
            return;
        }

        $orderId = $_GET['id'] ?? null;
        if (!$orderId) {
            flash('error', 'Order not found.');
            $this->redirect('/orders');
            return;
        }

        $userId = Session::id();
        $orderModel = new Order();
        $order = $orderModel->findByIdAndUser($orderId, $userId);

        if (!$order) {
            flash('error', 'Order not found or access denied.');
            $this->redirect('/orders');
            return;
        }

        // Fetch order items
        $orderItemModel = new OrderItem();
        $items = $orderItemModel->findByOrder($orderId);

        // Fetch delivery address
        $address = null;
        if (!empty($order['address_id'])) {
            $addressModel = new Address();
            $address = $addressModel->findById($order['address_id']);
        }

        $this->view('pages/order-detail', [
            'pageTitle' => 'Order #' . $orderId . ' - Into Hinabi Bakes',
            'order'     => $order,
            'items'     => $items,
            'address'   => $address,
        ]);
    }

    /**
     * Cancel a pending order (POST)
     */
    public function cancel(): void {
        if (!Session::check()) {
            flash('error', 'Please log in.');
            $this->redirect('/login');
            return;
        }

        $orderId = $_POST['order_id'] ?? null;
        if (!$orderId) {
            flash('error', 'Invalid order.');
            $this->redirect('/orders');
            return;
        }

        $userId = Session::id();
        $orderModel = new Order();
        $cancelled = $orderModel->cancel($orderId, $userId);

        if ($cancelled) {
            flash('success', 'Order #' . $orderId . ' has been cancelled.');
        } else {
            flash('error', 'Could not cancel this order. It may have already been processed.');
        }

        $this->redirect('/orders');
    }
}
