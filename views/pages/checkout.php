<main class="max-w-5xl mx-auto px-4 py-12 flex-grow w-full" x-data="checkoutPage()">
    <div class="text-center max-w-2xl mx-auto mb-10 space-y-2">
        <h1 class="text-3xl md:text-4xl font-extrabold text-amber-950">Checkout</h1>
        <p class="text-gray-600 text-sm">Complete your order details to place your bakery order.</p>
    </div>


    <template x-if="items.length === 0">
        <div class="bg-white rounded-xl shadow-md border border-amber-100 p-12 text-center">
            <svg class="w-16 h-16 text-amber-300 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
            </svg>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Nothing to check out</h2>
            <p class="text-gray-500 text-sm mb-6">Add some treats to your cart first.</p>
            <a href="<?= url('/products') ?>" class="inline-block bg-amber-800 hover:bg-amber-900 text-white font-bold py-3 px-6 rounded-lg shadow transition duration-150 text-sm">
                Browse Menu
            </a>
        </div>
    </template>


    <template x-if="items.length > 0">
        <div class="grid lg:grid-cols-5 gap-8">

            <div class="lg:col-span-3 space-y-6">

               
                <div class="bg-white rounded-xl shadow-md border border-amber-100 p-6">
                    <h2 class="text-lg font-bold text-amber-950 mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                        </svg>
                        Delivery Information
                    </h2>

                    <form id="checkout-form" data-ajax="true" action="<?= url('/checkout') ?>" method="POST" class="space-y-4">

                        <div data-ajax-alert class="hidden"></div>

                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label for="recipient_name" class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Recipient Name</label>
                                <input type="text" id="recipient_name" name="recipient_name" required
                                       value="<?= e($user['full_name'] ?? '') ?>"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-800 focus:border-amber-800 transition">
                            </div>
                            <div>
                                <label for="phone_number" class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Phone Number</label>
                                <input type="tel" id="phone_number" name="phone_number" required placeholder="09XX XXX XXXX"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-800 focus:border-amber-800 transition">
                            </div>
                        </div>

                        <div>
                            <label for="street_address" class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Street Address</label>
                            <input type="text" id="street_address" name="street_address" required placeholder="House/Unit No., Street Name"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-800 focus:border-amber-800 transition">
                        </div>

                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label for="barangay" class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Barangay</label>
                                <input type="text" id="barangay" name="barangay" required placeholder="Barangay Name"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-800 focus:border-amber-800 transition">
                            </div>
                            <div>
                                <label for="city" class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">City / Municipality</label>
                                <input type="text" id="city" name="city" required placeholder="City or Municipality"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-800 focus:border-amber-800 transition">
                            </div>
                        </div>

                        <div>
                            <label for="notes" class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Order Notes (Optional)</label>
                            <textarea id="notes" name="notes" rows="3" placeholder="Special requests, delivery instructions, etc."
                                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-800 focus:border-amber-800 transition resize-none"></textarea>
                        </div>

                        <!-- Hidden fields for cart data -->
                        <input type="hidden" name="cart_items" :value="JSON.stringify(items)">
                        <input type="hidden" name="total_amount" :value="subtotal().toFixed(2)">
                    </form>
                </div>

                <!-- Payment Method -->
                <div class="bg-white rounded-xl shadow-md border border-amber-100 p-6">
                    <h2 class="text-lg font-bold text-amber-950 mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                        </svg>
                        Payment Method
                    </h2>

                    <div class="space-y-3">
                        <label class="flex items-center gap-3 p-4 rounded-lg border cursor-pointer transition"
                               :class="paymentMethod === 'cod' ? 'border-amber-600 bg-amber-50 ring-2 ring-amber-600' : 'border-gray-200 hover:border-amber-300'">
                            <input type="radio" name="payment_method" value="cod" x-model="paymentMethod" class="accent-amber-800" form="checkout-form">
                            <div>
                                <span class="text-sm font-semibold text-gray-800">Cash on Delivery (COD)</span>
                                <p class="text-xs text-gray-500">Pay when your order arrives.</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-4 rounded-lg border cursor-pointer transition"
                               :class="paymentMethod === 'gcash' ? 'border-amber-600 bg-amber-50 ring-2 ring-amber-600' : 'border-gray-200 hover:border-amber-300'">
                            <input type="radio" name="payment_method" value="gcash" x-model="paymentMethod" class="accent-amber-800" form="checkout-form">
                            <div>
                                <span class="text-sm font-semibold text-gray-800">GCash</span>
                                <p class="text-xs text-gray-500">Pay via GCash mobile wallet.</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-4 rounded-lg border cursor-pointer transition"
                               :class="paymentMethod === 'bank' ? 'border-amber-600 bg-amber-50 ring-2 ring-amber-600' : 'border-gray-200 hover:border-amber-300'">
                            <input type="radio" name="payment_method" value="bank" x-model="paymentMethod" class="accent-amber-800" form="checkout-form">
                            <div>
                                <span class="text-sm font-semibold text-gray-800">Bank Transfer</span>
                                <p class="text-xs text-gray-500">Transfer directly to our bank account.</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-md border border-amber-100 p-6 lg:sticky lg:top-24">
                    <h2 class="text-lg font-bold text-amber-950 mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                        </svg>
                        Order Summary
                    </h2>

                    <div class="divide-y divide-amber-50 mb-5 max-h-64 overflow-y-auto">
                        <template x-for="item in items" :key="item.id">
                            <div class="flex items-center gap-3 py-3">
                                <img :src="item.image_url" :alt="item.name" class="w-12 h-12 rounded-lg object-cover border border-amber-100 flex-shrink-0">
                                <div class="flex-grow min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate" x-text="item.name"></p>
                                    <p class="text-xs text-gray-500">Qty: <span x-text="item.qty"></span></p>
                                </div>
                                <span class="text-sm font-bold text-amber-700 whitespace-nowrap" x-text="'$' + (item.price * item.qty).toFixed(2)"></span>
                            </div>
                        </template>
                    </div>

                    <div class="space-y-3 text-sm border-t border-amber-100 pt-4">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal (<span x-text="totalItems()"></span> items)</span>
                            <span class="font-semibold text-gray-800" x-text="'$' + subtotal().toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Delivery Fee</span>
                            <span class="font-semibold text-gray-800">Free</span>
                        </div>
                        <div class="border-t border-amber-100 pt-3 flex justify-between text-base">
                            <span class="font-bold text-amber-950">Total</span>
                            <span class="font-bold text-amber-800 text-lg" x-text="'$' + subtotal().toFixed(2)"></span>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        <button type="submit" form="checkout-form" @click="submitOrder($event)"
                                class="w-full bg-amber-800 hover:bg-amber-900 text-white font-bold py-3 px-6 rounded-lg shadow transition duration-150 text-sm cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="submitting">
                            <span x-show="!submitting">Place Order</span>
                            <span x-show="submitting" class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                                Processing...
                            </span>
                        </button>
                        <a href="<?= url('/cart') ?>" class="block w-full text-center text-amber-800 font-semibold hover:underline text-sm py-2">
                            Back to Cart
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Order Confirmed Modal Overlay -->
    <div x-show="orderConfirmed"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4"
         role="dialog" aria-modal="true" aria-labelledby="order-confirmed-title"
         style="display: none;">
        <div x-show="orderConfirmed"
             x-transition:enter="transition ease-out duration-300 delay-100"
             x-transition:enter-start="opacity-0 scale-90 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="bg-white rounded-2xl shadow-2xl border border-amber-100 max-w-md w-full p-8 text-center">

         
            <div class="w-20 h-20 mx-auto mb-5 bg-emerald-100 rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <h2 id="order-confirmed-title" class="text-2xl font-extrabold text-amber-950 mb-2">Order Complete</h2>
            <p class="text-gray-600 text-sm mb-6" x-text="confirmMessage"></p>

            <div class="bg-amber-50/60 rounded-xl p-4 mb-6 text-left">
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Total Items</span>
                    <span class="font-semibold text-gray-800" x-text="confirmItemCount + ' items'"></span>
                </div>
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Payment</span>
                    <span class="font-semibold text-gray-800" x-text="confirmPayment"></span>
                </div>
                <div class="border-t border-amber-200 mt-2 pt-2 flex justify-between">
                    <span class="font-bold text-amber-950 text-sm">Total Paid</span>
                    <span class="font-bold text-amber-800 text-base" x-text="'$' + confirmTotal"></span>
                </div>
            </div>

   
            <div class="space-y-3">
                <a :href="confirmRedirect"
                   class="block w-full bg-amber-800 hover:bg-amber-900 text-white font-bold py-3 px-6 rounded-lg shadow transition duration-150 text-sm text-center">
                    Back to Home
                </a>
                <a href="<?= url('/products') ?>"
                   class="block w-full text-center text-amber-800 font-semibold hover:underline text-sm py-2">
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>
</main>

<script>
function checkoutPage() {
    return {
        items: JSON.parse(localStorage.getItem('hinabi_cart') || '[]'),
        paymentMethod: 'cod',
        submitting: false,
        orderConfirmed: false,
        confirmMessage: '',
        confirmTotal: '0.00',
        confirmItemCount: 0,
        confirmPayment: '',
        confirmRedirect: '<?= url('/') ?>',

        subtotal() {
            return this.items.reduce((sum, item) => sum + (parseFloat(item.price) * item.qty), 0);
        },

        totalItems() {
            return this.items.reduce((sum, item) => sum + item.qty, 0);
        },

        getPaymentLabel() {
            const labels = { cod: 'Cash on Delivery', gcash: 'GCash', bank: 'Bank Transfer' };
            return labels[this.paymentMethod] || this.paymentMethod;
        },

        showError(alertBox, message) {
            if (alertBox) {
                alertBox.className = 'p-4 mb-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-sm font-medium';
                alertBox.textContent = message;
            }
        },

        submitOrder(e) {
            e.preventDefault();

            if (this.items.length === 0 || this.submitting) {
                return;
            }

            const form = document.getElementById('checkout-form');
            const alertBox = form.querySelector('[data-ajax-alert]');

           
            if (!form.reportValidity()) {
                return;
            }

            if (alertBox) {
                alertBox.className = 'hidden';
                alertBox.textContent = '';
            }

            this.submitting = true;
            const formData = new FormData(form);
            formData.set('cart_items', JSON.stringify(this.items));
            formData.set('total_amount', this.subtotal().toFixed(2));
            formData.set('payment_method', this.paymentMethod);

            const savedTotal = this.subtotal().toFixed(2);
            const savedCount = this.totalItems();
            const savedPayment = this.getPaymentLabel();

            $.ajax({
                url: form.action,
                type: 'POST',
                data: Object.fromEntries(formData),
                dataType: 'json',
                success: (res) => {
                    if (res.success) {
                        localStorage.removeItem('hinabi_cart');
                        window.dispatchEvent(new Event('storage'));

                        this.confirmMessage = res.message || 'Your order has been placed successfully. We will start preparing your treats right away.';
                        this.confirmTotal = savedTotal;
                        this.confirmItemCount = savedCount;
                        this.confirmPayment = savedPayment;
                        this.confirmRedirect = res.redirect || '<?= url('/') ?>';
                        this.orderConfirmed = true;
                        document.body.classList.add('overflow-hidden');
                    } else {
                        this.showError(alertBox, res.message || res.error || 'Something went wrong. Please try again.');
                        this.submitting = false;
                    }
                },
                error: (xhr) => {
           
                    const res = xhr.responseJSON || {};
                    if (xhr.status === 401) {
                        window.location.href = '<?= url('/login') ?>';
                        return;
                    }
                    this.showError(alertBox, res.message || res.error || 'Connection error. Please check your internet and try again.');
                    this.submitting = false;
                }
            });
        }
    };
}
</script>
