<?php require_once dirname(__DIR__) . '/layouts/baker_header.php'; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 border-b border-amber-100 mb-6 gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">
            Kitchen Overview
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 ml-2 align-middle">Live</span>
        </h1>
        <p class="text-sm text-stone-500 mt-1">Monitor live oven batches, incoming customer orders, and bakery stock levels.</p>
    </div>
    <a href="<?= url('/baker/orders') ?>" class="inline-flex items-center gap-1.5 bg-amber-800 hover:bg-amber-900 text-white font-bold py-2 px-4 rounded-lg text-sm shadow transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
        </svg>
        Open Baking Queue
    </a>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <!-- Pending -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-amber-100 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <div class="text-[11px] uppercase tracking-wider text-stone-500 font-semibold">Pending Orders</div>
            <div class="text-2xl font-black text-stone-900"><?= (int)($stats['pending_count'] ?? 0) ?></div>
            <div class="text-xs text-amber-600 font-medium mt-0.5">Awaiting preparation</div>
        </div>
    </div>

    <!-- In Oven -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-amber-100 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-orange-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-orange-700" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z"/>
            </svg>
        </div>
        <div>
            <div class="text-[11px] uppercase tracking-wider text-stone-500 font-semibold">In The Oven</div>
            <div class="text-2xl font-black text-orange-600"><?= (int)($stats['baking_count'] ?? 0) ?></div>
            <div class="text-xs text-stone-500 font-medium mt-0.5">Currently baking</div>
        </div>
    </div>

    <!-- Ready -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-amber-100 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
            </svg>
        </div>
        <div>
            <div class="text-[11px] uppercase tracking-wider text-stone-500 font-semibold">Ready for Delivery</div>
            <div class="text-2xl font-black text-emerald-600"><?= (int)($stats['ready_count'] ?? 0) ?></div>
            <div class="text-xs text-stone-500 font-medium mt-0.5">Packed &amp; ready</div>
        </div>
    </div>

    <!-- Sold Out -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-amber-100 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl <?= ($outOfStockCount > 0) ? 'bg-rose-100' : 'bg-stone-100' ?> flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 <?= ($outOfStockCount > 0) ? 'text-rose-700' : 'text-stone-500' ?>" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"/>
            </svg>
        </div>
        <div>
            <div class="text-[11px] uppercase tracking-wider text-stone-500 font-semibold">Sold Out Items</div>
            <div class="text-2xl font-black <?= ($outOfStockCount > 0) ? 'text-rose-600' : 'text-stone-900' ?>">
                <?= (int)$outOfStockCount ?> <span class="text-xs text-stone-400 font-normal">/ <?= (int)$totalProducts ?></span>
            </div>
            <a href="<?= url('/baker/inventory') ?>" class="text-xs text-amber-700 hover:underline font-semibold mt-0.5 block">Manage availability &rarr;</a>
        </div>
    </div>
</div>

<!-- Two-Col Layout -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Left 2 Cols -->
    <div class="lg:col-span-2 space-y-8">

        <!-- Batch Production List -->
        <div class="bg-white rounded-2xl shadow-sm border border-amber-100 overflow-hidden">
            <div class="p-5 border-b border-amber-50 bg-amber-50/40 flex justify-between items-center">
                <div>
                    <h2 class="font-bold text-stone-900 text-base">Kitchen Batch Production List</h2>
                    <p class="text-xs text-stone-500 mt-0.5">Aggregated quantities across Pending &amp; Baking orders.</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 bg-amber-200 text-amber-900 rounded-lg">
                    <?= count($bakingQueue) ?> Items Queued
                </span>
            </div>
            <div class="p-5">
                <?php if (empty($bakingQueue)): ?>
                    <div class="text-center py-8 text-stone-400">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-emerald-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium">All active batches are baked! No items currently in the queue.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <?php foreach ($bakingQueue as $batch): ?>
                            <div class="flex items-center justify-between p-3.5 rounded-xl border border-amber-100 bg-amber-50/40 hover:bg-amber-50/70 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-sm text-stone-900"><?= e($batch['product_name']) ?></h4>
                                        <p class="text-xs text-stone-500"><?= e($batch['category_name'] ?? 'Bakery') ?> &bull; <?= (int)$batch['order_count'] ?> orders</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xl font-extrabold text-amber-900 bg-amber-100 px-3 py-1 rounded-lg"><?= (int)$batch['total_needed'] ?></span>
                                    <span class="block text-[10px] text-stone-500 uppercase tracking-wider font-semibold mt-0.5">to bake</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-2xl shadow-sm border border-amber-100 overflow-hidden">
            <div class="p-5 border-b border-amber-50 flex justify-between items-center">
                <div>
                    <h2 class="font-bold text-stone-900 text-base">Recent Orders</h2>
                    <p class="text-xs text-stone-500">Live order queue requiring kitchen attention</p>
                </div>
                <a href="<?= url('/baker/orders') ?>" class="text-xs text-amber-800 hover:text-amber-900 font-bold hover:underline">View All &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-stone-600">
                    <thead class="bg-amber-50/60 text-xs uppercase text-stone-500 border-b border-amber-100">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Order</th>
                            <th class="px-5 py-3 font-semibold">Customer</th>
                            <th class="px-5 py-3 font-semibold">Items</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 font-semibold text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-amber-50">
                        <?php if (empty($activeOrders)): ?>
                            <tr><td colspan="5" class="px-5 py-8 text-center text-stone-400">No orders found in database yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($activeOrders as $ord): ?>
                                <?php $st = (int)($ord['status'] ?? 1); ?>
                                <tr class="hover:bg-amber-50/40 transition">
                                    <td class="px-5 py-3 font-bold text-stone-900">#<?= e($ord['order_number'] ?: $ord['id']) ?></td>
                                    <td class="px-5 py-3">
                                        <div class="font-medium text-stone-900"><?= e($ord['customer_name'] ?? 'Guest') ?></div>
                                        <div class="text-xs text-stone-400"><?= e($ord['city'] ?? '') ?></div>
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="text-xs flex flex-wrap gap-1">
                                            <?php if (!empty($ord['items'])): ?>
                                                <?php foreach ($ord['items'] as $it): ?>
                                                    <span class="inline-block bg-amber-100 rounded px-1.5 py-0.5 text-amber-900 font-medium">
                                                        <?= (int)$it['quantity'] ?>x <?= e($it['product_name'] ?? 'Item') ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-stone-400">No item details</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3">
                                        <?php $bc = match($st) {
                                            1 => 'bg-amber-100 text-amber-800',
                                            2 => 'bg-orange-100 text-orange-800',
                                            3 => 'bg-blue-100 text-blue-800',
                                            4 => 'bg-emerald-100 text-emerald-800',
                                            default => 'bg-stone-100 text-stone-800'
                                        }; ?>
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold <?= $bc ?>"><?= e($ord['status_name'] ?? 'Pending') ?></span>
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <a href="<?= url('/baker/orders?status=' . $ord['status']) ?>" class="text-amber-800 hover:text-amber-900 font-semibold text-xs hover:underline">Manage &rarr;</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div><!-- /left -->

    <!-- Right Col: Quick Actions + SOP -->
    <div class="space-y-6">
        <div class="bg-gradient-to-br from-amber-900 to-amber-950 text-white rounded-2xl p-6 shadow-md">
            <h3 class="text-base font-bold mb-1">Into Hinabi Kitchen</h3>
            <p class="text-xs text-amber-300 mb-4">Quick actions for the active shift</p>
            <div class="space-y-2">
                <a href="<?= url('/baker/inventory') ?>" class="flex items-center gap-2 w-full bg-amber-800/70 hover:bg-amber-800 text-white font-semibold py-2.5 px-4 rounded-xl text-xs transition border border-amber-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                    Check &amp; Toggle Flavors Stock
                </a>
                <a href="<?= url('/baker/orders?status=1') ?>" class="flex items-center gap-2 w-full bg-amber-700/60 hover:bg-amber-700 text-white font-semibold py-2.5 px-4 rounded-xl text-xs transition border border-amber-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Pending Orders (<?= (int)($stats['pending_count'] ?? 0) ?>)
                </a>
                <a href="<?= url('/baker/orders?status=2') ?>" class="flex items-center gap-2 w-full bg-orange-700/60 hover:bg-orange-700 text-white font-semibold py-2.5 px-4 rounded-xl text-xs transition border border-orange-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048A8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/></svg>
                    In-Oven Orders (<?= (int)($stats['baking_count'] ?? 0) ?>)
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-amber-100 p-5">
            <h3 class="font-bold text-stone-900 text-sm mb-3">Kitchen SOP Reminder</h3>
            <ul class="text-xs text-stone-600 space-y-2.5">
                <li class="flex items-start gap-2">
                    <span class="w-1.5 h-1.5 mt-1.5 rounded-full bg-amber-700 flex-shrink-0 inline-block"></span>
                    <span>Preheat ovens to 350&deg;F (175&deg;C) before batching cookies.</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-1.5 h-1.5 mt-1.5 rounded-full bg-amber-700 flex-shrink-0 inline-block"></span>
                    <span>Mark orders as <strong>Baking</strong> as soon as trays are slotted.</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-1.5 h-1.5 mt-1.5 rounded-full bg-amber-700 flex-shrink-0 inline-block"></span>
                    <span>Once cooled and packaged, immediately mark <strong>Ready for Delivery</strong>.</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-1.5 h-1.5 mt-1.5 rounded-full bg-amber-700 flex-shrink-0 inline-block"></span>
                    <span>If a flavor runs out, toggle it to <strong>Sold Out</strong> in Inventory immediately.</span>
                </li>
            </ul>
        </div>
    </div><!-- /right -->

</div>

<?php require_once dirname(__DIR__) . '/layouts/baker_footer.php'; ?>
