<?php require_once dirname(__DIR__) . '/layouts/admin_header.php'; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 border-b border-amber-100 mb-6 gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">All Orders</h1>
        <p class="text-sm text-stone-500 mt-1">Full oversight on purchases, billing, delivery addresses, and fulfillment.</p>
    </div>
    <a href="<?= url('/baker/orders') ?>" class="inline-flex items-center gap-1.5 text-xs text-amber-800 hover:text-amber-900 font-bold border border-amber-300 bg-amber-50 hover:bg-amber-100 px-3 py-2 rounded-lg transition">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/>
        </svg>
        Kitchen Baking Queue
    </a>
</div>

<!-- Status Filters -->
<div class="flex flex-wrap gap-2 mb-6">
    <a href="<?= url('/admin/orders') ?>" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition <?= ($currentFilter === null) ? 'bg-stone-900 text-white' : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200' ?>">
        All Orders (<?= (int)($stats['total_orders'] ?? 0) ?>)
    </a>
    <a href="<?= url('/admin/orders?status=1') ?>" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition <?= ($currentFilter === 1) ? 'bg-amber-600 text-white' : 'bg-white text-amber-800 hover:bg-amber-50 border border-amber-200' ?>">
        Pending (<?= (int)($stats['pending_count'] ?? 0) ?>)
    </a>
    <a href="<?= url('/admin/orders?status=2') ?>" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition <?= ($currentFilter === 2) ? 'bg-orange-600 text-white' : 'bg-white text-orange-800 hover:bg-orange-50 border border-orange-200' ?>">
        Baking (<?= (int)($stats['baking_count'] ?? 0) ?>)
    </a>
    <a href="<?= url('/admin/orders?status=3') ?>" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition <?= ($currentFilter === 3) ? 'bg-blue-600 text-white' : 'bg-white text-blue-800 hover:bg-blue-50 border border-blue-200' ?>">
        Ready for Delivery (<?= (int)($stats['ready_count'] ?? 0) ?>)
    </a>
    <a href="<?= url('/admin/orders?status=4') ?>" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition <?= ($currentFilter === 4) ? 'bg-emerald-600 text-white' : 'bg-white text-emerald-800 hover:bg-emerald-50 border border-emerald-200' ?>">
        Completed (<?= (int)($stats['completed_count'] ?? 0) ?>)
    </a>
</div>

<!-- Orders Table -->
<div class="bg-white rounded-2xl shadow-sm border border-amber-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-stone-600">
            <thead class="bg-amber-50/70 uppercase text-stone-500 border-b border-amber-100">
                <tr>
                    <th class="px-5 py-3 font-semibold">Order</th>
                    <th class="px-5 py-3 font-semibold">Customer</th>
                    <th class="px-5 py-3 font-semibold">Items</th>
                    <th class="px-5 py-3 font-semibold">Address / Notes</th>
                    <th class="px-5 py-3 font-semibold">Total</th>
                    <th class="px-5 py-3 font-semibold">Status</th>
                    <th class="px-5 py-3 font-semibold text-right">Update</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-amber-50">
                <?php if (empty($orders)): ?>
                    <tr><td colspan="7" class="px-5 py-10 text-center text-stone-400">No orders found matching this filter.</td></tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <?php $st = (int)($order['status'] ?? 1); ?>
                        <tr class="hover:bg-amber-50/40 transition">
                            <td class="px-5 py-3">
                                <div class="font-extrabold text-stone-900 text-sm">#<?= e($order['order_number'] ?: $order['id']) ?></div>
                                <span class="text-[10px] text-stone-400"><?= !empty($order['created_at']) ? date('M j, Y h:i A', strtotime($order['created_at'])) : '-' ?></span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="font-bold text-stone-900"><?= e($order['customer_name'] ?? 'Guest') ?></div>
                                <div class="text-[11px] text-stone-400"><?= e($order['customer_email'] ?? '') ?></div>
                                <div class="text-[11px] text-stone-500"><?= e($order['phone_number'] ?? 'N/A') ?></div>
                            </td>
                            <td class="px-5 py-3">
                                <div class="space-y-1">
                                    <?php if (!empty($order['items'])): ?>
                                        <?php foreach ($order['items'] as $item): ?>
                                            <div class="text-[11px] text-stone-700">
                                                <span class="font-bold text-amber-900"><?= (int)$item['quantity'] ?>x</span>
                                                <?= e($item['product_name'] ?? 'Item') ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-stone-400">No item details</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-5 py-3 max-w-xs">
                                <div class="text-[11px] text-stone-600"><?= e($order['street_address'] ?? '') ?> <?= e($order['barangay'] ?? '') ?>, <?= e($order['city'] ?? '') ?></div>
                                <?php if (!empty($order['customer_note'])): ?>
                                    <div class="mt-1 text-[10px] text-amber-800 bg-amber-50 p-1 rounded border border-amber-100">Note: <?= e($order['customer_note']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-3 font-mono font-black text-stone-900 text-sm">
                                ₱<?= number_format((float)($order['total_amount'] ?? 0), 2) ?>
                            </td>
                            <td class="px-5 py-3">
                                <?php $badge = match($st) {
                                    1 => 'bg-amber-100 text-amber-800 border-amber-200',
                                    2 => 'bg-orange-100 text-orange-800 border-orange-200',
                                    3 => 'bg-blue-100 text-blue-800 border-blue-200',
                                    4 => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    default => 'bg-stone-100 text-stone-700 border-stone-200'
                                }; ?>
                                <span data-order-badge class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold border uppercase tracking-wider <?= $badge ?>">
                                    <?= e($order['status_name'] ?? 'Pending') ?>
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <form action="<?= url('/admin/orders/status') ?>" method="POST" data-ajax="true" data-alert-target="#portal-ajax-alert" class="inline-block admin-order-status-form">
                                    <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                                    <select name="status_id" onchange="$(this.form).trigger('submit')"
                                            class="text-xs bg-stone-50 border border-stone-300 rounded-lg px-2 py-1 focus:outline-none focus:ring-1 focus:ring-amber-700">
                                        <option value="" disabled selected>Change...</option>
                                        <?php foreach ($statuses as $stat): ?>
                                            <option value="<?= (int)$stat['id'] ?>" <?= ($st === (int)$stat['id']) ? 'disabled' : '' ?>>
                                                &rarr; <?= e($stat['status']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    const badgeStyles = {
        1: 'bg-amber-100 text-amber-800 border-amber-200',
        2: 'bg-orange-100 text-orange-800 border-orange-200',
        3: 'bg-blue-100 text-blue-800 border-blue-200',
        4: 'bg-emerald-100 text-emerald-800 border-emerald-200'
    };
    $(document).on('ajax:success', '.admin-order-status-form', function(e, res) {
        if (!res || !res.status_id) return;
        const $row = $(this).closest('tr');
        const $badge = $row.find('[data-order-badge]');
        $badge
            .removeClass('bg-amber-100 text-amber-800 border-amber-200 bg-orange-100 text-orange-800 border-orange-200 bg-blue-100 text-blue-800 border-blue-200 bg-emerald-100 text-emerald-800 border-emerald-200 bg-stone-100 text-stone-700 border-stone-200')
            .addClass(badgeStyles[res.status_id] || 'bg-stone-100 text-stone-700 border-stone-200')
            .text(res.status_name);
        $(this).find('select option').prop('disabled', false);
        $(this).find('select option[value="' + res.status_id + '"]').prop('disabled', true);
    });
});
</script>

<?php require_once dirname(__DIR__) . '/layouts/admin_footer.php'; ?>
