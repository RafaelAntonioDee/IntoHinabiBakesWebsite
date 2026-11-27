<?php
    $sessionUser = \App\Core\Session::name() ?? '';
    $sessionUserId = \App\Core\Session::id();
    $isLoggedIn = \App\Core\Session::check();

    // Pre-index products by ID for clean, zero-escape modal lookup
    $productsMap = [];
    foreach ($products as $p) {
        $pId = (int)($p['id'] ?? 0);
        $pCatId = (int)($p['category_id'] ?? 1);
        $productsMap[$pId] = [
            'id'            => $pId,
            'name'          => $p['name'] ?? '',
            'category_id'   => $pCatId,
            'category_name' => $p['category_name'] ?? ($pCatId == 2 ? 'Mini Cakes' : 'Cookies'),
            'description'   => $p['description'] ?? '',
            'allergens'     => $p['allergens'] ?? '',
            'price'         => number_format((float)($p['price'] ?? 0), 2),
            'unit'          => $p['unit'] ?? 'each',
            'image_url'     => $p['image_url'] ?? '',
        ];
    }
?>
<main 
    class="max-w-7xl mx-auto px-4 py-12 flex-grow"
    x-data="productCatalog()"
    @keydown.escape.window="closeModal()"
>
    <div class="text-center max-w-2xl mx-auto mb-10 space-y-2">
        <h1 class="text-3xl md:text-4xl font-extrabold text-amber-950">Our Fresh Bakes Menu</h1>
        <p class="text-gray-600 text-sm">Handcrafted cookies and artisan gourmet cakes baked fresh daily in small batches.</p>
    </div>

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-8">
        <!-- Filter Buttons (Powered dynamically by Database category_id) -->
        <div class="flex justify-start gap-3 flex-wrap">
        <button class="filter-btn bg-amber-800 text-white px-6 py-2 rounded-full font-medium transition cursor-pointer shadow-sm" data-category-id="all">
            <svg class="hidden w-4 h-4 text-amber-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
            </svg>
            <span>All Treats</span>
        </button>
        <?php foreach ($categories as $cat): ?>
            <?php 
                $count = count(array_filter($products, fn($p) => ($p['category_id'] ?? null) == $cat['id']));
                $labelName = ($cat['id'] == 1) ? 'Cookies' : (($cat['id'] == 2) ? 'Mini Cakes' : $cat['category_name']);
            ?>
            <button class="filter-btn bg-gray-200 text-gray-800 hover:bg-gray-300 px-6 py-2 rounded-full font-medium transition cursor-pointer shadow-sm" data-category-id="<?= e($cat['id']) ?>">
                <?php if ($cat['id'] == 1): ?>
                    <svg class="hidden w-4 h-4 text-amber-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.5 9h.01M15.5 9h.01M12 13h.01M9 15.5h.01M14.5 15h.01"/>
                    </svg>
                <?php else: ?>
                    <svg class="hidden w-4 h-4 text-rose-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 0A2.25 2.25 0 109.75 4.5a2.25 2.25 0 002.25 2.25zm0 0A2.25 2.25 0 1114.25 4.5 2.25 2.25 0 0112 6.75zm-7.5 7.5h15M4.5 18.75h15M3 10.5h18a1.5 1.5 0 011.5 1.5v6.75a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18.75V12a1.5 1.5 0 011.5-1.5z"/>
                    </svg>
                <?php endif; ?>
                <span><?= e($labelName) ?> (<?= $count ?>)</span>
            </button>
        <?php endforeach; ?>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3 lg:ml-auto">
            <label for="price-sort" class="flex items-center gap-2 text-sm font-semibold text-amber-950">
                <span class="whitespace-nowrap">Price:</span>
                <div class="relative w-full sm:w-48">
                    <select id="price-sort" class="appearance-none w-full pl-3.5 pr-9 py-2 border border-gray-300 rounded-lg bg-white text-gray-800 font-normal outline-none focus:outline-none focus:ring-2 focus:ring-amber-800 focus:border-amber-800 hover:border-amber-600 transition cursor-pointer">
                        <option value="">Any price order</option>
                        <option value="low-high">Low to High</option>
                        <option value="high-low">High to Low</option>
                    </select>
                    <svg class="w-4 h-4 text-gray-500 pointer-events-none absolute right-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                    </svg>
                </div>
            </label>
            <label for="rating-sort" class="flex items-center gap-2 text-sm font-semibold text-amber-950">
                <span class="whitespace-nowrap">Rating:</span>
                <div class="relative w-full sm:w-48">
                    <select id="rating-sort" class="appearance-none w-full pl-3.5 pr-9 py-2 border border-gray-300 rounded-lg bg-white text-gray-800 font-normal outline-none focus:outline-none focus:ring-2 focus:ring-amber-800 focus:border-amber-800 hover:border-amber-600 transition cursor-pointer">
                        <option value="">Any rating order</option>
                        <option value="high-low">High to Low</option>
                        <option value="low-high">Low to High</option>
                        <option value="most-reviewed">Most Reviewed</option>
                    </select>
                    <svg class="w-4 h-4 text-gray-500 pointer-events-none absolute right-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                    </svg>
                </div>
            </label>
        </div>
    </div>

    <!-- Product Grid -->
    <div id="product-grid" class="grid md:grid-cols-3 gap-8">
        <?php foreach ($products as $index => $item): ?>
            <?php 
                $catId = (int)($item['category_id'] ?? 1);
                $catName = $item['category_name'] ?? ($catId == 2 ? 'Cakes' : 'Cookies');
                $itemId = (int)($item['id'] ?? 0);
                $reviewData = $reviewsData[$itemId] ?? [];
            ?>
            <div 
                class="product-item category-<?= e($catId) ?> bg-white rounded-xl shadow-md overflow-hidden border border-amber-100 flex flex-col transition-all duration-200 hover:shadow-xl hover:-translate-y-1 cursor-pointer group" 
                data-category-id="<?= e($catId) ?>"
                data-default-order="<?= e($index) ?>"
                data-price="<?= e(number_format((float)($item['price'] ?? 0), 2, '.', '')) ?>"
                data-rating="<?= e((float)($reviewData['average_rating'] ?? 0)) ?>"
                data-review-count="<?= e((int)($reviewData['total_count'] ?? 0)) ?>"
                data-name="<?= e($item['name']) ?>"
                @click="openModal(<?= $itemId ?>)"
                tabindex="0"
                role="button"
                aria-label="View details for <?= e($item['name']) ?>"
                @keydown.enter="openModal(<?= $itemId ?>)"
                @keydown.space.prevent="openModal(<?= $itemId ?>)"
            >
                <!-- Card Image Container -->
                <div class="relative overflow-hidden w-full h-56 bg-amber-50">
                    <img 
                        src="<?= e($item['image_url']) ?>" 
                        alt="<?= e($item['name']) ?>" 
                        class="w-full h-56 object-cover transform transition-transform duration-300 group-hover:scale-105"
                        loading="lazy"
                    >
                    <div class="absolute inset-0 bg-stone-900/10 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center pointer-events-none">
                        <span class="bg-white/95 text-amber-950 text-xs font-semibold px-3.5 py-1.5 rounded-full shadow-md flex items-center gap-1.5 transform translate-y-2 group-hover:translate-y-0 transition-transform duration-200">
                            <svg class="w-3.5 h-3.5 text-amber-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            View Description
                        </span>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-6 flex flex-col flex-grow">
                    <div class="flex justify-between items-start mb-1 gap-2">
                        <h3 class="text-xl font-bold text-gray-900 group-hover:text-amber-900 transition-colors"><?= e($item['name']) ?></h3>
                        <span class="text-xs uppercase tracking-wider font-semibold px-2 py-0.5 rounded flex-shrink-0 <?= ($catId == 2) ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' ?>">
                            <?= e($catName) ?>
                        </span>
                    </div>

                    <!-- Live Backend Rating Preview -->
                    <div class="flex items-center gap-1.5 mb-2.5 text-xs">
                        <template x-if="reviewsData[<?= $itemId ?>] && reviewsData[<?= $itemId ?>].total_count > 0">
                            <div class="flex items-center gap-1.5 text-amber-800 font-semibold">
                                <svg class="w-3.5 h-3.5 fill-amber-800 text-amber-800" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                <span x-text="reviewsData[<?= $itemId ?>].average_rating"></span>
                                <span class="text-gray-400 font-normal text-[11px]" x-text="'(' + reviewsData[<?= $itemId ?>].total_count + ')'"></span>
                            </div>
                        </template>
                        <template x-if="!reviewsData[<?= $itemId ?>] || reviewsData[<?= $itemId ?>].total_count === 0">
                            <span class="text-stone-400 text-[11px] font-medium flex items-center gap-1">
                                <svg class="w-3 h-3 text-stone-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                                No ratings yet
                            </span>
                        </template>
                    </div>

                    <p class="text-gray-600 mb-4 flex-grow text-sm line-clamp-2 leading-relaxed">
                        <?= e($item['description']) ?>
                    </p>

                    <div class="flex items-center justify-between pt-3 border-t border-amber-50 mt-auto">
                        <span class="text-amber-800 font-extrabold text-lg">
                            ₱<?= number_format((float)($item['price'] ?? 0), 2) ?> <span class="text-xs font-normal text-gray-500">/ <?= e($item['unit'] ?? 'each') ?></span>
                        </span>
                        <span class="text-xs font-semibold text-amber-800 group-hover:text-amber-950 flex items-center gap-1 transition-colors">
                            Details
                            <svg class="w-3.5 h-3.5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php require_once dirname(__DIR__) . '/components/product_modal.php'; ?>
</main>
