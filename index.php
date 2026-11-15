<?php
session_start();
include 'header.php';
?>

<main class="flex-grow">
    <?php if (isset($_SESSION['user_name']) || isset($_SESSION['user_id'])): ?>
        <div class="bg-amber-100 border-b border-amber-200/80 px-6 py-3">
            <div class="max-w-7xl mx-auto flex items-center justify-between text-sm">
                <div class="flex items-center space-x-2 text-amber-950 font-medium">
                    <span>👋</span>
                    <span>Welcome back, <strong class="font-bold"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Cookie Lover'); ?></strong>! Ready for fresh bakes today?</span>
                </div>
                <a href="products.php" class="text-amber-900 underline hover:text-amber-950 font-semibold text-xs">
                    Browse Today's Batch →
                </a>
            </div>
        </div>
    <?php endif; ?>

    <section class="relative bg-amber-950 text-white overflow-hidden py-20 md:py-28">
        <div class="absolute inset-0 z-0 opacity-40">
            <img src="https://images.unsplash.com/photo-1558961363-fa8fdf82db35?auto=format&fit=crop&w=1600&q=80" alt="Freshly baked cookies" class="w-full h-full object-cover">
        </div>
        
        <div class="absolute inset-0 bg-gradient-to-r from-amber-950 via-amber-950/80 to-transparent z-0"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 md:px-12">
            <div class="max-w-2xl space-y-6">
                <h1 class="text-4xl md:text-6xl font-black text-amber-50 tracking-tight leading-tight">
                    Freshly Baked Gourmet Cookies
                </h1>
                <p class="text-amber-100/90 text-lg md:text-xl font-normal leading-relaxed">
                    Handcrafted, chewy, gooey, and crisp gourmet cookies baked daily with premium ingredients.
                </p>
                <div class="pt-2">
                    <a href="products.php" class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-bold text-base px-8 py-3.5 rounded-xl shadow-lg hover:shadow-xl transition duration-200">
                        Explore Cookie Flavors
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-16 md:py-20">
        <div class="grid md:grid-cols-3 gap-8 text-center">
            
            <div class="bg-white p-8 rounded-2xl border border-amber-100 shadow-sm hover:shadow-md transition">
                <div class="text-4xl mb-4">🍪</div>
                <h3 class="text-xl font-bold text-amber-950 mb-2">Classic Warm Cookies</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Golden-brown chocolate chip, oatmeal raisin, and double chocolate chunk cookies baked fresh hourly.
                </p>
            </div>

            <div class="bg-white p-8 rounded-2xl border border-amber-100 shadow-sm hover:shadow-md transition">
                <div class="text-4xl mb-4">🧁</div>
                <h3 class="text-xl font-bold text-amber-950 mb-2">Stuffed & Specialty</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Decadent cookies stuffed with Nutella, caramel, peanut butter, and cheesecake fillings.
                </p>
            </div>

            <div class="bg-white p-8 rounded-2xl border border-amber-100 shadow-sm hover:shadow-md transition">
                <div class="text-4xl mb-4">🎁</div>
                <h3 class="text-xl font-bold text-amber-950 mb-2">Cookie Boxes & Gifts</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Custom cookie tins and gift boxes perfect for birthday parties, holidays, and events.
                </p>
            </div>

        </div>
    </section>
</main>

<?php include 'footer.php'; ?>