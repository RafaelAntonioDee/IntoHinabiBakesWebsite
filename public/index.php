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

// 5. Dispatch Request
$router->dispatch();
