<?php

/**
 * Sweet Cookie Oven - Front Controller
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
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\OrderController;

// 2. Initialize Router
$router = new Router();

// 3. Define Public Routes
$router->get('/', [HomeController::class, 'index']);
$router->get('/products', [ProductController::class, 'index']);
$router->get('/about', [AboutController::class, 'index']);
$router->get('/contact', [ContactController::class, 'index']);
$router->post('/contact', [ContactController::class, 'submit']);

// 4. Define Authentication & User Routes
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/profile', [ProfileController::class, 'index']);

// 5. Cart Routes
$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->post('/cart/update', [CartController::class, 'update']);
$router->post('/cart/remove', [CartController::class, 'remove']);
$router->post('/cart/clear', [CartController::class, 'clear']);
$router->get('/cart/json', [CartController::class, 'getCartJson']);

// 6. Checkout Routes
$router->get('/checkout', [CheckoutController::class, 'index']);
$router->post('/checkout', [CheckoutController::class, 'placeOrder']);

// 7. Order Routes
$router->get('/orders', [OrderController::class, 'index']);
$router->get('/orders/show', [OrderController::class, 'show']);
$router->post('/orders/cancel', [OrderController::class, 'cancel']);

// 8. Dispatch Request
$router->dispatch();
