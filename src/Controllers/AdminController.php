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
            $err = 'Invalid user or role selection.';
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => $err], 400);
            }
            flash('error', $err);
            $this->redirect('/admin/users');
            return;
        }

        // Safety: Prevent admin from accidentally demoting their own current session
        if ($userId === Session::id() && $roleId !== 3) {
            $err = 'Safety protection: You cannot demote your own administrator account while logged in.';
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => $err], 403);
            }
            flash('error', $err);
            $this->redirect('/admin/users');
            return;
        }

        $targetUser = $this->userModel->findById($userId);
        if (!$targetUser) {
            $err = 'User not found.';
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => $err], 404);
            }
            flash('error', $err);
            $this->redirect('/admin/users');
            return;
        }

        $success = $this->userModel->updateRole($userId, $roleId);
        if ($success) {
            $roleNames = [1 => 'Customer', 2 => 'Baker', 3 => 'Admin'];
            $newRoleName = $roleNames[$roleId] ?? "Role #{$roleId}";
            $msg = "Updated role for '{$targetUser['full_name']}' to {$newRoleName}!";
            if ($this->isAjax()) {
                $this->json(['success' => true, 'message' => $msg, 'user_id' => $userId, 'role_id' => $roleId, 'role_name' => $newRoleName]);
            }
            flash('success', $msg);
        } else {
            $err = 'Failed to update user role.';
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => $err], 500);
            }
            flash('error', $err);
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

        $errors = [];
        if ($fullName === '') {
            $errors['full_name'] = 'Full name is required.';
        }
        if ($email === '') {
            $errors['email'] = 'Email address is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        } elseif ($this->userModel->findByEmail($email)) {
            $errors['email'] = 'Email already exists.';
        }
        if ($password === '') {
            $errors['password'] = 'Password is required.';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'Password must be at least 6 characters.';
        }

        if (!empty($errors)) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'errors' => $errors, 'error' => reset($errors)], 400);
            }
            flash('error', implode(' ', $errors));
            $this->redirect('/admin/users');
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $created = $this->userModel->create($fullName, $email, $hashedPassword, $roleId);

        if ($created) {
            $msg = "Account for '{$fullName}' created successfully!";
            if ($this->isAjax()) {
                flash('success', $msg);
                $this->json(['success' => true, 'message' => $msg, 'redirect' => url('/admin/users')]);
            }
            flash('success', $msg);
        } else {
            $err = 'Failed to create user account.';
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => $err], 500);
            }
            flash('error', $err);
        }

        $this->redirect('/admin/users');
    }

    /**
     * Delete a user account
     */
    public function deleteUser(): void {
        $userId = (int)($_POST['user_id'] ?? 0);

        if ($userId <= 0) {
            $err = 'Invalid user.';
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => $err], 400);
            }
            flash('error', $err);
            $this->redirect('/admin/users');
            return;
        }

        // Safety: Cannot delete self
        if ($userId === Session::id()) {
            $err = 'You cannot delete your own account while logged in.';
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => $err], 403);
            }
            flash('error', $err);
            $this->redirect('/admin/users');
            return;
        }

        $deleted = $this->userModel->delete($userId);
        if ($deleted) {
            $msg = 'User removed successfully.';
            if ($this->isAjax()) {
                $this->json(['success' => true, 'message' => $msg, 'user_id' => $userId]);
            }
            flash('success', $msg);
        } else {
            $err = 'Could not delete user account.';
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => $err], 500);
            }
            flash('error', $err);
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
            $err = 'Invalid order or status.';
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => $err], 400);
            }
            flash('error', $err);
            $this->redirect('/admin/orders');
            return;
        }

        $updated = $this->orderModel->updateStatus($orderId, $statusId);
        if ($updated) {
            $statusLabels = [
                1 => 'Pending',
                2 => 'Baking',
                3 => 'Ready for Delivery',
                4 => 'Completed',
            ];
            $statusName = $statusLabels[$statusId] ?? "Status #{$statusId}";
            $msg = "Order #{$orderId} status updated to {$statusName}.";
            if ($this->isAjax()) {
                $this->json(['success' => true, 'message' => $msg, 'order_id' => $orderId, 'status_id' => $statusId, 'status_name' => $statusName]);
            }
            flash('success', $msg);
        } else {
            $err = 'Failed to update order status.';
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => $err], 500);
            }
            flash('error', $err);
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

        $errors = [];
        if ($name === '') {
            $errors['name'] = 'Product name is required.';
        }
        if ($imageUrl === '') {
            $errors['image_url'] = 'Image URL is required.';
        }

        if (!empty($errors)) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'errors' => $errors, 'error' => reset($errors)], 400);
            }
            flash('error', implode(' ', $errors));
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
            $msg = $saved ? "Updated '{$name}'!" : 'Failed to update product.';
        } else {
            $saved = $this->productModel->createProduct($data);
            $msg = $saved ? "Created new item '{$name}'!" : 'Failed to create product.';
        }

        if ($this->isAjax()) {
            if ($saved) {
                flash('success', $msg);
                $this->json(['success' => true, 'message' => $msg, 'redirect' => url('/admin/products')]);
            }
            $this->json(['success' => false, 'error' => $msg], 500);
        }

        flash($saved ? 'success' : 'error', $msg);
        $this->redirect('/admin/products');
    }

    /**
     * Delete product
     */
    public function deleteProduct(): void {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $err = 'Invalid product.';
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => $err], 400);
            }
            flash('error', $err);
            $this->redirect('/admin/products');
            return;
        }

        $deleted = $this->productModel->deleteProduct($id);
        $msg = $deleted ? 'Product deleted.' : 'Failed to delete product.';
        if ($this->isAjax()) {
            if ($deleted) {
                $this->json(['success' => true, 'message' => $msg, 'id' => $id]);
            }
            $this->json(['success' => false, 'error' => $msg], 500);
        }
        flash($deleted ? 'success' : 'error', $msg);
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
