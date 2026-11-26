<main class="max-w-2xl mx-auto px-4 py-12 flex-grow w-full">
    <div class="bg-white p-8 rounded-lg shadow-md border border-amber-100">
        <div class="flex items-center space-x-4 mb-6">
            <div class="w-16 h-16 bg-amber-800 text-white text-2xl font-bold flex items-center justify-center rounded-full shadow-inner">
                <?= strtoupper(substr($user['full_name'] ?? 'U', 0, 1)) ?>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-amber-900"><?= e($user['full_name']) ?></h1>
                <p class="text-gray-500 text-sm">Account Dashboard</p>
            </div>
        </div>

        <div class="space-y-4 border-t border-gray-100 pt-6">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase">Full Name</label>
                <p class="text-lg font-medium text-gray-800"><?= e($user['full_name']) ?></p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase">Email Address</label>
                <p class="text-lg font-medium text-gray-800"><?= e($user['email']) ?></p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase">Member Since</label>
                <p class="text-lg font-medium text-gray-800">
                    <?= !empty($user['created_at']) ? date("F j, Y", strtotime($user['created_at'])) : 'Recently' ?>
                </p>
            </div>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-semibold text-gray-500 uppercase">Delivery Address</label>
                    <?php if (!empty($address) && (!empty($address['street_address']) || !empty($address['city']))): ?>
                        <button 
                            type="button" 
                            onclick="openDeliveryAddressModal()" 
                            class="text-xs font-semibold text-amber-900 hover:text-amber-950 underline cursor-pointer"
                        >
                            Change Address
                        </button>
                    <?php endif; ?>
                </div>

                <?php if (!empty($address) && (!empty($address['street_address']) || !empty($address['city']))): ?>
                    <div class="mt-1 p-3.5 bg-amber-50/80 rounded-xl border border-amber-200 text-sm">
                        <div class="font-semibold text-amber-950 flex items-center gap-1.5">
                            <span class="text-amber-800">📍</span>
                            <span><?= e($address['street_address'] ?? '') ?><?= !empty($address['barangay']) ? ', ' . e($address['barangay']) : '' ?>, <?= e($address['city'] ?? '') ?></span>
                        </div>
                        <?php if (!empty($address['delivery_notes'])): ?>
                            <p class="text-xs text-gray-600 mt-1 italic">Note: <?= e($address['delivery_notes']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($address['latitude']) && !empty($address['longitude'])): ?>
                            <p class="text-[11px] font-mono text-amber-800/80 mt-1">Coordinates: <?= e($address['latitude']) ?>, <?= e($address['longitude']) ?></p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="mt-1 p-4 bg-amber-50/40 rounded-xl border border-dashed border-amber-200 text-center">
                        <p class="text-sm text-gray-500 italic mb-2.5">No delivery address saved yet.</p>
                        <button 
                            type="button" 
                            onclick="openDeliveryAddressModal()"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-amber-900 hover:bg-amber-950 active:bg-stone-900 text-white rounded-xl text-xs sm:text-sm font-semibold transition-all shadow-sm cursor-pointer"
                        >
                            <svg class="w-4 h-4 text-amber-300" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                            </svg>
                            <span>Set Delivery Address</span>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Shared Pinning Address Modal Component -->
        <?php require_once dirname(__DIR__) . '/components/address_modal.php'; ?>

        <div class="mt-8 pt-6 border-t border-gray-100 flex justify-between items-center">
            <a href="<?= url('/') ?>" class="text-amber-800 font-semibold hover:underline">← Back to Home</a>
            <a href="<?= url('/logout') ?>" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-semibold text-sm transition">
                Logout
            </a>
        </div>
    </div>
</main>
