<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Order;
use App\Models\Product;

class BakerController extends Controller {
    private Order $orderModel;
    private Product $productModel;

    public function __construct() {
        // Enforce RBAC: Only Baker and Admin accounts can access
        Session::requireRole(['baker', 'admin']);
        $this->orderModel = new Order();
        $this->productModel = new Product();
    }

    /**
     * Baker Kitchen Dashboard Overview
     */
    public function index(): void {
        $stats = $this->orderModel->getOrderStats();
        $bakingQueue = $this->orderModel->getKitchenBakingSummary();
        $activeOrders = $this->orderModel->getAllOrders(null, 10);
        $products = $this->productModel->allForManagement();

        // Calculate out-of-stock items
        $outOfStockCount = count(array_filter($products, fn($p) => empty($p['availability']) || empty($p['is_active'])));

        $this->view('baker/dashboard', [
            'pageTitle'       => 'Baker Kitchen Dashboard - Into Hinabi Bakes',
            'stats'           => $stats,
            'bakingQueue'     => $bakingQueue,
            'activeOrders'    => $activeOrders,
            'totalProducts'   => count($products),
            'outOfStockCount' => $outOfStockCount,
        ]);
    }

    /**
     * Kitchen Orders Queue with Status Management
     */
    public function orders(): void {
        $statusFilter = isset($_GET['status']) && $_GET['status'] !== '' ? (int)$_GET['status'] : null;
        $orders = $this->orderModel->getAllOrders($statusFilter);
        $statuses = $this->orderModel->getAllStatuses();
        $stats = $this->orderModel->getOrderStats();
        $bakingQueue = $this->orderModel->getKitchenBakingSummary();

        $this->view('baker/orders', [
            'pageTitle'    => 'Baking Queue & Orders - Into Hinabi Bakes',
            'orders'       => $orders,
            'statuses'     => $statuses,
            'currentFilter'=> $statusFilter,
            'stats'        => $stats,
            'bakingQueue'  => $bakingQueue,
        ]);
    }

    /**
     * Update order status (Pending -> Baking -> Ready -> Completed)
     */
    public function updateOrderStatus(): void {
        $orderId = (int)($_POST['order_id'] ?? 0);
        $statusId = (int)($_POST['status_id'] ?? 0);

        if ($orderId <= 0 || $statusId <= 0) {
            flash('error', 'Invalid order or status selection.');
            $this->redirect('/baker/orders');
            return;
        }

        $success = $this->orderModel->updateStatus($orderId, $statusId);
        if ($success) {
            $statusLabels = [
                1 => 'Pending',
                2 => 'Baking / In Oven',
                3 => 'Ready for Delivery',
                4 => 'Completed',
            ];
            $label = $statusLabels[$statusId] ?? "Status #{$statusId}";
            flash('success', "Order #{$orderId} updated to {$label}!");
        } else {
            flash('error', "Could not update order #{$orderId}.");
        }

        $this->redirect('/baker/orders');
    }

    /**
     * Inventory & Flavor Stock Management
     */
    public function inventory(): void {
        $categoryId = isset($_GET['category']) && $_GET['category'] !== '' ? (int)$_GET['category'] : null;
        $search = trim($_GET['search'] ?? '');

        $products = $this->productModel->allForManagement($categoryId, $search);
        $categories = $this->productModel->getCategories();

        $this->view('baker/inventory', [
            'pageTitle'       => 'Kitchen Inventory - Into Hinabi Bakes',
            'products'        => $products,
            'categories'      => $categories,
            'selectedCategory'=> $categoryId,
            'searchQuery'     => $search,
        ]);
    }

    /**
     * Toggle product availability (In Stock / Sold Out)
     */
    public function toggleStock(): void {
        $productId = (int)($_POST['product_id'] ?? 0);

        if ($productId <= 0) {
            flash('error', 'Invalid product selected.');
            $this->redirect('/baker/inventory');
            return;
        }

        $product = $this->productModel->findById($productId);
        if (!$product) {
            flash('error', 'Product not found.');
            $this->redirect('/baker/inventory');
            return;
        }

        $toggled = $this->productModel->toggleAvailability($productId);
        if ($toggled) {
            flash('success', "Updated stock status for '" . htmlspecialchars($product['name']) . "'!");
        } else {
            flash('error', 'Failed to update stock status.');
        }

        $this->redirect('/baker/inventory');
    }
}
