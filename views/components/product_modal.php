<?php
/**
 * Product Details & Reviews Modal Component
 */
?>
<!-- Product Description & Reviews Modal -->
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
                            <p class="text-xs font-semibold text-emerald-700 mt-1 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Freshly Baked Daily from Scratch</span>
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <div class="text-2xl font-black text-amber-800">
                                ₱<span x-text="activeProduct ? activeProduct.price : ''"></span>
                            </div>
                            <div class="text-xs font-medium text-gray-500" x-text="activeProduct ? 'per ' + activeProduct.unit : ''"></div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-amber-900 mb-1.5">Description</h4>
                        <p class="text-gray-700 text-sm leading-relaxed" x-text="activeProduct ? activeProduct.description : ''"></p>
                    </div>

                    <!-- Allergen Caution -->
                    <div class="p-3 bg-amber-50/80 rounded-xl border border-amber-200/60">
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-amber-800 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                            </svg>
                            <div class="text-xs text-amber-950">
                                <span class="font-bold">Allergens:</span>
                                <template x-if="activeProduct && activeProduct.allergens && activeProduct.allergens.trim() !== ''">
                                    <span class="font-semibold text-amber-900" x-text="activeProduct.allergens"></span>
                                </template>
                                <template x-if="!activeProduct || !activeProduct.allergens || activeProduct.allergens.trim() === ''">
                                    <span class="text-emerald-800 font-medium block mt-0.5">
                                        There are no allergens listed for this product.
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Order Options & Add to Cart -->
                    <div class="pt-3 mt-auto space-y-3 border-t border-amber-100">
                        <div class="flex items-center justify-between gap-4">
                            <label for="modal-order-qty" class="text-xs font-bold uppercase tracking-wider text-amber-900">
                                Quantity
                            </label>
                            <div class="inline-flex items-center border border-gray-300 rounded-lg bg-white overflow-hidden">
                                <button
                                    type="button"
                                    @click="orderQuantity = Math.max(1, (parseInt(orderQuantity, 10) || 1) - 1)"
                                    class="px-3 py-1.5 text-sm font-bold text-gray-700 hover:bg-amber-50 transition cursor-pointer"
                                    aria-label="Decrease quantity"
                                >&minus;</button>
                                <input
                                    id="modal-order-qty"
                                    type="text"
                                    inputmode="numeric"
                                    x-model.number="orderQuantity"
                                    @input="orderQuantity = String(orderQuantity).replace(/\D/g, '')"
                                    @change="orderQuantity = Math.max(1, Math.min(99, parseInt(orderQuantity, 10) || 1))"
                                    class="w-12 text-center text-sm font-bold text-amber-950 border-x border-gray-200 py-1.5 focus:outline-none"
                                >
                                <button
                                    type="button"
                                    @click="orderQuantity = Math.min(99, (parseInt(orderQuantity, 10) || 1) + 1)"
                                    class="px-3 py-1.5 text-sm font-bold text-gray-700 hover:bg-amber-50 transition cursor-pointer"
                                    aria-label="Increase quantity"
                                >+</button>
                            </div>
                        </div>

                        <div>
                            <label for="modal-order-note" class="block text-xs font-bold uppercase tracking-wider text-amber-900 mb-1">
                                Special Instructions <span class="text-[11px] font-normal normal-case text-gray-500">(Optional)</span>
                            </label>
                            <input
                                id="modal-order-note"
                                type="text"
                                x-model="orderNote"
                                maxlength="150"
                                placeholder="e.g. Less sweet, gift note, or packaging request"
                                class="w-full px-3.5 py-2 text-xs border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-800 focus:border-amber-800 transition"
                            >
                        </div>

                        <button
                            type="button"
                            @click="addToCart()"
                            class="w-full bg-amber-800 hover:bg-amber-900 active:bg-amber-950 text-white font-bold py-3 px-4 rounded-xl shadow text-sm text-center transition duration-150 cursor-pointer flex items-center justify-center gap-2"
                        >
                            <svg x-show="!addedToCart" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
                            </svg>
                            <svg x-show="addedToCart" x-cloak class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                            <span x-text="addedToCart ? 'Added to Cart!' : ('Add to Cart · ₱' + ((parseFloat(activeProduct ? activeProduct.price : 0) || 0) * (parseInt(orderQuantity, 10) || 1)).toFixed(2))"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column: Ratings & Reviews -->
            <div class="w-full md:w-1/2 relative bg-amber-50/20">
                <div class="md:absolute md:inset-0 flex flex-col p-6 sm:p-7">
                    <div class="mb-4 flex-shrink-0">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-base font-bold text-gray-900">Ratings and reviews</h4>
                        <span class="text-gray-400 hover:text-gray-600 cursor-pointer" title="Verified customer ratings">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                            </svg>
                        </span>
                    </div>

                    <!-- Rating Overview -->
                    <div class="flex items-center gap-6 pb-4 border-b border-gray-200/80">
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

                        <!-- Rating Distribution Bars -->
                        <div class="flex-grow space-y-1">
                            <template x-for="s in [5, 4, 3, 2, 1]" :key="s">
                                <button
                                    type="button"
                                    @click="toggleRatingFilter(s)"
                                    :class="selectedRatingFilter === s ? 'bg-amber-100/90 ring-1 ring-amber-700' : 'hover:bg-amber-50/80'"
                                    class="w-full flex items-center gap-2.5 text-xs text-gray-600 font-medium px-1.5 py-0.5 rounded-md transition cursor-pointer"
                                    :title="'Filter by ' + s + ' star reviews'"
                                >
                                    <span class="w-2.5 text-right text-gray-700" x-text="s"></span>
                                    <div class="flex-grow h-2.5 bg-gray-200 rounded-full overflow-hidden">
                                        <div 
                                            class="h-full bg-amber-800 rounded-full transition-all duration-300" 
                                            :style="'width: ' + (getSummary().stars && getSummary().stars[s] ? getSummary().stars[s].pct : 0) + '%;'"
                                        ></div>
                                    </div>
                                    <span class="w-5 text-[10px] text-gray-400 text-right" x-text="getSummary().stars && getSummary().stars[s] ? getSummary().stars[s].count : 0"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Community Reviews List -->
                <div class="space-y-3 flex-1 min-h-0 max-h-64 md:max-h-none overflow-y-auto pr-1 mb-4">
                    <template x-if="getReviews().length === 0">
                        <div class="text-center py-6 text-xs text-gray-500">
                            <span x-text="selectedRatingFilter ? ('No ' + selectedRatingFilter + '-star reviews yet.') : 'No reviews yet. Be the first to share your experience with this treat!'"></span>
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

                <!-- Review Submission Section -->
                <template x-if="isLoggedIn">
                    <div class="mt-auto pt-4 border-t border-amber-200/60 space-y-3 flex-shrink-0">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-sm font-bold text-amber-950 block" x-text="getSummary().user_review ? 'Update your review' : 'Review this product'"></span>
                                <span class="text-[11px] text-gray-500" x-text="'Posting as ' + (loggedInUser || 'Verified Customer')"></span>
                            </div>
                            <!-- Star Selection -->
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

                        <textarea 
                            x-model="reviewComment" 
                            rows="2" 
                            placeholder="Share your experience with this flavor..."
                            class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-800 focus:border-amber-800 bg-white transition"
                        ></textarea>

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

                <!-- Sign-in Prompt for Guests -->
                <template x-if="!isLoggedIn">
                    <div class="mt-auto pt-4 border-t border-amber-200/60 flex-shrink-0">
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
</div>

<script>
function productCatalog() {
    return {
        activeProduct: null,
        userRating: 5,
        hoverRating: 0,
        selectedRatingFilter: null,
        orderQuantity: 1,
        orderNote: '',
        addedToCart: false,
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
            const reviews = this.getSummary().reviews || [];
            if (this.selectedRatingFilter === null) {
                return reviews;
            }
            return reviews.filter(r => Number(r.rating) === Number(this.selectedRatingFilter));
        },

        toggleRatingFilter(star) {
            this.selectedRatingFilter = (this.selectedRatingFilter === star) ? null : star;
        },

        addToCart() {
            if (!this.activeProduct) return;
            const qty = Math.max(1, Math.min(99, parseInt(this.orderQuantity, 10) || 1));
            const note = (this.orderNote || '').trim();
            let cart = [];
            try {
                cart = JSON.parse(localStorage.getItem('hinabi_cart') || '[]');
            } catch (e) {
                cart = [];
            }
            const existing = cart.find(item => Number(item.id) === Number(this.activeProduct.id) && (item.note || '') === note);
            if (existing) {
                existing.quantity = (existing.quantity || 1) + qty;
            } else {
                cart.push({
                    id: this.activeProduct.id,
                    name: this.activeProduct.name,
                    price: parseFloat(this.activeProduct.price) || 0,
                    image_url: this.activeProduct.image_url,
                    quantity: qty,
                    note: note
                });
            }
            localStorage.setItem('hinabi_cart', JSON.stringify(cart));
            window.dispatchEvent(new CustomEvent('cart-updated', { detail: cart }));
            this.addedToCart = true;
            setTimeout(() => { this.addedToCart = false; }, 1500);
        },

        openModal(productId) {
            const product = this.productsMap[productId] || null;
            if (!product) return;
            this.activeProduct = product;
            this.selectedRatingFilter = null;
            this.orderQuantity = 1;
            this.orderNote = '';
            this.addedToCart = false;
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

        submitReview() {
            if (!this.isLoggedIn) {
                window.location.href = '<?= url('/login') ?>';
                return;
            }
            if (!this.reviewComment.trim() || !this.activeProduct || this.isSubmitting) return;
            this.isSubmitting = true;

            const self = this;
            $.ajax({
                url: '<?= url('/products/review') ?>',
                method: 'POST',
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify({
                    product_id: self.activeProduct.id,
                    rating: self.userRating,
                    comment: self.reviewComment.trim()
                })
            })
            .done(function(res) {
                if (res.success && res.data) {
                    self.reviewsData[self.activeProduct.id] = res.data;
                    self.reviewSubmitted = true;
                    setTimeout(function() { self.reviewSubmitted = false; }, 4000);
                } else if (res.requires_login) {
                    window.location.href = '<?= url('/login') ?>';
                } else {
                    alert(res.error || 'Failed to submit review.');
                }
            })
            .fail(function(err) {
                console.error('Review submission failed:', err);
                alert('Failed to submit review.');
            })
            .always(function() {
                self.isSubmitting = false;
            });
        }
    };
}
</script>
