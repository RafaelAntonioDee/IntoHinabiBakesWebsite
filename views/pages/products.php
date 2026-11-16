<main class="max-w-7xl mx-auto px-4 py-12 flex-grow">
    <h1 class="text-3xl font-bold text-center text-amber-900 mb-8">Our Fresh Cookie Menu</h1>

    <!-- Filter Buttons -->
    <div class="flex justify-center space-x-3 mb-10 flex-wrap gap-y-3">
        <button class="filter-btn bg-amber-800 text-white px-6 py-2 rounded-full font-medium transition cursor-pointer shadow-sm" data-filter="all">
            All Cookies
        </button>
        <button class="filter-btn bg-gray-200 text-gray-800 hover:bg-gray-300 px-6 py-2 rounded-full font-medium transition cursor-pointer shadow-sm" data-filter="classic">
            Classics
        </button>
        <button class="filter-btn bg-gray-200 text-gray-800 hover:bg-gray-300 px-6 py-2 rounded-full font-medium transition cursor-pointer shadow-sm" data-filter="stuffed">
            Stuffed & Deluxe
        </button>
        <button class="filter-btn bg-gray-200 text-gray-800 hover:bg-gray-300 px-6 py-2 rounded-full font-medium transition cursor-pointer shadow-sm" data-filter="box">
            Cookie Boxes
        </button>
    </div>

    <!-- Product Grid -->
    <div class="grid md:grid-cols-3 gap-8">
        <?php foreach ($products as $item): ?>
            <div class="product-item <?= e($item['category']) ?> bg-white rounded-xl shadow-md overflow-hidden border border-amber-100 flex flex-col transition hover:shadow-lg">
                <img src="<?= e($item['image_url']) ?>" alt="<?= e($item['name']) ?>" class="w-full h-52 object-cover">
                <div class="p-6 flex flex-col flex-grow">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-xl font-bold text-gray-900"><?= e($item['name']) ?></h3>
                        <span class="text-xs uppercase tracking-wider font-semibold px-2 py-0.5 rounded bg-amber-100 text-amber-800">
                            <?= e($item['category']) ?>
                        </span>
                    </div>
                    <p class="text-gray-600 mb-4 flex-grow text-sm"><?= e($item['description']) ?></p>
                    <span class="text-amber-700 font-bold text-lg">
                        $<?= number_format((float)$item['price'], 2) ?> / <?= e($item['unit'] ?? 'each') ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>
