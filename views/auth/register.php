<main class="max-w-md mx-auto px-4 py-12 flex-grow w-full">
    <h1 class="text-3xl font-bold text-center text-amber-900 mb-8">Sign Up</h1>

    <form action="<?= url('/register') ?>" method="POST" class="bg-white p-8 rounded-lg shadow-md border border-amber-100 space-y-6">
        <div>
            <label class="block text-gray-700 font-medium mb-2" for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" required class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-amber-600 focus:outline-none">
        </div>
        <div>
            <label class="block text-gray-700 font-medium mb-2" for="email">Email Address</label>
            <input type="email" id="email" name="email" required class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-amber-600 focus:outline-none">
        </div>
        <div>
            <label class="block text-gray-700 font-medium mb-2" for="password">Password (min 6 characters)</label>
            <input type="password" id="password" name="password" minlength="6" required class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-amber-600 focus:outline-none">
        </div>
        <button type="submit" class="w-full bg-amber-800 text-white font-bold py-3 rounded-md hover:bg-amber-900 transition">Create Account</button>
        
        <p class="text-sm text-center text-gray-600">
            Already have an account? 
            <a href="<?= url('/login') ?>" class="text-amber-800 font-semibold hover:underline">Login here</a>
        </p>
    </form>
</main>
