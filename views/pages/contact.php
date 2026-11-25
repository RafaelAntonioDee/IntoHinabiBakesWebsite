<?php
    $inquiryItem = trim($_GET['item'] ?? '');
    $initialMessage = old('message') ?: ($inquiryItem !== '' ? "Hello! I would like to inquire about ordering: {$inquiryItem}." : '');
?>
<main class="max-w-5xl mx-auto px-4 py-12 flex-grow w-full">
    <div class="text-center max-w-xl mx-auto mb-10 space-y-2">
        <h1 class="text-3xl md:text-4xl font-extrabold text-amber-950">Get In Touch</h1>
        <p class="text-gray-600 text-sm">Have a question, catering request, or custom cookie order? Send us a message below.</p>
    </div>

    <div class="grid md:grid-cols-3 gap-8 items-start">
        <form action="<?= url('/contact') ?>" method="POST" class="md:col-span-2 bg-white p-6 md:p-8 rounded-xl shadow-sm border border-amber-200/80 space-y-5">
            <div>
                <label class="block text-gray-800 font-semibold text-sm mb-1.5" for="name">Full Name</label>
                <input type="text" id="name" name="name" required value="<?= e(old('name')) ?>" placeholder="Jane Doe" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-800 focus:border-amber-800 transition">
            </div>

            <div>
                <label class="block text-gray-800 font-semibold text-sm mb-1.5" for="email">Email Address</label>
                <input type="email" id="email" name="email" required value="<?= e(old('email')) ?>" placeholder="jane@example.com" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-800 focus:border-amber-800 transition">
            </div>

            <div>
                <label class="block text-gray-800 font-semibold text-sm mb-1.5" for="message">Message / Custom Cookie Order</label>
                <textarea id="message" name="message" rows="5" required placeholder="Tell us about your order or inquiry..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-800 focus:border-amber-800 transition"><?= e($initialMessage) ?></textarea>
            </div>

            <button type="submit" class="w-full bg-amber-800 hover:bg-amber-900 text-white font-bold py-3 px-6 rounded-lg shadow transition duration-150 text-sm cursor-pointer">
                Send Message
            </button>
        </form>

        <aside class="bg-amber-900 text-amber-50 p-6 md:p-8 rounded-xl shadow-sm space-y-6">
            <h2 class="text-xl font-bold text-amber-100">Bakery Details</h2>

            <div class="space-y-4 text-sm">
                <div class="flex items-start space-x-3">
                    <div class="p-2 bg-amber-800/80 rounded-lg text-amber-200 flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-amber-100">Location</p>
                        <p class="text-amber-200/80 leading-relaxed">Biñan City, Laguna, Philippines</p>
                    </div>
                </div>

                <div class="flex items-start space-x-3">
                    <div class="p-2 bg-amber-800/80 rounded-lg text-amber-200 flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-amber-100">Opening Hours</p>
                        <p class="text-amber-200/80">Open Daily<br>8:00 AM – 8:00 PM</p>
                    </div>
                </div>

                <div class="flex items-start space-x-3">
                    <div class="p-2 bg-amber-800/80 rounded-lg text-amber-200 flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-amber-100">Direct Contact</p>
                        <p class="text-amber-200/80">hello@sweetcookieoven.com</p>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-amber-800/80 text-xs text-amber-300/80">
                <p>For urgent same-day orders, please visit our store directly.</p>
            </div>
        </aside>
    </div>
</main>
