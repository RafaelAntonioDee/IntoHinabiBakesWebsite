<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sweet Cookie Oven</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="custom.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body class="flex flex-col min-h-screen bg-amber-50/20">

    <nav class="bg-white text-gray-800 shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <!-- Updated Logo with Image Link -->
            <a href="index.php" class="text-2xl font-bold text-amber-900 flex items-center space-x-2">
                <img src="https://png.pngtree.com/recommend-works/png-clipart/20240728/ourmid/pngtree-simple-logo-of-chocolate-cookies-png-image_13238339.png" alt="Sweet Cookie Oven Logo" class="w-9 h-9 object-contain">
                <span>Sweet Cookie Oven</span>
            </a>

            <div class="hidden md:flex space-x-6 items-center">
                <a href="index.php" class="text-gray-800 hover:text-amber-600 font-semibold">Home</a>
                <a href="products.php" class="text-gray-800 hover:text-amber-600 font-semibold">Cookies & Treats</a>
                <a href="about.php" class="text-gray-800 hover:text-amber-600 font-semibold">About Us</a>
                <a href="contact.php" class="text-gray-800 hover:text-amber-600 font-semibold">Contact</a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" class="text-amber-800 font-semibold hover:underline">
                        👤 <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                    </a>
                    <a href="logout.php" class="bg-amber-800 hover:bg-amber-900 text-white px-4 py-2 rounded-md font-semibold text-sm transition">
                        Logout
                    </a>
                <?php else: ?>
                    <a href="login.php" class="text-gray-800 hover:text-amber-600 font-semibold">Login</a>
                    <a href="register.php" class="bg-amber-800 hover:bg-amber-900 text-white px-4 py-2 rounded-md font-semibold text-sm transition">
                        Sign Up
                    </a>
                <?php endif; ?>
            </div>

            <button id="nav-toggle" class="md:hidden focus:outline-none text-gray-800 text-2xl">☰</button>
        </div>

        <div id="mobile-menu" class="hidden md:hidden bg-white px-4 pt-2 pb-4 space-y-2 border-t">
            <a href="index.php" class="block text-gray-800 hover:text-amber-600">Home</a>
            <a href="products.php" class="block text-gray-800 hover:text-amber-600">Cookies & Treats</a>
            <a href="about.php" class="block text-gray-800 hover:text-amber-600">About Us</a>
            <a href="contact.php" class="block text-gray-800 hover:text-amber-600">Contact</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php" class="block text-amber-800 font-semibold">Profile (<?php echo htmlspecialchars($_SESSION['user_name']); ?>)</a>
                <a href="logout.php" class="block text-red-600 font-semibold">Logout</a>
            <?php else: ?>
                <a href="login.php" class="block text-gray-800 hover:text-amber-600">Login</a>
                <a href="register.php" class="block text-amber-600 font-semibold">Sign Up</a>
            <?php endif; ?>
        </div>
    </nav>