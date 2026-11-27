<main class="max-w-4xl mx-auto px-4 py-12 flex-grow w-full" x-data="cartPage()">
    <div class="text-center max-w-2xl mx-auto mb-10 space-y-2">
        <h1 class="text-3xl md:text-4xl font-extrabold text-amber-950">Your Cart</h1>
        <p class="text-gray-600 text-sm">Review your selected treats before placing your order.</p>
    </div>

    <template x-if="items.length === 0">
        <div class="bg-white rounded-xl shadow-md border border-amber-100 p-12 text-center">
            <svg class="w-16 h-16 text-amber-300 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
            </svg>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Your cart is empty</h2>
            <p class="text-gray-500 text-sm mb-6">Browse our fresh bakes menu and add some treats.</p>
            <a href="<?= url('/products') ?>" class="inline-block bg-amber-800 hover:bg-amber-900 text-white font-bold py-3 px-6 rounded-lg shadow transition duration-150 text-sm">
                Browse Menu
            </a>
        </div>
    </template>

    <template x-if="items.length > 0">
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-md border border-amber-100 overflow-hidden">
                <div class="divide-y divide-amber-50">
                    <template x-for="(item, index) in items" :key="item.id">
                        <div class="flex items-center gap-4 p-4 sm:p-5 hover:bg-amber-50/30 transition">
                            <img :src="item.image_url" :alt="item.name" class="w-20 h-20 sm:w-24 sm:h-24 rounded-lg object-cover border border-amber-100 flex-shrink-0">

                            <div class="flex-grow min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <h3 class="text-base font-bold text-gray-900 truncate" x-text="item.name"></h3>
                                        <span class="text-xs uppercase tracking-wider font-semibold px-2 py-0.5 rounded mt-1 inline-block"
                                              :class="item.category_id == 2 ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800'"
                                              x-text="item.category_name"></span>
                                    </div>
                                    <span class="text-amber-700 font-bold text-base whitespace-nowrap" x-text="'$' + (item.price * item.qty).toFixed(2)"></span>
                                </div>

                                <div class="flex items-center justify-between mt-3">
                                    <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                        <button type="button" @click="updateQty(index, -1)"
                                                class="px-3 py-1.5 text-gray-600 hover:bg-amber-50 transition text-sm font-bold cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/></svg>
                                        </button>
                                        <span class="px-4 py-1.5 text-sm font-semibold text-gray-800 bg-white min-w-[2.5rem] text-center" x-text="item.qty"></span>
                                        <button type="button" @click="updateQty(index, 1)"
                                                class="px-3 py-1.5 text-gray-600 hover:bg-amber-50 transition text-sm font-bold cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                        </button>
                                    </div>
                                    <span class="text-xs text-gray-400" x-text="'$' + parseFloat(item.price).toFixed(2) + ' / ' + (item.unit || 'each')"></span>
                                    <button type="button" @click="removeItem(index)"
                                            class="text-rose-500 hover:text-rose-700 transition cursor-pointer p-1" title="Remove item">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md border border-amber-100 p-6">
                <h2 class="text-lg font-bold text-amber-950 mb-4">Order Summary</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal (<span x-text="totalItems()"></span> items)</span>
                        <span class="font-semibold text-gray-800" x-text="'$' + subtotal().toFixed(2)"></span>
                    </div>
                    <div class="border-t border-amber-100 pt-3 flex justify-between text-base">
                        <span class="font-bold text-amber-950">Total</span>
                        <span class="font-bold text-amber-800 text-lg" x-text="'$' + subtotal().toFixed(2)"></span>
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    <a href="<?= url('/checkout') ?>" class="block w-full bg-amber-800 hover:bg-amber-900 text-white font-bold py-3 px-6 rounded-lg shadow transition duration-150 text-sm text-center">
                        Proceed to Checkout
                    </a>
                    <a href="<?= url('/products') ?>" class="block w-full text-center text-amber-800 font-semibold hover:underline text-sm py-2">
                        Continue Shopping
                    </a>
                </div>
            </div>

 
            <div class="text-center">
                <button type="button" @click="clearCart()" class="text-sm text-rose-500 hover:text-rose-700 font-medium transition cursor-pointer">
                    Clear Entire Cart
                </button>
            </div>
        </div>
    </template>
</main>

<script>
function cartPage() {
    return {
        items: JSON.parse(localStorage.getItem('hinabi_cart') || '[]'),

        saveCart() {
            localStorage.setItem('hinabi_cart', JSON.stringify(this.items));
        },

        updateQty(index, delta) {
            const newQty = this.items[index].qty + delta;
            if (newQty < 1) {
                this.removeItem(index);
                return;
            }
            this.items[index].qty = newQty;
            this.saveCart();
        },

        removeItem(index) {
            this.items.splice(index, 1);
            this.saveCart();
        },

        clearCart() {
            this.items = [];
            this.saveCart();
        },

        subtotal() {
            return this.items.reduce((sum, item) => sum + (parseFloat(item.price) * item.qty), 0);
        },

        totalItems() {
            return this.items.reduce((sum, item) => sum + item.qty, 0);
        }
    };
}
</script>
