<main class="max-w-md mx-auto px-4 py-12 flex-grow w-full">
    <h1 class="text-3xl font-bold text-center text-amber-900 mb-8">Sign Up</h1>

    <form action="<?= url('/register') ?>" method="POST" data-ajax="true" novalidate class="bg-white p-8 rounded-lg shadow-md border border-amber-100 space-y-5">
        <div data-ajax-alert class="hidden"></div>
        <div>
            <label class="block text-gray-700 font-medium mb-2" for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" required placeholder="e.g. Juan Dela Cruz" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-amber-600 focus:outline-none transition">
        </div>
        <div>
            <label class="block text-gray-700 font-medium mb-2" for="email">Email Address</label>
            <input type="email" id="email" name="email" required placeholder="e.g. juan@example.com" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-amber-600 focus:outline-none transition">
        </div>
        <div>
            <label class="block text-gray-700 font-medium mb-2" for="phone_number">Phone Number</label>
            <input type="tel" id="phone_number" name="phone_number" inputmode="tel" minlength="10" maxlength="16" pattern="(09[\d\s\-]{9,12}|(\+?63)9[\d\s\-]{9,12}|0[2-8][\d\s\-()]{7,12})" title="Enter a valid PH mobile (11 digits, e.g. 09171234567 or +639171234567) or landline number" required placeholder="e.g. 0917 123 4567" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-amber-600 focus:outline-none transition">
        </div>
        <div>
            <label class="block text-gray-700 font-medium mb-2" for="password">Password (min 6 characters)</label>
            <input type="password" id="password" name="password" minlength="6" required placeholder="At least 6 characters" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-amber-600 focus:outline-none transition">
        </div>
        <button type="submit" class="w-full bg-amber-800 text-white font-bold py-3 rounded-md hover:bg-amber-900 transition">Create Account</button>
        
        <p class="text-sm text-center text-gray-600">
            Already have an account? 
            <a href="<?= url('/login') ?>" class="text-amber-800 font-semibold hover:underline">Login here</a>
        </p>
    </form>
</main>
