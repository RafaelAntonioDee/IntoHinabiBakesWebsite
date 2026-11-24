<?php require_once dirname(__DIR__) . '/layouts/baker_header.php'; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 border-b border-amber-100 mb-6 gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Inventory &amp; Availability</h1>
        <p class="text-sm text-stone-500 mt-1">Toggle cookie flavors and cakes as In Stock or Sold Out. Changes reflect immediately on the customer menu.</p>
    </div>
    <a href="<?= url('/baker') ?>" class="inline-flex items-center gap-1 text-xs text-amber-800 hover:text-amber-900 font-bold border border-amber-300 bg-amber-50 hover:bg-amber-100 px-3 py-2 rounded-lg transition">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Kitchen Dashboard
    </a>
</div>

<!-- Filters Bar -->
<div class="bg-white rounded-2xl p-4 shadow-sm border border-amber-100 mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
    <div class="flex flex-wrap gap-2 w-full md:w-auto">
        <a href="<?= url('/baker/inventory') ?>" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition <?= ($selectedCategory === null) ? 'bg-amber-900 text-white' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' ?>">All Items</a>
        <?php foreach ($categories as $cat): ?>
            <a href="<?= url('/baker/inventory?category=' . $cat['id']) ?>" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition <?= ($selectedCategory == $cat['id']) ? 'bg-amber-900 text-white' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' ?>">
                <?= e($cat['category_name']) ?>
            </a>
        <?php endforeach; ?>
    </div>
    <form action="<?= url('/baker/inventory') ?>" method="GET" class="flex items-center gap-2 w-full md:w-auto">
        <?php if ($selectedCategory): ?>
            <input type="hidden" name="category" value="<?= (int)$selectedCategory ?>">
        <?php endif; ?>
        <input type="text" name="search" value="<?= e($searchQuery) ?>" placeholder="Search flavor..."
               class="px-3 py-1.5 text-xs border border-stone-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-amber-800 bg-stone-50 w-full md:w-48">
        <button type="submit" class="bg-amber-800 hover:bg-amber-900 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition">Filter</button>
        <?php if (!empty($searchQuery)): ?>
            <a href="<?= url('/baker/inventory') ?>" class="text-xs text-stone-400 hover:text-stone-600 px-1">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Product Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (empty($products)): ?>
        <div class="col-span-full bg-white p-12 text-center rounded-2xl border border-amber-100">
            <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-amber-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"/>
                </svg>
            </div>
            <p class="text-sm font-semibold text-stone-600">No bakery items found matching criteria.</p>
        </div>
    <?php else: ?>
        <?php foreach ($products as $item): ?>
            <?php $isAvailable = !empty($item['availability']) && !empty($item['is_active']); ?>
            <div class="bg-white rounded-2xl shadow-sm border <?= $isAvailable ? 'border-amber-100' : 'border-rose-300' ?> overflow-hidden flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <!-- Product Image -->
                    <div class="relative h-44 overflow-hidden bg-stone-100">
                        <img src="<?= e($item['image_url']) ?>" alt="<?= e($item['name']) ?>" class="w-full h-full object-cover">
                        <div class="absolute top-2.5 right-2.5">
                            <?php if ($isAvailable): ?>
                                <span class="bg-emerald-600/90 backdrop-blur-sm text-white font-bold text-[10px] uppercase tracking-wider px-2.5 py-1 rounded-full shadow">In Stock</span>
                            <?php else: ?>
                                <span class="bg-rose-600/90 backdrop-blur-sm text-white font-bold text-[10px] uppercase tracking-wider px-2.5 py-1 rounded-full shadow">Sold Out</span>
                            <?php endif; ?>
                        </div>
                        <div class="absolute bottom-2.5 left-2.5">
                            <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded shadow-sm <?= ($item['category_id'] == 2) ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-900' ?>">
                                <?= e($item['category_name'] ?? 'Bakery') ?>
                            </span>
                        </div>
                    </div>
                    <!-- Details -->
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-stone-900 text-base leading-snug"><?= e($item['name']) ?></h3>
                            <span class="font-extrabold text-amber-900 text-sm ml-2 font-mono">$<?= number_format((float)($item['price'] ?? 0), 2) ?></span>
                        </div>
                        <p class="text-xs text-stone-500 line-clamp-2 mb-3"><?= e($item['description'] ?? '') ?></p>
                    </div>
                </div>
                <!-- Toggle Action -->
                <div class="p-5 pt-0">
                    <form action="<?= url('/baker/inventory/toggle') ?>" method="POST">
                        <input type="hidden" name="product_id" value="<?= (int)$item['id'] ?>">
                        <?php if ($isAvailable): ?>
                            <button type="submit" class="w-full bg-white hover:bg-rose-50 text-rose-700 hover:text-rose-800 border border-rose-300 font-bold py-2 px-4 rounded-xl text-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                Mark as Sold Out
                            </button>
                        <?php else: ?>
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-xl text-xs shadow transition flex items-center justify-center gap-1.5 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                Mark as In Stock
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/layouts/baker_footer.php'; ?>
