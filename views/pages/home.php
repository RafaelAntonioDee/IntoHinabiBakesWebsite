<main class="flex-grow">
    <section class="relative bg-amber-950 text-white overflow-hidden py-20 md:py-28">
        <div class="absolute inset-0 z-0 opacity-40">
            <img src="https://images.unsplash.com/photo-1558961363-fa8fdf82db35?auto=format&fit=crop&w=1600&q=80" alt="Freshly baked cookies" class="w-full h-full object-cover">
        </div>
        
        <div class="absolute inset-0 bg-gradient-to-r from-amber-950 via-amber-950/80 to-transparent z-0"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 md:px-12">
            <div class="max-w-2xl space-y-6">
                <h1 class="text-4xl md:text-6xl font-black text-amber-50 tracking-tight leading-tight">
                    Freshly Baked Gourmet Cookies
                </h1>
                <p class="text-amber-100/90 text-lg md:text-xl font-normal leading-relaxed">
                    Handcrafted, chewy, gooey, and crisp gourmet cookies baked daily with premium ingredients.
                </p>
                <div class="pt-2">
                    <a href="<?= url('/products') ?>" class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-bold text-base px-8 py-3.5 rounded-xl shadow-lg hover:shadow-xl transition duration-200">
                        Explore Cookie Flavors
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-16 md:py-20">
        <div class="grid md:grid-cols-3 gap-8 text-center">
            
            <div class="bg-white p-8 rounded-2xl border border-amber-100 shadow-sm hover:shadow-md transition flex flex-col items-center">
                <div class="w-14 h-14 bg-amber-100 text-amber-800 rounded-2xl flex items-center justify-center mb-4 shadow-sm">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.5 9h.01M15.5 9h.01M12 13h.01M9 15.5h.01M14.5 15h.01"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-amber-950 mb-2">Classic Warm Cookies</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Golden-brown chocolate chip, oatmeal raisin, and double chocolate chunk cookies baked fresh hourly.
                </p>
            </div>

            <div class="bg-white p-8 rounded-2xl border border-amber-100 shadow-sm hover:shadow-md transition flex flex-col items-center">
                <div class="w-14 h-14 bg-rose-100 text-rose-800 rounded-2xl flex items-center justify-center mb-4 shadow-sm">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 0A2.25 2.25 0 109.75 4.5a2.25 2.25 0 002.25 2.25zm0 0A2.25 2.25 0 1114.25 4.5 2.25 2.25 0 0112 6.75zm-7.5 7.5h15M4.5 18.75h15M3 10.5h18a1.5 1.5 0 011.5 1.5v6.75a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18.75V12a1.5 1.5 0 011.5-1.5z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-amber-950 mb-2">Stuffed & Specialty</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Decadent cookies stuffed with Nutella, caramel, peanut butter, and cheesecake fillings.
                </p>
            </div>

            <div class="bg-white p-8 rounded-2xl border border-amber-100 shadow-sm hover:shadow-md transition flex flex-col items-center">
                <div class="w-14 h-14 bg-amber-100 text-amber-900 rounded-2xl flex items-center justify-center mb-4 shadow-sm">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H4.5a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-amber-950 mb-2">Cookie Boxes & Gifts</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Custom cookie tins and gift boxes perfect for birthday parties, holidays, and events.
                </p>
            </div>

        </div>
    </section>
</main>
