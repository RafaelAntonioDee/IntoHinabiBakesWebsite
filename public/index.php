<?php

/**
 * Into Hinabi Bakes - Front Controller
 */

// 1. Bootstrap configuration & autoloading
require_once dirname(__DIR__) . '/config/app.php';

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\AboutController;
use App\Controllers\ContactController;
use App\Controllers\AuthController;
use App\Controllers\ProfileController;
use App\Controllers\BakerController;
use App\Controllers\AdminController;

// 2. Initialize Router
$router = new Router();

// 3. Define Public Routes
$router->get('/', [HomeController::class, 'index']);
$router->get('/products', [ProductController::class, 'index']);
$router->get('/about', [AboutController::class, 'index']);
$router->get('/contact', [ContactController::class, 'index']);
$router->post('/contact', [ContactController::class, 'submit']);

// 4. Define Authentication & Customer Routes
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/profile', [ProfileController::class, 'index']);

// 5. Define Baker Kitchen Routes (RBAC: Baker & Admin)
$router->get('/baker', [BakerController::class, 'index']);
$router->get('/baker/orders', [BakerController::class, 'orders']);
$router->post('/baker/orders/status', [BakerController::class, 'updateOrderStatus']);
$router->get('/baker/inventory', [BakerController::class, 'inventory']);
$router->post('/baker/inventory/toggle', [BakerController::class, 'toggleStock']);

// 6. Define Administrator Routes (RBAC: Admin Only)
$router->get('/admin', [AdminController::class, 'index']);
$router->get('/admin/users', [AdminController::class, 'users']);
$router->post('/admin/users/role', [AdminController::class, 'updateUserRole']);
$router->post('/admin/users/create', [AdminController::class, 'createUser']);
$router->post('/admin/users/delete', [AdminController::class, 'deleteUser']);
$router->get('/admin/orders', [AdminController::class, 'orders']);
$router->post('/admin/orders/status', [AdminController::class, 'updateOrderStatus']);
$router->get('/admin/products', [AdminController::class, 'products']);
$router->post('/admin/products/save', [AdminController::class, 'saveProduct']);
$router->post('/admin/products/delete', [AdminController::class, 'deleteProduct']);
$router->get('/admin/inquiries', [AdminController::class, 'inquiries']);

// 7. Dispatch Request
$router->dispatch();
