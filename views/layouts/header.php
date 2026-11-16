<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Sweet Cookie Oven') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= asset('assets/css/custom.css') ?>">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body class="flex flex-col min-h-screen bg-amber-50/20">

    <nav class="bg-white text-gray-800 shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <!-- Logo -->
            <a href="<?= url('/') ?>" class="text-2xl font-bold text-amber-900 flex items-center space-x-2">
                <img src="https://png.pngtree.com/recommend-works/png-clipart/20240728/ourmid/pngtree-simple-logo-of-chocolate-cookies-png-image_13238339.png" alt="Sweet Cookie Oven Logo" class="w-9 h-9 object-contain">
                <span>Into Hinabi Bakes</span>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex space-x-6 items-center">
                <a href="<?= url('/') ?>" class="text-gray-800 hover:text-amber-600 font-semibold">Home</a>
                <a href="<?= url('/products') ?>" class="text-gray-800 hover:text-amber-600 font-semibold">Cookies & Treats</a>
                <a href="<?= url('/about') ?>" class="text-gray-800 hover:text-amber-600 font-semibold">About Us</a>
                <a href="<?= url('/contact') ?>" class="text-gray-800 hover:text-amber-600 font-semibold">Contact</a>

                <?php if (\App\Core\Session::check()): ?>
                    <a href="<?= url('/profile') ?>" class="text-amber-800 font-semibold hover:underline">
                        👤 <?= e(\App\Core\Session::name()) ?>
                    </a>
                    <a href="<?= url('/logout') ?>" class="bg-amber-800 hover:bg-amber-900 text-white px-4 py-2 rounded-md font-semibold text-sm transition">
                        Logout
                    </a>
                <?php else: ?>
                    <a href="<?= url('/login') ?>" class="text-gray-800 hover:text-amber-600 font-semibold">Login</a>
                    <a href="<?= url('/register') ?>" class="bg-amber-800 hover:bg-amber-900 text-white px-4 py-2 rounded-md font-semibold text-sm transition">
                        Sign Up
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile Toggle -->
            <button id="nav-toggle" class="md:hidden focus:outline-none text-gray-800 text-2xl" aria-label="Toggle navigation">☰</button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white px-4 pt-2 pb-4 space-y-2 border-t">
            <a href="<?= url('/') ?>" class="block text-gray-800 hover:text-amber-600 font-medium">Home</a>
            <a href="<?= url('/products') ?>" class="block text-gray-800 hover:text-amber-600 font-medium">Cookies & Treats</a>
            <a href="<?= url('/about') ?>" class="block text-gray-800 hover:text-amber-600 font-medium">About Us</a>
            <a href="<?= url('/contact') ?>" class="block text-gray-800 hover:text-amber-600 font-medium">Contact</a>
            <?php if (\App\Core\Session::check()): ?>
                <a href="<?= url('/profile') ?>" class="block text-amber-800 font-semibold">Profile (<?= e(\App\Core\Session::name()) ?>)</a>
                <a href="<?= url('/logout') ?>" class="block text-red-600 font-semibold">Logout</a>
            <?php else: ?>
                <a href="<?= url('/login') ?>" class="block text-gray-800 hover:text-amber-600 font-medium">Login</a>
                <a href="<?= url('/register') ?>" class="block text-amber-600 font-semibold">Sign Up</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Global Flash Messages Notification Area -->
    <div class="max-w-7xl mx-auto px-4 w-full">
        <?php if ($success = flash('success')): ?>
            <div class="mt-4 p-4 bg-green-100 border border-green-200 text-green-800 rounded-lg text-sm font-medium shadow-sm flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span>✓</span>
                    <span><?= e($success) ?></span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-green-800 font-bold ml-4">&times;</button>
            </div>
        <?php endif; ?>

        <?php if ($error = flash('error')): ?>
            <div class="mt-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg text-sm font-medium shadow-sm flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span>⚠️</span>
                    <span><?= e($error) ?></span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-red-700 font-bold ml-4">&times;</button>
            </div>
        <?php endif; ?>
    </div>
