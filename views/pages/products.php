<main class="max-w-7xl mx-auto px-4 py-12 flex-grow" x-data="productCatalog()">
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
                    <div class="flex items-center justify-between mt-auto">
                        <span class="text-amber-700 font-bold text-lg">
                            $<?= number_format((float)($item['price'] ?? 0), 2) ?> / <?= e($item['unit'] ?? 'each') ?>
                        </span>
                        <button type="button"
                                @click="addToCart({
                                    id: <?= (int)($item['id'] ?? 0) ?>,
                                    name: '<?= e($item['name'] ?? '') ?>',
                                    price: <?= (float)($item['price'] ?? 0) ?>,
                                    image_url: '<?= e($item['image_url'] ?? '') ?>',
                                    unit: '<?= e($item['unit'] ?? 'each') ?>',
                                    category_id: <?= (int)$catId ?>,
                                    category_name: '<?= e($catName) ?>'
                                })"
                                class="bg-amber-800 hover:bg-amber-900 text-white p-2 rounded-lg shadow transition duration-150 cursor-pointer flex-shrink-0"
                                title="Add to Cart">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <!-- Cart Added Toast Notification -->
    <div x-show="showToast" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed bottom-6 right-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-5 py-3 text-sm font-medium shadow-lg flex items-center gap-2 z-50" style="display: none;">
        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span x-text="toastMessage"></span>
    </div>
</main>

<script>
function productCatalog() {
    return {
        showToast: false,
        toastMessage: '',

        addToCart(product) {
            let cart = JSON.parse(localStorage.getItem('hinabi_cart') || '[]');
            const existing = cart.find(item => item.id === product.id);
            if (existing) {
                existing.qty += 1;
            } else {
                cart.push({ ...product, qty: 1 });
            }
            localStorage.setItem('hinabi_cart', JSON.stringify(cart));

            this.toastMessage = product.name + ' added to cart';
            this.showToast = true;
            setTimeout(() => { this.showToast = false; }, 2000);
        }
    };
}
</script>
