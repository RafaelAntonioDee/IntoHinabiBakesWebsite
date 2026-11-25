<?php require_once dirname(__DIR__) . '/layouts/baker_header.php'; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 border-b border-amber-100 mb-6 gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Baking Queue</h1>
        <p class="text-sm text-stone-500 mt-1">Track batches from preparation into the oven and through to packing.</p>
    </div>
    <a href="<?= url('/baker') ?>" class="inline-flex items-center gap-1 text-xs text-amber-800 hover:text-amber-900 font-bold border border-amber-300 bg-amber-50 hover:bg-amber-100 px-3 py-2 rounded-lg transition">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Kitchen Dashboard
    </a>
</div>

<!-- Status Filter Tabs -->
<div class="flex flex-wrap gap-2 mb-6">
    <a href="<?= url('/baker/orders') ?>" class="px-4 py-2 rounded-xl text-xs font-bold transition <?= ($currentFilter === null) ? 'bg-amber-900 text-white shadow' : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200' ?>">
        All Orders (<?= (int)($stats['total_orders'] ?? 0) ?>)
    </a>
    <a href="<?= url('/baker/orders?status=1') ?>" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 <?= ($currentFilter === 1) ? 'bg-amber-600 text-white shadow' : 'bg-white text-amber-800 hover:bg-amber-50 border border-amber-200' ?>">
        Pending
        <span class="px-1.5 rounded-full text-[10px] <?= ($currentFilter === 1) ? 'bg-amber-800 text-white' : 'bg-amber-100 text-amber-900' ?>">
            <?= (int)($stats['pending_count'] ?? 0) ?>
        </span>
    </a>
    <a href="<?= url('/baker/orders?status=2') ?>" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 <?= ($currentFilter === 2) ? 'bg-orange-600 text-white shadow' : 'bg-white text-orange-800 hover:bg-orange-50 border border-orange-200' ?>">
        Baking / In Oven
        <span class="px-1.5 rounded-full text-[10px] <?= ($currentFilter === 2) ? 'bg-orange-800 text-white' : 'bg-orange-100 text-orange-900' ?>">
            <?= (int)($stats['baking_count'] ?? 0) ?>
        </span>
    </a>
    <a href="<?= url('/baker/orders?status=3') ?>" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 <?= ($currentFilter === 3) ? 'bg-blue-600 text-white shadow' : 'bg-white text-blue-800 hover:bg-blue-50 border border-blue-200' ?>">
        Ready for Delivery
        <span class="px-1.5 rounded-full text-[10px] <?= ($currentFilter === 3) ? 'bg-blue-800 text-white' : 'bg-blue-100 text-blue-900' ?>">
            <?= (int)($stats['ready_count'] ?? 0) ?>
        </span>
    </a>
    <a href="<?= url('/baker/orders?status=4') ?>" class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 <?= ($currentFilter === 4) ? 'bg-emerald-700 text-white shadow' : 'bg-white text-emerald-800 hover:bg-emerald-50 border border-emerald-200' ?>">
        Completed
        <span class="px-1.5 rounded-full text-[10px] <?= ($currentFilter === 4) ? 'bg-emerald-900 text-white' : 'bg-emerald-100 text-emerald-900' ?>">
            <?= (int)($stats['completed_count'] ?? 0) ?>
        </span>
    </a>
</div>

<?php if (empty($orders)): ?>
    <div class="bg-white rounded-2xl border border-amber-100 p-12 text-center">
        <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-amber-100 flex items-center justify-center">
            <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-stone-800">No orders in this view</h3>
        <p class="text-sm text-stone-500 mt-1">No orders match the selected status filter.</p>
        <a href="<?= url('/baker/orders') ?>" class="inline-block mt-4 text-xs font-bold text-amber-800 bg-amber-100 hover:bg-amber-200 px-4 py-2 rounded-lg transition">
            View All Orders
        </a>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php foreach ($orders as $order): ?>
            <?php
                $st = (int)($order['status'] ?? 1);
                $cardAccent = match($st) {
                    1 => 'border-amber-300',
                    2 => 'border-orange-400',
                    3 => 'border-blue-300',
                    4 => 'border-emerald-200',
                    default => 'border-stone-200'
                };
            ?>
            <div class="bg-white rounded-2xl shadow-sm border <?= $cardAccent ?> p-6 flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <!-- Order Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span class="text-xs font-extrabold uppercase tracking-wider text-stone-500">Order #<?= e($order['order_number'] ?: $order['id']) ?></span>
                            <h3 class="text-lg font-bold text-stone-900 mt-0.5"><?= e($order['customer_name'] ?? 'Guest Customer') ?></h3>
                            <p class="text-xs text-stone-500 flex items-center gap-1 mt-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                                <?= e($order['phone_number'] ?? 'No phone') ?>
                                &bull;
                                <?= e($order['barangay'] ?? '') ?><?= !empty($order['city']) ? ', ' . e($order['city']) : '' ?>
                            </p>
                        </div>
                        <div class="text-right">
                            <?php $badgeStyle = match($st) {
                                1 => 'bg-amber-100 text-amber-800 border-amber-300',
                                2 => 'bg-orange-100 text-orange-800 border-orange-300',
                                3 => 'bg-blue-100 text-blue-800 border-blue-300',
                                4 => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                default => 'bg-stone-100 text-stone-800 border-stone-200'
                            }; ?>
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold border <?= $badgeStyle ?>">
                                <?= e($order['status_name'] ?? 'Pending') ?>
                            </span>
                            <span class="block text-[10px] text-stone-400 mt-1">
                                <?= !empty($order['created_at']) ? date('M j, Y h:i A', strtotime($order['created_at'])) : '' ?>
                            </span>
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="mb-4">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-stone-500 mb-2">Items in this Order:</h4>
                        <div class="space-y-2 bg-amber-50/50 rounded-xl p-3 border border-amber-100">
                            <?php if (!empty($order['items'])): ?>
                                <?php foreach ($order['items'] as $item): ?>
                                    <div class="flex justify-between items-center text-sm">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-amber-900 bg-amber-100 px-2 py-0.5 rounded text-xs"><?= (int)$item['quantity'] ?>x</span>
                                            <span class="font-medium text-stone-900"><?= e($item['product_name'] ?? 'Custom Item') ?></span>
                                            <span class="text-xs text-stone-400">(<?= e($item['category_name'] ?? 'Bakery') ?>)</span>
                                        </div>
                                        <span class="text-xs text-stone-500 font-mono">$<?= number_format((float)($item['price'] ?? 0), 2) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-xs text-stone-400 italic">No specific items listed.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Customer Note -->
                    <?php if (!empty($order['customer_note'])): ?>
                        <div class="mb-4 p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 flex items-start gap-2">
                            <svg class="w-3.5 h-3.5 mt-0.5 text-amber-700 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                            </svg>
                            <div>
                                <strong class="font-semibold">Special Instructions:</strong>
                                <p class="mt-0.5 text-stone-700"><?= e($order['customer_note']) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Workflow Progression -->
                <div class="pt-4 border-t border-amber-100 mt-2">
                    <form action="<?= url('/baker/orders/status') ?>" method="POST" class="flex flex-wrap items-center justify-between gap-2">
                        <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                        <div class="flex-grow">
                            <?php if ($st === 1): ?>
                                <input type="hidden" name="status_id" value="2">
                                <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs shadow transition flex items-center justify-center gap-1.5 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048A8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/></svg>
                                    Put In Oven / Start Baking
                                </button>
                            <?php elseif ($st === 2): ?>
                                <input type="hidden" name="status_id" value="3">
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs shadow transition flex items-center justify-center gap-1.5 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5"/></svg>
                                    Done &rarr; Mark Ready for Delivery
                                </button>
                            <?php elseif ($st === 3): ?>
                                <input type="hidden" name="status_id" value="4">
                                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs shadow transition flex items-center justify-center gap-1.5 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Dispatched / Mark Completed
                                </button>
                            <?php else: ?>
                                <div class="text-xs text-emerald-700 font-semibold flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Order Completed and Dispatched
                                </div>
                            <?php endif; ?>
                        </div>
                        <!-- Manual Override -->
                        <select onchange="this.form.submit()" name="status_id"
                                class="text-xs bg-stone-100 border border-stone-300 rounded-lg px-2 py-2 text-stone-700 focus:outline-none focus:ring-1 focus:ring-amber-700">
                            <option value="" disabled selected>Change Status...</option>
                            <?php foreach ($statuses as $stat): ?>
                                <option value="<?= (int)$stat['id'] ?>" <?= ($st === (int)$stat['id']) ? 'disabled' : '' ?>>
                                    &rarr; <?= e($stat['status']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once dirname(__DIR__) . '/layouts/baker_footer.php'; ?>
