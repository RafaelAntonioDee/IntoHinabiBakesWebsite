<?php require_once dirname(__DIR__) . '/layouts/admin_header.php'; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 border-b border-amber-100 mb-6 gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Executive Dashboard</h1>
        <p class="text-sm text-stone-500 mt-1">System metrics, revenue, staff and operations at a glance.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="<?= url('/admin/users') ?>" class="inline-flex items-center gap-1.5 bg-stone-900 hover:bg-stone-800 text-white font-bold py-2 px-4 rounded-lg text-xs shadow transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
            Manage Users
        </a>
        <a href="<?= url('/admin/products') ?>" class="inline-flex items-center gap-1.5 bg-amber-800 hover:bg-amber-900 text-white font-bold py-2 px-4 rounded-lg text-xs shadow transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"/></svg>
            Catalog & Prices
        </a>
    </div>
</div>

<!-- KPI Stat Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <!-- Total Revenue -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-amber-100 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <div class="text-[11px] uppercase tracking-wider text-stone-500 font-semibold">Total Revenue</div>
            <div class="text-2xl font-black text-stone-900">$<?= number_format((float)($orderStats['total_revenue'] ?? 0), 2) ?></div>
            <div class="text-xs text-emerald-600 font-medium mt-0.5">Across all paid orders</div>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-amber-100 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
            </svg>
        </div>
        <div>
            <div class="text-[11px] uppercase tracking-wider text-stone-500 font-semibold">Total Orders</div>
            <div class="text-2xl font-black text-stone-900"><?= (int)($orderStats['total_orders'] ?? 0) ?></div>
            <div class="text-xs text-blue-600 font-medium mt-0.5">
                <?= (int)($orderStats['pending_count'] ?? 0) ?> pending &bull; <?= (int)($orderStats['baking_count'] ?? 0) ?> baking
            </div>
        </div>
    </div>

    <!-- Registered Accounts -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-amber-100 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
            </svg>
        </div>
        <div>
            <div class="text-[11px] uppercase tracking-wider text-stone-500 font-semibold">Registered Accounts</div>
            <div class="text-2xl font-black text-stone-900"><?= (int)($userStats['total_users'] ?? 0) ?></div>
            <div class="text-xs text-amber-700 font-medium mt-0.5">
                <?= (int)($userStats['total_bakers'] ?? 0) ?> Bakers &bull; <?= (int)($userStats['total_admins'] ?? 0) ?> Admins
            </div>
        </div>
    </div>

    <!-- Bakery Items -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-amber-100 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-orange-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-orange-700" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"/>
            </svg>
        </div>
        <div>
            <div class="text-[11px] uppercase tracking-wider text-stone-500 font-semibold">Bakery Items</div>
            <div class="text-2xl font-black text-stone-900"><?= (int)$totalProducts ?></div>
            <a href="<?= url('/admin/inquiries') ?>" class="text-xs text-amber-800 font-semibold hover:underline mt-0.5 block">
                <?= (int)$inquiriesCount ?> Contact Inquiries &rarr;
            </a>
        </div>
    </div>
</div>

<!-- Two-Col Table Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Recent Orders -->
    <div class="bg-white rounded-2xl shadow-sm border border-amber-100 overflow-hidden">
        <div class="p-5 border-b border-amber-50 flex justify-between items-center">
            <div>
                <h2 class="font-bold text-stone-900 text-base">Recent Orders</h2>
                <p class="text-xs text-stone-500">Live order activity across the store</p>
            </div>
            <a href="<?= url('/admin/orders') ?>" class="text-xs text-amber-800 hover:text-amber-900 font-bold hover:underline">
                View All &rarr;
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-600">
                <thead class="bg-amber-50/60 uppercase text-stone-500 border-b border-amber-100">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Order</th>
                        <th class="px-5 py-3 font-semibold">Customer</th>
                        <th class="px-5 py-3 font-semibold">Total</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-amber-50">
                    <?php if (empty($recentOrders)): ?>
                        <tr><td colspan="4" class="px-5 py-6 text-center text-stone-400">No orders yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentOrders as $order): ?>
                            <?php $st = (int)($order['status'] ?? 1); ?>
                            <tr class="hover:bg-amber-50/40 transition">
                                <td class="px-5 py-3 font-bold text-stone-900">#<?= e($order['order_number'] ?: $order['id']) ?></td>
                                <td class="px-5 py-3 font-medium"><?= e($order['customer_name'] ?? 'Guest') ?></td>
                                <td class="px-5 py-3 font-mono font-bold text-stone-900">$<?= number_format((float)($order['total_amount'] ?? 0), 2) ?></td>
                                <td class="px-5 py-3">
                                    <?php $badge = match($st) {
                                        1 => 'bg-amber-100 text-amber-800',
                                        2 => 'bg-orange-100 text-orange-800',
                                        3 => 'bg-blue-100 text-blue-800',
                                        4 => 'bg-emerald-100 text-emerald-800',
                                        default => 'bg-stone-100 text-stone-700'
                                    }; ?>
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold <?= $badge ?>"><?= e($order['status_name'] ?? 'Pending') ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="bg-white rounded-2xl shadow-sm border border-amber-100 overflow-hidden">
        <div class="p-5 border-b border-amber-50 flex justify-between items-center">
            <div>
                <h2 class="font-bold text-stone-900 text-base">User Directory Preview</h2>
                <p class="text-xs text-stone-500">Recently registered customers and staff</p>
            </div>
            <a href="<?= url('/admin/users') ?>" class="text-xs text-amber-800 hover:text-amber-900 font-bold hover:underline">
                Manage All &rarr;
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-600">
                <thead class="bg-amber-50/60 uppercase text-stone-500 border-b border-amber-100">
                    <tr>
                        <th class="px-5 py-3 font-semibold">User</th>
                        <th class="px-5 py-3 font-semibold">Email</th>
                        <th class="px-5 py-3 font-semibold">Role</th>
                        <th class="px-5 py-3 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-amber-50">
                    <?php if (empty($recentUsers)): ?>
                        <tr><td colspan="4" class="px-5 py-6 text-center text-stone-400">No users found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentUsers as $usr): ?>
                            <?php
                                $rId = (int)($usr['role_id'] ?? 1);
                                $rolePill = match($rId) {
                                    3 => 'bg-amber-100 text-amber-900 font-bold',
                                    2 => 'bg-orange-100 text-orange-800 font-bold',
                                    default => 'bg-stone-100 text-stone-700'
                                };
                            ?>
                            <tr class="hover:bg-amber-50/40 transition">
                                <td class="px-5 py-3 font-bold text-stone-900"><?= e($usr['full_name']) ?></td>
                                <td class="px-5 py-3 font-mono text-stone-500 text-[11px]"><?= e($usr['email']) ?></td>
                                <td class="px-5 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] uppercase tracking-wider <?= $rolePill ?>">
                                        <?= e($usr['role_name'] ?? 'Customer') ?>
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <a href="<?= url('/admin/users?search=' . urlencode($usr['email'])) ?>" class="text-amber-800 hover:text-amber-900 font-semibold hover:underline">
                                        Edit Role &rarr;
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/layouts/admin_footer.php'; ?>
