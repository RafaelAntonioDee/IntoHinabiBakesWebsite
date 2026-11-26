<?php
/**
 * Shared Portal Navigation Partial
 * Included by header.php, admin_header.php, and baker_header.php
 * Robust, single-row flex layout with fixed-width view switcher and responsive scrolling.
 */
$_route = currentRoute();
$_isAdminRoute = str_starts_with($_route, '/admin');
$_isBakerRoute = str_starts_with($_route, '/baker');
$_isStorefrontRoute = !$_isAdminRoute && !$_isBakerRoute;

$_initialView = $_isAdminRoute ? 'admin' : ($_isBakerRoute ? 'baker' : 'storefront');

$_userIsAdmin = \App\Core\Session::isAdmin();
$_userIsBaker = \App\Core\Session::isBaker();
$_userIsStaff = $_userIsAdmin || $_userIsBaker;
$_isLoggedIn  = \App\Core\Session::check();
?>

<style>
[x-cloak] { display: none !important; }
.scrollbar-none::-webkit-scrollbar { display: none; }
.scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<nav class="bg-white text-gray-800 shadow-md sticky top-0 z-50 border-b border-amber-100"
     x-data="{ currentView: '<?= $_initialView ?>', viewOpen: false, userOpen: false, mobileOpen: false }"
     @click.outside="viewOpen = false; userOpen = false">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Main Navbar Flex Row: strictly nowrap and h-16 to prevent any multi-line stacking -->
        <div class="flex items-center justify-between h-16 gap-3 sm:gap-4 lg:gap-6 flex-nowrap min-w-0">

            <!-- Left Section: Logo + Staff View Switcher -->
            <div class="flex items-center gap-3 sm:gap-4 flex-shrink-0">
                <!-- Brand Logo -->
                <a href="<?= url('/') ?>" class="text-xl sm:text-2xl font-bold text-amber-900 flex items-center space-x-2 flex-shrink-0 group">
                    <img src="https://png.pngtree.com/recommend-works/png-clipart/20240728/ourmid/pngtree-simple-logo-of-chocolate-cookies-png-image_13238339.png"
                         alt="Into Hinabi Bakes Logo"
                         class="w-8 h-8 sm:w-9 sm:h-9 object-contain flex-shrink-0">
                    <span class="group-hover:text-amber-800 transition whitespace-nowrap">Into Hinabi Bakes</span>
                </a>

                <!-- Staff View Switcher Dropdown (Fixed width: w-32 sm:w-36 so switching never shifts adjacent elements) -->
                <?php if ($_userIsStaff): ?>
                    <div class="relative flex-shrink-0 hidden sm:block">
                        <button type="button"
                                @click="viewOpen = !viewOpen; userOpen = false"
                                class="w-32 sm:w-36 h-8 sm:h-9 flex items-center justify-between px-2.5 sm:px-3 py-1 rounded-lg text-xs font-bold transition border cursor-pointer bg-amber-50 hover:bg-amber-100 text-amber-900 border-amber-300 shadow-sm flex-shrink-0">
                            <!-- View Label -->
                            <template x-if="currentView === 'admin'">
                                <span class="flex items-center gap-1.5 truncate">
                                    <svg class="w-3.5 h-3.5 text-amber-800 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span class="truncate">Admin View</span>
                                </span>
                            </template>
                            <template x-if="currentView === 'baker'">
                                <span class="flex items-center gap-1.5 truncate">
                                    <svg class="w-3.5 h-3.5 text-amber-700 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048A8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z"/></svg>
                                    <span class="truncate">Baker View</span>
                                </span>
                            </template>
                            <template x-if="currentView === 'storefront'">
                                <span class="flex items-center gap-1.5 truncate">
                                    <svg class="w-3.5 h-3.5 text-stone-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                                    <span class="truncate">Storefront</span>
                                </span>
                            </template>
                            <svg class="w-3 h-3 transition-transform text-stone-400 flex-shrink-0 ml-1" :class="viewOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                            </svg>
                        </button>

                        <!-- Dropdown Options to Select View -->
                        <div x-show="viewOpen"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             @click.outside="viewOpen = false"
                             class="absolute left-0 mt-2 w-52 bg-white rounded-xl shadow-xl border border-amber-200 py-1 z-50 overflow-hidden"
                             style="display: none;">
                            <div class="px-3 py-1.5 border-b border-amber-100 bg-amber-50">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-amber-800">Select View</p>
                            </div>

                            <!-- Option 1: Storefront View -->
                            <button type="button"
                                    @click="currentView = 'storefront'; viewOpen = false"
                                    class="w-full text-left flex items-center gap-2.5 px-3.5 py-2 text-sm transition cursor-pointer hover:bg-amber-50"
                                    :class="currentView === 'storefront' ? 'bg-amber-50 font-bold text-amber-900' : 'text-stone-700'">
                                <svg class="w-4 h-4 text-amber-700 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                                <span>Storefront</span>
                                <span x-show="currentView === 'storefront'" class="ml-auto text-xs text-amber-700 font-bold">✓</span>
                            </button>

                            <!-- Option 2: Baker View -->
                            <?php if ($_userIsStaff): ?>
                                <button type="button"
                                        @click="currentView = 'baker'; viewOpen = false"
                                        class="w-full text-left flex items-center gap-2.5 px-3.5 py-2 text-sm transition cursor-pointer hover:bg-amber-50"
                                        :class="currentView === 'baker' ? 'bg-amber-50 font-bold text-amber-900' : 'text-stone-700'">
                                    <svg class="w-4 h-4 text-amber-700 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048A8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z"/></svg>
                                    <span>Baker View</span>
                                    <span x-show="currentView === 'baker'" class="ml-auto text-xs text-amber-700 font-bold">✓</span>
                                </button>
                            <?php endif; ?>

                            <!-- Option 3: Admin View -->
                            <?php if ($_userIsAdmin): ?>
                                <button type="button"
                                        @click="currentView = 'admin'; viewOpen = false"
                                        class="w-full text-left flex items-center gap-2.5 px-3.5 py-2 text-sm transition cursor-pointer hover:bg-amber-50"
                                        :class="currentView === 'admin' ? 'bg-amber-50 font-bold text-amber-900' : 'text-stone-700'">
                                    <svg class="w-4 h-4 text-amber-700 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>Admin View</span>
                                    <span x-show="currentView === 'admin'" class="ml-auto text-xs text-amber-700 font-bold">✓</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Center: Horizontally Scrollable Nav Buttons Container (flex-1 min-w-0 for smooth auto-scrolling) -->
            <div class="hidden md:flex items-center overflow-x-auto whitespace-nowrap scrollbar-none py-1 flex-1 min-w-0 px-2">

                <!-- 1. Storefront Navigation Buttons -->
                <div x-show="currentView === 'storefront'" class="flex items-center space-x-4 lg:space-x-6 whitespace-nowrap flex-shrink-0" x-cloak>
                    <a href="<?= url('/') ?>" class="text-sm lg:text-base font-semibold transition <?= ($_route === '/') ? 'text-amber-800 font-bold' : 'text-gray-800 hover:text-amber-600' ?>">
                        Home
                    </a>
                    <a href="<?= url('/products') ?>" class="text-sm lg:text-base font-semibold transition <?= str_starts_with($_route, '/products') ? 'text-amber-800 font-bold' : 'text-gray-800 hover:text-amber-600' ?>">
                        Cookies &amp; Treats
                    </a>
                    <a href="<?= url('/about') ?>" class="text-sm lg:text-base font-semibold transition <?= str_starts_with($_route, '/about') ? 'text-amber-800 font-bold' : 'text-gray-800 hover:text-amber-600' ?>">
                        About Us
                    </a>
                    <a href="<?= url('/contact') ?>" class="text-sm lg:text-base font-semibold transition <?= str_starts_with($_route, '/contact') ? 'text-amber-800 font-bold' : 'text-gray-800 hover:text-amber-600' ?>">
                        Contact
                    </a>
                </div>

                <!-- 2. Baker Navigation Buttons -->
                <?php if ($_userIsStaff): ?>
                    <div x-show="currentView === 'baker'" class="flex items-center space-x-4 lg:space-x-6 whitespace-nowrap flex-shrink-0" style="display: none;" x-cloak>
                        <a href="<?= url('/baker') ?>" class="text-sm lg:text-base font-semibold transition <?= ($_route === '/baker') ? 'text-amber-800 font-bold' : 'text-gray-800 hover:text-amber-600' ?>">
                            Dashboard
                        </a>
                        <a href="<?= url('/baker/orders') ?>" class="text-sm lg:text-base font-semibold transition <?= str_starts_with($_route, '/baker/orders') ? 'text-amber-800 font-bold' : 'text-gray-800 hover:text-amber-600' ?>">
                            Baking Queue
                        </a>
                        <a href="<?= url('/baker/inventory') ?>" class="text-sm lg:text-base font-semibold transition <?= str_starts_with($_route, '/baker/inventory') ? 'text-amber-800 font-bold' : 'text-gray-800 hover:text-amber-600' ?>">
                            Inventory &amp; Stock
                        </a>
                    </div>
                <?php endif; ?>

                <!-- 3. Admin Navigation Buttons -->
                <?php if ($_userIsAdmin): ?>
                    <div x-show="currentView === 'admin'" class="flex items-center space-x-4 lg:space-x-6 whitespace-nowrap flex-shrink-0" style="display: none;" x-cloak>
                        <a href="<?= url('/admin') ?>" class="text-sm lg:text-base font-semibold transition <?= ($_route === '/admin') ? 'text-amber-800 font-bold' : 'text-gray-800 hover:text-amber-600' ?>">
                            Overview
                        </a>
                        <a href="<?= url('/admin/users') ?>" class="text-sm lg:text-base font-semibold transition <?= str_starts_with($_route, '/admin/users') ? 'text-amber-800 font-bold' : 'text-gray-800 hover:text-amber-600' ?>">
                            Users &amp; Staff
                        </a>
                        <a href="<?= url('/admin/orders') ?>" class="text-sm lg:text-base font-semibold transition <?= str_starts_with($_route, '/admin/orders') ? 'text-amber-800 font-bold' : 'text-gray-800 hover:text-amber-600' ?>">
                            Orders
                        </a>
                        <a href="<?= url('/admin/products') ?>" class="text-sm lg:text-base font-semibold transition <?= str_starts_with($_route, '/admin/products') ? 'text-amber-800 font-bold' : 'text-gray-800 hover:text-amber-600' ?>">
                            Products
                        </a>
                        <a href="<?= url('/admin/inquiries') ?>" class="text-sm lg:text-base font-semibold transition <?= str_starts_with($_route, '/admin/inquiries') ? 'text-amber-800 font-bold' : 'text-gray-800 hover:text-amber-600' ?>">
                            Inquiries
                        </a>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Right Section: User Profile & Actions -->
            <div class="hidden md:flex items-center space-x-3 flex-shrink-0">
                <?php if ($_isLoggedIn): ?>
                    <!-- User Profile Dropdown -->
                    <div class="relative flex-shrink-0">
                        <button @click="userOpen = !userOpen; viewOpen = false"
                                class="flex items-center space-x-1.5 text-amber-800 hover:text-amber-900 font-semibold cursor-pointer whitespace-nowrap text-sm">
                            <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                            <span class="max-w-[120px] sm:max-w-[150px] truncate"><?= e(\App\Core\Session::name()) ?></span>
                            <?php if ($_userIsStaff): ?>
                                <span class="text-[11px] px-2 py-0.5 rounded-full font-bold <?= $_userIsAdmin ? 'bg-amber-100 text-amber-900' : 'bg-orange-100 text-orange-800' ?>">
                                    <?= e(\App\Core\Session::roleName()) ?>
                                </span>
                            <?php endif; ?>
                            <svg class="w-3 h-3 text-stone-400 transition-transform" :class="userOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                        </button>

                        <div x-show="userOpen"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             @click.outside="userOpen = false"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-amber-100 py-1 z-50 overflow-hidden"
                             style="display: none;">
                            <a href="<?= url('/profile') ?>" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-stone-700 hover:bg-amber-50 hover:text-amber-900 transition">
                                <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                My Profile
                            </a>
                            <div class="border-t border-amber-50"></div>
                            <a href="<?= url('/logout') ?>" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                                Sign Out
                            </a>
                        </div>
                    </div>

                    <!-- Direct Logout Button -->
                    <a href="<?= url('/logout') ?>" class="bg-amber-800 hover:bg-amber-900 text-white px-3.5 py-1.5 rounded-lg font-semibold text-xs transition flex-shrink-0 whitespace-nowrap">
                        Logout
                    </a>
                <?php else: ?>
                    <a href="<?= url('/login') ?>" class="text-gray-800 hover:text-amber-600 font-semibold text-sm transition flex-shrink-0">Login</a>
                    <a href="<?= url('/register') ?>" class="bg-amber-800 hover:bg-amber-900 text-white px-3.5 py-1.5 rounded-lg font-semibold text-xs transition flex-shrink-0 whitespace-nowrap">
                        Sign Up
                    </a>
                <?php endif; ?>
            </div>

            <!-- Mobile Hamburger Button -->
            <button @click="mobileOpen = !mobileOpen" class="md:hidden focus:outline-none text-gray-800 text-2xl ml-auto" aria-label="Toggle navigation">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" style="display: none;"/>
                </svg>
            </button>
        </div>

        <!-- Mobile Menu (Collapsible) -->
        <div x-show="mobileOpen" class="md:hidden mt-3 pt-3 border-t border-amber-100 space-y-2 pb-3" style="display: none;">
            <!-- View Switcher for Staff on Mobile -->
            <?php if ($_userIsStaff): ?>
                <div class="p-2.5 bg-amber-50 rounded-xl space-y-1 mb-2">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-amber-800 px-1">Switch View</p>
                    <div class="flex gap-1.5 overflow-x-auto whitespace-nowrap scrollbar-none pb-0.5">
                        <button type="button" @click="currentView = 'storefront'" class="px-2.5 py-1 rounded text-xs font-semibold cursor-pointer whitespace-nowrap" :class="currentView === 'storefront' ? 'bg-amber-800 text-white' : 'bg-white text-stone-700'">Storefront</button>
                        <button type="button" @click="currentView = 'baker'" class="px-2.5 py-1 rounded text-xs font-semibold cursor-pointer whitespace-nowrap" :class="currentView === 'baker' ? 'bg-amber-800 text-white' : 'bg-white text-stone-700'">Baker View</button>
                        <?php if ($_userIsAdmin): ?>
                            <button type="button" @click="currentView = 'admin'" class="px-2.5 py-1 rounded text-xs font-semibold cursor-pointer whitespace-nowrap" :class="currentView === 'admin' ? 'bg-amber-800 text-white' : 'bg-white text-stone-700'">Admin View</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Navigation Links dynamically changing based on currentView -->
            <div x-show="currentView === 'admin'">
                <a href="<?= url('/admin') ?>" class="block font-medium py-1.5 <?= ($_route === '/admin') ? 'text-amber-800 font-bold' : 'text-gray-700' ?>">Overview</a>
                <a href="<?= url('/admin/users') ?>" class="block font-medium py-1.5 <?= str_starts_with($_route, '/admin/users') ? 'text-amber-800 font-bold' : 'text-gray-700' ?>">Users &amp; Staff</a>
                <a href="<?= url('/admin/orders') ?>" class="block font-medium py-1.5 <?= str_starts_with($_route, '/admin/orders') ? 'text-amber-800 font-bold' : 'text-gray-700' ?>">Orders</a>
                <a href="<?= url('/admin/products') ?>" class="block font-medium py-1.5 <?= str_starts_with($_route, '/admin/products') ? 'text-amber-800 font-bold' : 'text-gray-700' ?>">Products</a>
                <a href="<?= url('/admin/inquiries') ?>" class="block font-medium py-1.5 <?= str_starts_with($_route, '/admin/inquiries') ? 'text-amber-800 font-bold' : 'text-gray-700' ?>">Inquiries</a>
            </div>

            <div x-show="currentView === 'baker'">
                <a href="<?= url('/baker') ?>" class="block font-medium py-1.5 <?= ($_route === '/baker') ? 'text-amber-800 font-bold' : 'text-gray-700' ?>">Dashboard</a>
                <a href="<?= url('/baker/orders') ?>" class="block font-medium py-1.5 <?= str_starts_with($_route, '/baker/orders') ? 'text-amber-800 font-bold' : 'text-gray-700' ?>">Baking Queue</a>
                <a href="<?= url('/baker/inventory') ?>" class="block font-medium py-1.5 <?= str_starts_with($_route, '/baker/inventory') ? 'text-amber-800 font-bold' : 'text-gray-700' ?>">Inventory &amp; Stock</a>
            </div>

            <div x-show="currentView === 'storefront'">
                <a href="<?= url('/') ?>" class="block font-medium py-1.5 <?= ($_route === '/') ? 'text-amber-800 font-bold' : 'text-gray-700' ?>">Home</a>
                <a href="<?= url('/products') ?>" class="block font-medium py-1.5 <?= str_starts_with($_route, '/products') ? 'text-amber-800 font-bold' : 'text-gray-700' ?>">Cookies &amp; Treats</a>
                <a href="<?= url('/about') ?>" class="block font-medium py-1.5 <?= str_starts_with($_route, '/about') ? 'text-amber-800 font-bold' : 'text-gray-700' ?>">About Us</a>
                <a href="<?= url('/contact') ?>" class="block font-medium py-1.5 <?= str_starts_with($_route, '/contact') ? 'text-amber-800 font-bold' : 'text-gray-700' ?>">Contact</a>
            </div>

            <div class="border-t border-amber-100 pt-2">
                <?php if ($_isLoggedIn): ?>
                    <a href="<?= url('/profile') ?>" class="block text-amber-800 font-semibold py-1.5">Profile (<?= e(\App\Core\Session::name()) ?>)</a>
                    <a href="<?= url('/logout') ?>" class="block text-rose-600 font-semibold py-1.5">Logout</a>
                <?php else: ?>
                    <a href="<?= url('/login') ?>" class="block text-gray-700 font-medium py-1.5">Login</a>
                    <a href="<?= url('/register') ?>" class="block text-amber-800 font-semibold py-1.5">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Attached Sticky Delivery Address Confirmation Banner -->
    <?php 
    $_uri = $_SERVER['REQUEST_URI'] ?? '';
    $_isCookiesPage = str_contains($_uri, 'products') || (($_route ?? '') === '/products' || str_starts_with(($_route ?? ''), '/products'));
    if ($_isCookiesPage): 
    ?>
        <?php require_once dirname(__DIR__) . '/components/delivery_banner.php'; ?>
    <?php endif; ?>
</nav>
