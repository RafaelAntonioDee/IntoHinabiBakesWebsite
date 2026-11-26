<?php
/**
 * Delivery Address Confirmation Banner
 * Attached to Sticky Navbar
 * Only shown to logged-in users who have no saved delivery addresses yet.
 */

$loggedInUserId = \App\Core\Session::id();
if (!$loggedInUserId) {
    return;
}

$userHasSavedAddress = false;
try {
    $addressModel = new \App\Models\Address();
    $userHasSavedAddress = $addressModel->countByUserId($loggedInUserId) > 0;
} catch (\Exception $e) {
    error_log("Error checking saved address: " . $e->getMessage());
}

if ($userHasSavedAddress) {
    return;
}
?>

<!-- Include Shared Interactive Address Pinning Modal -->
<?php require_once __DIR__ . '/address_modal.php'; ?>

<div 
    x-data="{
        isBannerVisible: true
    }"
    @delivery-location-saved.window="isBannerVisible = false"
    class="w-full"
>
    <!-- 1. The Encircled Delivery Address Confirmation Banner Attached to Sticky Navbar -->
    <div 
        x-show="isBannerVisible"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2 max-h-0"
        x-transition:enter-end="opacity-100 translate-y-0 max-h-32"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-y-0 max-h-32"
        x-transition:leave-end="opacity-0 -translate-y-2 max-h-0"
        class="w-full bg-white/95 backdrop-blur-xs border-t border-amber-100/60 overflow-hidden"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2.5">
            <div class="bg-[#fde2bf] border border-[#f5c697] text-amber-950 rounded-full px-4 sm:px-6 py-2 shadow-sm flex items-center justify-between gap-3 sm:gap-4 transition-all">
                
                <!-- Left: Pin Icon + Announcement Text -->
                <div class="flex items-center gap-2.5 sm:gap-3 min-w-0 flex-1">
                    <!-- Brown Location Pin Icon -->
                    <div class="flex-shrink-0 text-amber-900">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 fill-current text-amber-900" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                        </svg>
                    </div>

                    <!-- Text Notice -->
                    <div class="text-xs sm:text-sm md:text-[15px] font-medium text-amber-950 truncate sm:whitespace-normal">
                        <span>Please confirm your delivery address so we know where to deliver your order.</span>
                    </div>
                </div>

                <!-- Right: Action Pill Button & Dismiss Cross -->
                <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                    <button 
                        type="button"
                        onclick="openDeliveryAddressModal()"
                        class="bg-[#78350f] hover:bg-[#59260b] active:bg-[#451a03] text-white text-xs sm:text-sm font-semibold px-4 sm:px-5 py-1.5 sm:py-2 rounded-full transition-colors shadow-sm cursor-pointer whitespace-nowrap focus:outline-none focus:ring-2 focus:ring-amber-700 focus:ring-offset-1"
                    >
                        Confirm Address
                    </button>

                    <!-- Dismiss Button -->
                    <button 
                        type="button"
                        @click="isBannerVisible = false" 
                        class="text-amber-900/70 hover:text-amber-950 p-1 transition-colors cursor-pointer rounded-full focus:outline-none"
                        aria-label="Close notification"
                    >
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
