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
            'category_name' => $p['category_name'] ?? ($pCatId == 2 ? 'Cakes' : 'Cookies'),
            'description'   => $p['description'] ?? '',
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
                $labelName = ($cat['id'] == 1) ? 'Cookies' : (($cat['id'] == 2) ? 'Cakes' : $cat['category_name']);
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
                            $<?= number_format((float)($item['price'] ?? 0), 2) ?> <span class="text-xs font-normal text-gray-500">/ <?= e($item['unit'] ?? 'each') ?></span>
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

    <!-- Interactive Product Description & Reviews Overlay Modal -->
    <div 
        x-show="activeProduct" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog" 
        aria-modal="true"
        aria-labelledby="modal-product-title"
    >
        <!-- Backdrop Blur -->
        <div 
            x-show="activeProduct"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-stone-950/70 backdrop-blur-sm transition-opacity"
            @click="closeModal()"
        ></div>

        <!-- Modal Dialog Container -->
        <div class="min-h-full flex items-center justify-center p-3 sm:p-6 text-center">
            <div 
                x-show="activeProduct"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all w-full max-w-4xl lg:max-w-5xl border border-amber-100 flex flex-col md:flex-row max-h-[92vh]"
                @click.stop
            >
                <!-- Top-Right Close Button -->
                <button 
                    type="button" 
                    @click="closeModal()"
                    class="absolute top-4 right-4 z-30 bg-white/90 hover:bg-white text-gray-700 hover:text-gray-900 rounded-full p-2 shadow-md transition cursor-pointer focus:outline-none"
                    aria-label="Close modal"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <!-- Left Column: Product Details & Visuals -->
                <div class="w-full md:w-1/2 flex flex-col overflow-y-auto border-b md:border-b-0 md:border-r border-amber-100">
                    <!-- Modal Product Image -->
                    <div class="relative h-60 sm:h-68 w-full bg-amber-50 flex-shrink-0">
                        <img 
                            :src="activeProduct ? activeProduct.image_url : ''" 
                            :alt="activeProduct ? activeProduct.name : ''" 
                            class="w-full h-full object-cover"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-stone-950/75 via-stone-950/20 to-transparent"></div>
                        
                        <!-- Category Badge -->
                        <span 
                            class="absolute bottom-4 left-4 text-xs uppercase tracking-wider font-bold px-3 py-1 rounded-full shadow-sm"
                            :class="activeProduct && activeProduct.category_id == 2 ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800'"
                            x-text="activeProduct ? activeProduct.category_name : ''"
                        ></span>
                    </div>

                    <!-- Left Column Body -->
                    <div class="p-6 sm:p-7 space-y-5 flex flex-col flex-grow">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 id="modal-product-title" class="text-2xl font-bold text-amber-950" x-text="activeProduct ? activeProduct.name : ''"></h3>
                                <!-- Subtitle with restored icon -->
                                <p class="text-xs font-semibold text-emerald-700 mt-1 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Freshly Baked Daily from Scratch</span>
                                </p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <div class="text-2xl font-black text-amber-800">
                                    $<span x-text="activeProduct ? activeProduct.price : ''"></span>
                                </div>
                                <div class="text-xs font-medium text-gray-500" x-text="activeProduct ? 'per ' + activeProduct.unit : ''"></div>
                            </div>
                        </div>

                        <!-- Clean Description (No icons, no yellow background box) -->
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-amber-900 mb-1.5">Description</h4>
                            <p class="text-gray-700 text-sm leading-relaxed" x-text="activeProduct ? activeProduct.description : ''"></p>
                        </div>

                        <!-- Allergen Cautions Under Description -->
                        <div class="p-3 bg-amber-50/80 rounded-xl border border-amber-200/60">
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-amber-800 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                                </svg>
                                <div class="text-xs text-amber-950">
                                    <span class="font-bold">Allergen Caution:</span>
                                    <span class="text-amber-900/90 leading-relaxed block mt-0.5">
                                        Contains wheat (gluten), dairy (milk, butter), and eggs. Prepared in a bakery environment that also handles tree nuts, peanuts, and soy.
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Bakery Quality Subtitle Chips with Restored Icons -->
                        <div class="grid grid-cols-2 gap-3 pt-1 text-xs text-gray-700 font-medium">
                            <div class="flex items-center gap-2 p-2.5 rounded-lg bg-stone-50 border border-stone-200/80">
                                <svg class="w-4 h-4 text-amber-700 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Warm hourly tray batch</span>
                            </div>
                            <div class="flex items-center gap-2 p-2.5 rounded-lg bg-stone-50 border border-stone-200/80">
                                <svg class="w-4 h-4 text-amber-700 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>100% Real creamery butter</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="pt-4 mt-auto flex items-center gap-3">
                            <a 
                                :href="'<?= url('/contact') ?>?item=' + (activeProduct ? encodeURIComponent(activeProduct.name) : '')" 
                                class="flex-1 bg-amber-800 hover:bg-amber-900 text-white font-bold py-2.5 px-4 rounded-xl shadow text-sm text-center transition duration-150 cursor-pointer"
                            >
                                Inquire or Order
                            </a>
                            <button 
                                type="button" 
                                @click="closeModal()" 
                                class="px-5 py-2.5 rounded-xl border border-gray-300 hover:bg-gray-100 text-gray-700 font-medium text-sm transition duration-150 cursor-pointer"
                            >
                                Close
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Review This Product Part (Image 1 Style - Connected to Backend) -->
                <div class="w-full md:w-1/2 flex flex-col p-6 sm:p-7 overflow-y-auto bg-amber-50/20">
                    <!-- Google Play Style Ratings & Reviews Header -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-base font-bold text-gray-900">Ratings and reviews</h4>
                            <span class="text-gray-400 hover:text-gray-600 cursor-pointer" title="Verified customer ratings">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                                </svg>
                            </span>
                        </div>

                        <!-- Real Backend Rating Overview Dashboard -->
                        <div class="flex items-center gap-6 pb-4 border-b border-gray-200/80">
                            <!-- Real Average Rating and Dynamic Stars -->
                            <div class="text-left flex-shrink-0">
                                <div class="text-5xl font-black text-gray-900 tracking-tight leading-none" x-text="getSummary().total_count > 0 ? getSummary().average_rating : '0.0'"></div>
                                <div class="flex items-center gap-0.5 mt-2 text-amber-800">
                                    <template x-for="s in 5" :key="s">
                                        <svg 
                                            class="w-4 h-4" 
                                            :class="getSummary().total_count > 0 && s <= Math.round(parseFloat(getSummary().average_rating)) ? 'fill-amber-800 text-amber-800' : 'text-gray-300 fill-none stroke-current'" 
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                        >
                                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                        </svg>
                                    </template>
                                </div>
                                <div class="text-[11px] text-gray-500 mt-1 font-medium" x-text="getSummary().total_count > 0 ? (getSummary().total_count + ' ' + (getSummary().total_count === 1 ? 'rating' : 'ratings')) : 'No ratings yet'"></div>
                            </div>

                            <!-- Real Backend Rating Distribution Progress Bars (5 down to 1) -->
                            <div class="flex-grow space-y-1.5">
                                <template x-for="s in [5, 4, 3, 2, 1]" :key="s">
                                    <div class="flex items-center gap-2.5 text-xs text-gray-600 font-medium">
                                        <span class="w-2.5 text-right text-gray-700" x-text="s"></span>
                                        <div class="flex-grow h-2.5 bg-gray-200 rounded-full overflow-hidden">
                                            <div 
                                                class="h-full bg-amber-800 rounded-full transition-all duration-300" 
                                                :style="'width: ' + (getSummary().stars && getSummary().stars[s] ? getSummary().stars[s].pct : 0) + '%;'"
                                            ></div>
                                        </div>
                                        <span class="w-5 text-[10px] text-gray-400 text-right" x-text="getSummary().stars && getSummary().stars[s] ? getSummary().stars[s].count : 0"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Community Reviews List (from Backend) -->
                    <div class="space-y-3 flex-grow overflow-y-auto pr-1 max-h-44 mb-5">
                        <template x-if="getReviews().length === 0">
                            <div class="text-center py-6 text-xs text-gray-500">
                                No reviews yet. Be the first to share your experience with this treat!
                            </div>
                        </template>

                        <template x-for="(rev, idx) in getReviews()" :key="idx">
                            <div class="bg-white p-3.5 rounded-xl border border-amber-100/80 shadow-xs space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-xs text-amber-950 flex items-center gap-1.5">
                                        <span x-text="rev.name"></span>
                                        <template x-if="loggedInUserId && rev.user_id === loggedInUserId">
                                            <span class="text-[10px] bg-amber-100 text-amber-900 font-semibold px-1.5 py-0.2 rounded">You</span>
                                        </template>
                                    </span>
                                    <span class="text-[11px] text-gray-400" x-text="rev.date"></span>
                                </div>
                                <div class="flex items-center gap-0.5">
                                    <template x-for="s in 5" :key="s">
                                        <svg 
                                            class="w-3.5 h-3.5"
                                            :class="s <= rev.rating ? 'text-amber-800 fill-amber-800' : 'text-gray-300 fill-none stroke-current'"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                                        </svg>
                                    </template>
                                </div>
                                <p class="text-xs text-gray-700 leading-relaxed" x-text="rev.comment"></p>
                            </div>
                        </template>
                    </div>

                    <!-- Experience Submission Section -->
                    <template x-if="isLoggedIn">
                        <div class="mt-auto pt-4 border-t border-amber-200/60 space-y-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-sm font-bold text-amber-950 block" x-text="getSummary().user_review ? 'Update your review' : 'Review this product'"></span>
                                    <span class="text-[11px] text-gray-500" x-text="'Posting as ' + (loggedInUser || 'Verified Customer')"></span>
                                </div>
                                <!-- Interactive Star Selection -->
                                <div class="flex items-center gap-1">
                                    <template x-for="star in 5" :key="star">
                                        <button 
                                            type="button" 
                                            @click="userRating = star"
                                            @mouseenter="hoverRating = star"
                                            @mouseleave="hoverRating = 0"
                                            class="p-0.5 focus:outline-none transition-transform hover:scale-110 cursor-pointer"
                                            :aria-label="'Rate ' + star + ' stars'"
                                        >
                                            <svg 
                                                class="w-5 h-5 transition-colors"
                                                :class="(hoverRating || userRating) >= star ? 'text-amber-800 fill-amber-800' : 'text-gray-300 fill-none stroke-current'"
                                                viewBox="0 0 24 24" 
                                                stroke-width="1.5"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                                            </svg>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <!-- Experience Submission Textarea -->
                            <textarea 
                                x-model="reviewComment" 
                                rows="2" 
                                placeholder="Share your experience with this flavor..."
                                class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-800 focus:border-amber-800 bg-white transition"
                            ></textarea>

                            <!-- Submit Review Button -->
                            <button 
                                type="button" 
                                @click="submitReview()" 
                                :disabled="!reviewComment.trim() || isSubmitting" 
                                class="w-full bg-amber-800 hover:bg-amber-900 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold py-2.5 px-4 rounded-xl shadow text-xs transition duration-150 cursor-pointer flex items-center justify-center gap-2"
                            >
                                <span x-text="isSubmitting ? 'Saving...' : (getSummary().user_review ? 'Update Review' : 'Submit Review')"></span>
                            </button>

                            <div 
                                x-show="reviewSubmitted" 
                                x-transition 
                                class="p-2.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-xs font-medium text-center"
                            >
                                Thank you! Your review has been saved to the bakery records.
                            </div>
                        </div>
                    </template>

                    <!-- Friendly Sign-in Prompt for Non-Logged-in Visitors -->
                    <template x-if="!isLoggedIn">
                        <div class="mt-auto pt-4 border-t border-amber-200/60">
                            <div class="p-4 rounded-xl bg-amber-50/70 border border-amber-200/80 text-center space-y-2">
                                <div class="text-xs font-bold text-amber-950">Want to rate or review this treat?</div>
                                <p class="text-xs text-stone-600">Please sign in to your account to share your experience with our bakery.</p>
                                <div class="pt-1 flex items-center justify-center gap-3">
                                    <a href="<?= url('/login') ?>" class="inline-flex items-center gap-1.5 bg-amber-800 hover:bg-amber-900 text-white px-4 py-2 rounded-lg font-semibold text-xs transition shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                                        Log In to Review
                                    </a>
                                    <a href="<?= url('/register') ?>" class="text-xs font-semibold text-amber-800 hover:text-amber-950 underline underline-offset-2">
                                        Create Account
                                    </a>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function productCatalog() {
    return {
        activeProduct: null,
        userRating: 5,
        hoverRating: 0,
        loggedInUser: <?= json_encode($sessionUser) ?>,
        loggedInUserId: <?= json_encode($sessionUserId) ?>,
        isLoggedIn: <?= json_encode($isLoggedIn) ?>,
        reviewComment: '',
        reviewSubmitted: false,
        isSubmitting: false,
        productsMap: <?= json_encode($productsMap, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
        reviewsData: <?= json_encode($reviewsData ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,

        getSummary() {
            if (!this.activeProduct) {
                return {
                    total_count: 0,
                    average_rating: '0.0',
                    user_review: null,
                    stars: { 5: { count: 0, pct: 0 }, 4: { count: 0, pct: 0 }, 3: { count: 0, pct: 0 }, 2: { count: 0, pct: 0 }, 1: { count: 0, pct: 0 } },
                    reviews: []
                };
            }
            const data = this.reviewsData[this.activeProduct.id];
            if (data) {
                return data;
            }
            return {
                total_count: 0,
                average_rating: '0.0',
                user_review: null,
                stars: { 5: { count: 0, pct: 0 }, 4: { count: 0, pct: 0 }, 3: { count: 0, pct: 0 }, 2: { count: 0, pct: 0 }, 1: { count: 0, pct: 0 } },
                reviews: []
            };
        },

        getReviews() {
            return this.getSummary().reviews || [];
        },

        openModal(productId) {
            const product = this.productsMap[productId] || null;
            if (!product) return;
            this.activeProduct = product;
            const summary = this.getSummary();
            if (summary && summary.user_review) {
                this.userRating = summary.user_review.rating || 5;
                this.reviewComment = summary.user_review.comment || '';
            } else {
                this.userRating = 5;
                this.reviewComment = '';
            }
            this.hoverRating = 0;
            this.reviewSubmitted = false;
            document.body.classList.add('overflow-hidden');
        },

        closeModal() {
            this.activeProduct = null;
            document.body.classList.remove('overflow-hidden');
        },

        async submitReview() {
            if (!this.isLoggedIn) {
                window.location.href = '<?= url('/login') ?>';
                return;
            }
            if (!this.reviewComment.trim() || !this.activeProduct || this.isSubmitting) return;
            this.isSubmitting = true;

            try {
                const response = await fetch('<?= url('/products/review') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: this.activeProduct.id,
                        rating: this.userRating,
                        comment: this.reviewComment.trim()
                    })
                });

                const res = await response.json();
                if (res.success && res.data) {
                    this.reviewsData[this.activeProduct.id] = res.data;
                    this.reviewSubmitted = true;
                    setTimeout(() => { this.reviewSubmitted = false; }, 4000);
                } else if (res.requires_login) {
                    window.location.href = '<?= url('/login') ?>';
                } else {
                    alert(res.error || 'Failed to submit review.');
                }
            } catch (err) {
                console.error('Review submission failed:', err);
            } finally {
                this.isSubmitting = false;
            }
        }
    };
}
</script>
