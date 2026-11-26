<?php
$addresses = $addresses ?? [];
$maxAddresses = $maxAddresses ?? 3;
$addressCount = count($addresses);
?>
<main class="max-w-2xl mx-auto px-4 py-12 flex-grow w-full">
    <div class="bg-white p-8 rounded-lg shadow-md border border-amber-100">
        <div class="flex items-center space-x-4 mb-6">
            <div id="profile-avatar-initial" class="w-16 h-16 bg-amber-800 text-white text-2xl font-bold flex items-center justify-center rounded-full shadow-inner">
                <?= strtoupper(substr($user['full_name'] ?? 'U', 0, 1)) ?>
            </div>
            <div>
                <h1 id="profile-header-name" class="text-2xl font-bold text-amber-900"><?= e($user['full_name']) ?></h1>
                <p class="text-gray-500 text-sm">Account Dashboard</p>
            </div>
        </div>

        <div id="profile-ajax-alert" class="hidden mb-4"></div>

        <div class="divide-y divide-amber-100/80 border-t border-amber-100 pt-2"
             x-data="{ editingName: false, editingEmail: false, editingPhone: false }"
             @profile-field-saved.window="if ($event.detail === 'name') editingName = false; if ($event.detail === 'email') editingEmail = false; if ($event.detail === 'phone') editingPhone = false;">
            <!-- Full Name -->
            <div class="py-3.5">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Full Name</label>
                <div x-show="!editingName" class="flex items-center justify-between gap-4">
                    <p id="profile-display-name" class="text-base sm:text-lg font-medium text-gray-800"><?= e($user['full_name']) ?></p>
                    <button
                        type="button"
                        @click="editingName = true; editingEmail = false; editingPhone = false"
                        class="text-xs font-semibold text-amber-800 hover:text-amber-950 bg-amber-50 hover:bg-amber-100 border border-amber-200 px-3 py-1 rounded-lg transition cursor-pointer flex-shrink-0"
                    >
                        Edit
                    </button>
                </div>
                <form id="profile-name-form" action="<?= url('/profile/name') ?>" method="POST" data-ajax="true" data-alert-target="#profile-ajax-alert" novalidate x-show="editingName" x-cloak class="flex flex-col sm:flex-row sm:items-start gap-2 max-w-md mt-1">
                    <div class="flex-1">
                        <input
                            type="text"
                            name="full_name"
                            value="<?= e($user['full_name']) ?>"
                            placeholder="e.g. Juan Dela Cruz"
                            required
                            class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-700 focus:border-amber-700 transition"
                        >
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            type="submit"
                            class="px-4 py-2 bg-amber-800 hover:bg-amber-900 text-white text-xs sm:text-sm font-semibold rounded-lg transition cursor-pointer whitespace-nowrap"
                        >
                            Save
                        </button>
                        <button
                            type="button"
                            @click="editingName = false"
                            class="px-3 py-2 text-xs sm:text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition cursor-pointer"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Email Address -->
            <div class="py-3.5">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Email Address</label>
                <div x-show="!editingEmail" class="flex items-center justify-between gap-4">
                    <p id="profile-display-email" class="text-base sm:text-lg font-medium text-gray-800 truncate"><?= e($user['email']) ?></p>
                    <button
                        type="button"
                        @click="editingEmail = true; editingName = false; editingPhone = false"
                        class="text-xs font-semibold text-amber-800 hover:text-amber-950 bg-amber-50 hover:bg-amber-100 border border-amber-200 px-3 py-1 rounded-lg transition cursor-pointer flex-shrink-0"
                    >
                        Edit
                    </button>
                </div>
                <form id="profile-email-form" x-show="editingEmail" x-cloak action="<?= url('/profile/email') ?>" method="POST" data-ajax="true" data-alert-target="#profile-ajax-alert" novalidate class="flex flex-col sm:flex-row sm:items-start gap-2 max-w-md mt-1">
                    <div class="flex-1">
                        <input
                            type="email"
                            name="email"
                            value="<?= e($user['email']) ?>"
                            placeholder="e.g. juan@example.com"
                            required
                            class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-700 focus:border-amber-700 transition"
                        >
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            type="submit"
                            class="px-4 py-2 bg-amber-800 hover:bg-amber-900 text-white text-xs sm:text-sm font-semibold rounded-lg transition cursor-pointer whitespace-nowrap"
                        >
                            Save
                        </button>
                        <button
                            type="button"
                            @click="editingEmail = false"
                            class="px-3 py-2 text-xs sm:text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition cursor-pointer"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Phone Number -->
            <div class="py-3.5">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Phone Number</label>
                <div x-show="!editingPhone" class="flex items-center justify-between gap-4">
                    <p id="profile-display-phone" class="text-base sm:text-lg font-medium text-gray-800">
                        <?= !empty($user['phone_number']) ? e($user['phone_number']) : '<span class="text-gray-400 italic text-base">Not set</span>' ?>
                    </p>
                    <button
                        type="button"
                        @click="editingPhone = true; editingName = false; editingEmail = false"
                        class="text-xs font-semibold text-amber-800 hover:text-amber-950 bg-amber-50 hover:bg-amber-100 border border-amber-200 px-3 py-1 rounded-lg transition cursor-pointer flex-shrink-0"
                    >
                        Edit
                    </button>
                </div>
                <form id="profile-phone-form" x-show="editingPhone" x-cloak action="<?= url('/profile/phone') ?>" method="POST" data-ajax="true" data-alert-target="#profile-ajax-alert" novalidate class="flex flex-col sm:flex-row sm:items-start gap-2 max-w-md mt-1">
                    <div class="flex-1">
                        <input
                            type="tel"
                            name="phone_number"
                            value="<?= e($user['phone_number'] ?? '') ?>"
                            placeholder="e.g. 0917 123 4567"
                            inputmode="tel"
                            minlength="10"
                            maxlength="16"
                            pattern="(09[\d\s\-]{9,12}|(\+?63)9[\d\s\-]{9,12}|0[2-8][\d\s\-()]{7,12})"
                            title="Enter a valid PH mobile (11 digits, e.g. 09171234567 or +639171234567) or landline number"
                            required
                            class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-700 focus:border-amber-700 transition"
                        >
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            type="submit"
                            class="px-4 py-2 bg-amber-800 hover:bg-amber-900 text-white text-xs sm:text-sm font-semibold rounded-lg transition cursor-pointer whitespace-nowrap"
                        >
                            Save
                        </button>
                        <button
                            type="button"
                            @click="editingPhone = false"
                            class="px-3 py-2 text-xs sm:text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition cursor-pointer"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Member Since -->
            <div class="py-3.5">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Member Since</label>
                <p class="text-base sm:text-lg font-medium text-gray-800">
                    <?= !empty($user['created_at']) ? date("F j, Y", strtotime($user['created_at'])) : 'Recently' ?>
                </p>
            </div>

            <!-- Saved Delivery Addresses (Up to 3) -->
            <div class="pt-4">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase">
                        Delivery Addresses (<?= $addressCount ?> / <?= $maxAddresses ?>)
                    </label>
                    <?php if ($addressCount > 0 && $addressCount < $maxAddresses): ?>
                        <button 
                            type="button" 
                            onclick="openDeliveryAddressModal()" 
                            class="inline-flex items-center gap-1 text-xs font-semibold text-amber-900 hover:text-amber-950 bg-amber-100 hover:bg-amber-200/80 px-3 py-1.5 rounded-lg transition cursor-pointer"
                        >
                            <span>+ Add Address</span>
                        </button>
                    <?php endif; ?>
                </div>

                <?php if ($addressCount > 0): ?>
                    <div class="space-y-3">
                        <?php foreach ($addresses as $addr): ?>
                            <?php
                                $addrJson = json_encode($addr, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                                $isDefault = !empty($addr['is_default']);
                            ?>
                            <div class="p-4 rounded-xl border <?= $isDefault ? 'bg-amber-50/90 border-amber-300' : 'bg-stone-50/60 border-gray-200' ?> text-sm">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-1">
                                            <span class="font-semibold text-amber-950">
                                                <?= e($addr['recipient_name'] ?: $user['full_name']) ?>
                                            </span>
                                            <?php if ($isDefault): ?>
                                                <span class="text-[11px] font-bold uppercase tracking-wide px-2 py-0.5 rounded-full bg-amber-800 text-white">
                                                    Default
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-amber-950 font-medium">
                                            <?= e($addr['street_address'] ?? '') ?><?= !empty($addr['barangay']) ? ', ' . e($addr['barangay']) : '' ?>, <?= e($addr['city'] ?? '') ?><?= !empty($addr['postal_code']) ? ' ' . e($addr['postal_code']) : '' ?>
                                        </p>
                                        <?php if (!empty($addr['delivery_notes'])): ?>
                                            <p class="text-xs text-gray-600 mt-1 italic">Note: <?= e($addr['delivery_notes']) ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <?php if (!$isDefault): ?>
                                            <button
                                                type="button"
                                                onclick="setDefaultAddress(<?= (int)$addr['id'] ?>)"
                                                class="text-xs font-semibold text-amber-800 hover:text-amber-950 underline cursor-pointer"
                                            >
                                                Set Default
                                            </button>
                                        <?php endif; ?>
                                        <button
                                            type="button"
                                            onclick='openDeliveryAddressModal(<?= $addrJson ?>)'
                                            class="text-xs font-semibold text-stone-700 hover:text-amber-900 underline cursor-pointer"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            type="button"
                                            onclick="deleteSavedAddress(<?= (int)$addr['id'] ?>)"
                                            class="text-xs font-semibold text-rose-600 hover:text-rose-800 underline cursor-pointer"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
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
                            <span>Add Delivery Address</span>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Shared Pinning Address Modal Component -->
        <?php require_once dirname(__DIR__) . '/components/address_modal.php'; ?>

        <div class="mt-8 pt-6 border-t border-gray-100 flex justify-between items-center">
            <a href="<?= url('/') ?>" class="text-amber-800 font-semibold hover:underline">← Back to Home</a>
            <a href="<?= url('/logout') ?>" onclick="return openLogoutConfirmModal(event)" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-semibold text-sm transition">
                Logout
            </a>
        </div>
    </div>
</main>

<script>
$(document).ready(function() {
    $('#profile-name-form').on('ajax:success', function(e, res) {
        if (res && res.full_name) {
            $('#profile-display-name, #profile-header-name').text(res.full_name);
            $('#profile-avatar-initial').text(res.full_name.charAt(0).toUpperCase());
            window.dispatchEvent(new CustomEvent('profile-field-saved', { detail: 'name' }));
        }
    });

    $('#profile-email-form').on('ajax:success', function(e, res) {
        if (res && res.email) {
            $('#profile-display-email').text(res.email);
            window.dispatchEvent(new CustomEvent('profile-field-saved', { detail: 'email' }));
        }
    });

    $('#profile-phone-form').on('ajax:success', function(e, res) {
        if (res && res.phone_number) {
            $('#profile-display-phone').text(res.phone_number);
            window.dispatchEvent(new CustomEvent('profile-field-saved', { detail: 'phone' }));
        }
    });
});

function setDefaultAddress(addressId) {
    $.ajax({
        url: <?= json_encode(url('/api/address/default'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
        method: 'POST',
        contentType: 'application/json',
        dataType: 'json',
        data: JSON.stringify({ id: addressId })
    })
    .done(function(data) {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.error || 'Could not update default address.');
        }
    })
    .fail(function() {
        alert('Could not connect to server.');
    });
}

function deleteSavedAddress(addressId) {
    if (!confirm('Are you sure you want to delete this delivery address?')) return;

    $.ajax({
        url: <?= json_encode(url('/api/address/delete'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
        method: 'POST',
        contentType: 'application/json',
        dataType: 'json',
        data: JSON.stringify({ id: addressId })
    })
    .done(function(data) {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.error || 'Could not delete address.');
        }
    })
    .fail(function() {
        alert('Could not connect to server.');
    });
}
</script>
