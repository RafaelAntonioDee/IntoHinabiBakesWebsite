<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\User;
use App\Models\Role;
use App\Models\Order;
use App\Models\Product;
use App\Models\ContactInquiry;

class AdminController extends Controller {
    private User $userModel;
    private Role $roleModel;
    private Order $orderModel;
    private Product $productModel;
    private ContactInquiry $inquiryModel;

    public function __construct() {
        // Enforce RBAC: Strictly Admin accounts only!
        Session::requireRole(['admin']);
        $this->userModel = new User();
        $this->roleModel = new Role();
        $this->orderModel = new Order();
        $this->productModel = new Product();
        $this->inquiryModel = new ContactInquiry();
    }

    /**
     * Admin Dashboard Overview
     */
    public function index(): void {
        $userStats = $this->userModel->getUserStats();
        $orderStats = $this->orderModel->getOrderStats();
        $recentOrders = $this->orderModel->getAllOrders(null, 5);
        $recentUsers = $this->userModel->getAllUsers(null, null);
        $recentUsers = array_slice($recentUsers, 0, 5);
        $products = $this->productModel->allForManagement();
        $inquiriesCount = $this->inquiryModel->count();

        $this->view('admin/dashboard', [
            'pageTitle'      => 'Admin Portal - Into Hinabi Bakes',
            'userStats'      => $userStats,
            'orderStats'     => $orderStats,
            'recentOrders'   => $recentOrders,
            'recentUsers'    => $recentUsers,
            'totalProducts'  => count($products),
            'inquiriesCount' => $inquiriesCount,
        ]);
    }

    /**
     * User Management: List, Search, and Role Assignment
     */
    public function users(): void {
        $search = trim($_GET['search'] ?? '');
        $roleFilter = isset($_GET['role']) && $_GET['role'] !== '' ? (int)$_GET['role'] : null;

        $users = $this->userModel->getAllUsers($search, $roleFilter);
        $roles = $this->roleModel->getAll();
        $userStats = $this->userModel->getUserStats();

        $this->view('admin/users', [
            'pageTitle'    => 'User Management - Into Hinabi Bakes',
            'users'        => $users,
            'roles'        => $roles,
            'userStats'    => $userStats,
            'search'       => $search,
            'roleFilter'   => $roleFilter,
        ]);
    }

    /**
     * Update a user's role (Promote to Baker/Admin, or Demote to Customer)
     */
    public function updateUserRole(): void {
        $userId = (int)($_POST['user_id'] ?? 0);
        $roleId = (int)($_POST['role_id'] ?? 0);

        if ($userId <= 0 || $roleId <= 0) {
            flash('error', 'Invalid user or role selection.');
            $this->redirect('/admin/users');
            return;
        }

        // Safety: Prevent admin from accidentally demoting their own current session
        if ($userId === Session::id() && $roleId !== 3) {
            flash('error', 'Safety protection: You cannot demote your own administrator account while logged in.');
            $this->redirect('/admin/users');
            return;
        }

        $targetUser = $this->userModel->findById($userId);
        if (!$targetUser) {
            flash('error', 'User not found.');
            $this->redirect('/admin/users');
            return;
        }

        $success = $this->userModel->updateRole($userId, $roleId);
        if ($success) {
            $roleNames = [1 => 'Customer', 2 => 'Baker', 3 => 'Admin'];
            $newRoleName = $roleNames[$roleId] ?? "Role #{$roleId}";
            flash('success', "Updated role for '{$targetUser['full_name']}' to {$newRoleName}!");
        } else {
            flash('error', 'Failed to update user role.');
        }

        $this->redirect('/admin/users');
    }

    /**
     * Create a new user or staff account directly
     */
    public function createUser(): void {
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $roleId = (int)($_POST['role_id'] ?? 1);

        if (empty($fullName) || empty($email) || empty($password)) {
            flash('error', 'All fields are required.');
            $this->redirect('/admin/users');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Please enter a valid email address.');
            $this->redirect('/admin/users');
            return;
        }

        if (strlen($password) < 6) {
            flash('error', 'Password must be at least 6 characters long.');
            $this->redirect('/admin/users');
            return;
        }

        $existing = $this->userModel->findByEmail($email);
        if ($existing) {
            flash('error', 'A user with this email address already exists.');
            $this->redirect('/admin/users');
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $created = $this->userModel->create($fullName, $email, $hashedPassword, $roleId);

        if ($created) {
            flash('success', "Account for '{$fullName}' created successfully!");
        } else {
            flash('error', 'Failed to create user account.');
        }

        $this->redirect('/admin/users');
    }

    /**
     * Delete a user account
     */
    public function deleteUser(): void {
        $userId = (int)($_POST['user_id'] ?? 0);

        if ($userId <= 0) {
            flash('error', 'Invalid user.');
            $this->redirect('/admin/users');
            return;
        }

        // Safety: Cannot delete self
        if ($userId === Session::id()) {
            flash('error', 'You cannot delete your own account while logged in.');
            $this->redirect('/admin/users');
            return;
        }

        $deleted = $this->userModel->delete($userId);
        if ($deleted) {
            flash('success', 'User removed successfully.');
        } else {
            flash('error', 'Could not delete user account.');
        }

        $this->redirect('/admin/users');
    }

    /**
     * Full System Orders Oversight
     */
    public function orders(): void {
        $statusFilter = isset($_GET['status']) && $_GET['status'] !== '' ? (int)$_GET['status'] : null;
        $orders = $this->orderModel->getAllOrders($statusFilter);
        $statuses = $this->orderModel->getAllStatuses();
        $stats = $this->orderModel->getOrderStats();

        $this->view('admin/orders', [
            'pageTitle'    => 'Order Management - Into Hinabi Bakes',
            'orders'       => $orders,
            'statuses'     => $statuses,
            'currentFilter'=> $statusFilter,
            'stats'        => $stats,
        ]);
    }

    /**
     * Update order status from Admin panel
     */
    public function updateOrderStatus(): void {
        $orderId = (int)($_POST['order_id'] ?? 0);
        $statusId = (int)($_POST['status_id'] ?? 0);

        if ($orderId <= 0 || $statusId <= 0) {
            flash('error', 'Invalid order or status.');
            $this->redirect('/admin/orders');
            return;
        }

        $updated = $this->orderModel->updateStatus($orderId, $statusId);
        if ($updated) {
            flash('success', "Order #{$orderId} status updated.");
        } else {
            flash('error', 'Failed to update order status.');
        }

        $this->redirect('/admin/orders');
    }

    /**
     * Menu & Product Catalog Management
     */
    public function products(): void {
        $categoryId = isset($_GET['category']) && $_GET['category'] !== '' ? (int)$_GET['category'] : null;
        $search = trim($_GET['search'] ?? '');

        $products = $this->productModel->allForManagement($categoryId, $search);
        $categories = $this->productModel->getCategories();

        $this->view('admin/products', [
            'pageTitle'       => 'Product Catalog - Into Hinabi Bakes',
            'products'        => $products,
            'categories'      => $categories,
            'selectedCategory'=> $categoryId,
            'search'          => $search,
        ]);
    }

    /**
     * Create or update product
     */
    public function saveProduct(): void {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 1);
        $price = (float)($_POST['price'] ?? 0.00);
        $imageUrl = trim($_POST['image_url'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $availability = isset($_POST['availability']) ? 1 : 0;

        if (empty($name)) {
            flash('error', 'Product name is required.');
            $this->redirect('/admin/products');
            return;
        }

        $data = [
            'name'         => $name,
            'description'  => $description,
            'category_id'  => $categoryId,
            'price'        => $price,
            'image_url'    => $imageUrl,
            'is_active'    => $isActive,
            'availability' => $availability,
        ];

        if ($id > 0) {
            $saved = $this->productModel->updateProduct($id, $data);
            flash($saved ? 'success' : 'error', $saved ? "Updated '{$name}'!" : 'Failed to update product.');
        } else {
            $saved = $this->productModel->createProduct($data);
            flash($saved ? 'success' : 'error', $saved ? "Created new item '{$name}'!" : 'Failed to create product.');
        }

        $this->redirect('/admin/products');
    }

    /**
     * Delete product
     */
    public function deleteProduct(): void {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            flash('error', 'Invalid product.');
            $this->redirect('/admin/products');
            return;
        }

        $deleted = $this->productModel->deleteProduct($id);
        flash($deleted ? 'success' : 'error', $deleted ? 'Product deleted.' : 'Failed to delete product.');
        $this->redirect('/admin/products');
    }

    /**
     * View Contact Inquiries
     */
    public function inquiries(): void {
        $inquiries = $this->inquiryModel->getAll();

        $this->view('admin/inquiries', [
            'pageTitle' => 'Contact Inquiries - Into Hinabi Bakes',
            'inquiries' => $inquiries,
        ]);
    }
}
