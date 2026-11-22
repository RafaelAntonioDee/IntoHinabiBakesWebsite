<main class="max-w-7xl mx-auto px-4 py-12 flex-grow">
    <div class="text-center max-w-2xl mx-auto mb-10 space-y-2">
        <h1 class="text-3xl md:text-4xl font-extrabold text-amber-950">Our Fresh Bakes Menu</h1>
        <p class="text-gray-600 text-sm">Handcrafted cookies and artisan gourmet cakes baked fresh daily in small batches.</p>
    </div>

    <!-- Filter Buttons (Powered dynamically by Database category_id) -->
    <div class="flex justify-center space-x-3 mb-10 flex-wrap gap-y-3">
        <button class="filter-btn bg-amber-800 text-white px-6 py-2 rounded-full font-medium transition cursor-pointer shadow-sm" data-category-id="all">
            All Treats
        </button>
        <?php foreach ($categories as $cat): ?>
            <?php 
                $count = count(array_filter($products, fn($p) => ($p['category_id'] ?? null) == $cat['id']));
                $icon = ($cat['id'] == 1) ? '🍪' : (($cat['id'] == 2) ? '🎂' : '✨');
                $labelName = ($cat['id'] == 1) ? 'Cookies' : (($cat['id'] == 2) ? 'Cakes' : $cat['category_name']);
            ?>
            <button class="filter-btn bg-gray-200 text-gray-800 hover:bg-gray-300 px-6 py-2 rounded-full font-medium transition cursor-pointer shadow-sm" data-category-id="<?= e($cat['id']) ?>">
                <?= $icon ?> <?= e($labelName) ?> (<?= $count ?>)
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Product Grid -->
    <div class="grid md:grid-cols-3 gap-8">
        <?php foreach ($products as $item): ?>
            <?php 
                $catId = $item['category_id'] ?? 1;
                $catName = $item['category_name'] ?? ($catId == 2 ? 'Cakes' : 'Cookies');
            ?>
            <div class="product-item category-<?= e($catId) ?> bg-white rounded-xl shadow-md overflow-hidden border border-amber-100 flex flex-col transition hover:shadow-lg" data-category-id="<?= e($catId) ?>">
                <img src="<?= e($item['image_url']) ?>" alt="<?= e($item['name']) ?>" class="w-full h-56 object-cover">
                <div class="p-6 flex flex-col flex-grow">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-xl font-bold text-gray-900"><?= e($item['name']) ?></h3>
                        <span class="text-xs uppercase tracking-wider font-semibold px-2 py-0.5 rounded <?= ($catId == 2) ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' ?>">
                            <?= e($catName) ?>
                        </span>
                    </div>
                    <p class="text-gray-600 mb-4 flex-grow text-sm"><?= e($item['description']) ?></p>
                    <span class="text-amber-700 font-bold text-lg">
                        $<?= number_format((float)($item['price'] ?? 0), 2) ?> / <?= e($item['unit'] ?? 'each') ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>
