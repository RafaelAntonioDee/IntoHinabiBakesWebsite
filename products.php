<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cookies & Treats - Sweet Cookie Oven</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="custom.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body class="flex flex-col min-h-screen bg-amber-50/20">

    <?php include 'header.php'; ?>

    <main class="max-w-7xl mx-auto px-4 py-12 flex-grow">
        <h1 class="text-3xl font-bold text-center text-amber-900 mb-8">Our Fresh Cookie Menu</h1>

        <div class="flex justify-center space-x-3 mb-10 flex-wrap gap-y-3">
            <button class="filter-btn bg-amber-800 text-white px-6 py-2 rounded-full font-medium transition cursor-pointer shadow-sm" data-filter="all">
                All Cookies
            </button>
            <button class="filter-btn bg-gray-200 text-gray-800 hover:bg-gray-300 px-6 py-2 rounded-full font-medium transition cursor-pointer shadow-sm" data-filter="classic">
                Classics
            </button>
            <button class="filter-btn bg-gray-200 text-gray-800 hover:bg-gray-300 px-6 py-2 rounded-full font-medium transition cursor-pointer shadow-sm" data-filter="stuffed">
                Stuffed & Deluxe
            </button>
            <button class="filter-btn bg-gray-200 text-gray-800 hover:bg-gray-300 px-6 py-2 rounded-full font-medium transition cursor-pointer shadow-sm" data-filter="box">
                Cookie Boxes
            </button>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            
            <div class="product-item classic bg-white rounded-xl shadow-md overflow-hidden border border-amber-100 flex flex-col">
                <img src="https://images.unsplash.com/photo-1558961363-fa8fdf82db35?auto=format&fit=crop&w=600&q=80" alt="Classic Chocolate Chunk" class="w-full h-52 object-cover">
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Classic Chocolate Chunk</h3>
                    <p class="text-gray-600 mb-4 flex-grow">Crispy edges, gooey center packed with dark Belgian chocolate chunks.</p>
                    <span class="text-amber-700 font-bold text-lg">$3.50 / each</span>
                </div>
            </div>

            <div class="product-item stuffed bg-white rounded-xl shadow-md overflow-hidden border border-amber-100 flex flex-col">
                <img src="https://images.unsplash.com/photo-1618923850107-d1a234d7a73a?auto=format&fit=crop&w=600&q=80" alt="Nutella Lava Cookie" class="w-full h-52 object-cover">
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Nutella Lava Cookie</h3>
                    <p class="text-gray-600 mb-4 flex-grow">Rich hazelnut cocoa dough stuffed with a warm, melted Nutella center.</p>
                    <span class="text-amber-700 font-bold text-lg">$4.50 / each</span>
                </div>
            </div>

            <div class="product-item classic bg-white rounded-xl shadow-md overflow-hidden border border-amber-100 flex flex-col">
                <img src="https://images.unsplash.com/photo-1582623201359-74e936ed48c4?fm=jpg&q=60&w=3000&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTB8fGNob2NvbGF0ZSUyMGNvb2tpZXxlbnwwfHwwfHx8MA%3D%3D" alt="Double Chocolate Fudge" class="w-full h-52 object-cover">
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Double Chocolate Fudge</h3>
                    <p class="text-gray-600 mb-4 flex-grow">Decadent cocoa dough loaded with white chocolate chips and fudge bites.</p>
                    <span class="text-amber-700 font-bold text-lg">$3.75 / each</span>
                </div>
            </div>

            <div class="product-item classic bg-white rounded-xl shadow-md overflow-hidden border border-amber-100 flex flex-col">
                <img src="https://www.eatingwell.com/thmb/rzJMZGIDf6XKf3-V24fqTXBu_u0=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/45684841-d040ba276fd24b599d44ebfb0b8a7696.jpg" alt="Oatmeal Cinnamon Raisin" class="w-full h-52 object-cover">
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Oatmeal Cinnamon Raisin</h3>
                    <p class="text-gray-600 mb-4 flex-grow">Hearty rolled oats baked with sweet cinnamon, brown sugar, and plump raisins.</p>
                    <span class="text-amber-700 font-bold text-lg">$3.25 / each</span>
                </div>
            </div>

            <div class="product-item classic bg-white rounded-xl shadow-md overflow-hidden border border-amber-100 flex flex-col">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTSJO6ZZfrgYicBFkKN49OQnKUwpcmO-IQXpeWdcIaFZXjTI82zy3Wz4uuP&s=10" alt="Snickerdoodle Cinnamon Crisp" class="w-full h-52 object-cover">
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Snickerdoodle Crisp</h3>
                    <p class="text-gray-600 mb-4 flex-grow">Soft buttery sugar cookie rolled in aromatic cinnamon sugar and baked to perfection.</p>
                    <span class="text-amber-700 font-bold text-lg">$3.25 / each</span>
                </div>
            </div>

            <div class="product-item stuffed bg-white rounded-xl shadow-md overflow-hidden border border-amber-100 flex flex-col">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTohyTO2mWs-38YpkrLoaMUTs9D9fhbdO-BzgYrq0gKJL2Yc8tfracMyfg&s=10" alt="Red Velvet Cheesecake" class="w-full h-52 object-cover">
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Red Velvet Cheesecake</h3>
                    <p class="text-gray-600 mb-4 flex-grow">Soft red velvet cookie filled with a rich cream cheese center.</p>
                    <span class="text-amber-700 font-bold text-lg">$4.75 / each</span>
                </div>
            </div>

            <div class="product-item stuffed bg-white rounded-xl shadow-md overflow-hidden border border-amber-100 flex flex-col">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSHR4JCHe1wdiz2dkRR9UL5eg5WJc0yyDKgUyiRE1XNdpo7r_HCb8Y6WOc&s=10" alt="Salted Caramel Peanut Butter" class="w-full h-52 object-cover">
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Salted Caramel Stuffed</h3>
                    <p class="text-gray-600 mb-4 flex-grow">Peanut butter dough stuffed with gooey melted salted caramel and topped with sea salt.</p>
                    <span class="text-amber-700 font-bold text-lg">$4.85 / each</span>
                </div>
            </div>

            <div class="product-item stuffed bg-white rounded-xl shadow-md overflow-hidden border border-amber-100 flex flex-col">
                <img src="https://www.sixsistersstuff.com/wp-content/uploads/2022/09/Cookies-and-Cream-Cookies-1.jpg" alt="Cookies & Cream Fudge Stuffed" class="w-full h-52 object-cover">
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Cookies & Cream Delight</h3>
                    <p class="text-gray-600 mb-4 flex-grow">Crushed Oreo cookie dough surrounding a white chocolate truffle core.</p>
                    <span class="text-amber-700 font-bold text-lg">$4.95 / each</span>
                </div>
            </div>

            <div class="product-item box bg-white rounded-xl shadow-md overflow-hidden border border-amber-100 flex flex-col">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTiyOsTmfitGQVDKmFqahJr4Z6YD13UIfR5HQgtcZ0MQlt5mU-1Qmyh6899&s=10" alt="Baker's Dozen Variety Box" class="w-full h-52 object-cover">
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Baker's Dozen Variety Box</h3>
                    <p class="text-gray-600 mb-4 flex-grow">A custom gift box featuring 12 of our top-selling seasonal and classic cookies.</p>
                    <span class="text-amber-700 font-bold text-lg">$38.00 / box</span>
                </div>
            </div>

            <div class="product-item box bg-white rounded-xl shadow-md overflow-hidden border border-amber-100 flex flex-col">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSkhfi9AbibQvPt7o1ClEjHEIL8DT-x9RhH3BXGBguFoAOa66q_WebjUCAh&s=10" alt="Party Sampler Box" class="w-full h-52 object-cover">
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Party Sampler Box</h3>
                    <p class="text-gray-600 mb-4 flex-grow">24 mini cookies perfect for events, office gatherings, and birthday parties.</p>
                    <span class="text-amber-700 font-bold text-lg">$45.00 / box</span>
                </div>
            </div>

            <div class="product-item box bg-white rounded-xl shadow-md overflow-hidden border border-amber-100 flex flex-col">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQtDZuxHEMVWp5XTIA4jYwAnWNlvilGEWE04Zd431BNAIO-vFm_YbGkw4U&s=10" alt="Deluxe Stuffed Gift Tin" class="w-full h-52 object-cover">
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Deluxe Stuffed Gift Tin</h3>
                    <p class="text-gray-600 mb-4 flex-grow">An assortment of 8 large stuffed cookies packaged in an elegant signature tin.</p>
                    <span class="text-amber-700 font-bold text-lg">$34.00 / tin</span>
                </div>
            </div>

        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script>
        $(document).ready(function() {
            $('.filter-btn').on('click', function() {
                const category = $(this).data('filter');

                $('.filter-btn')
                    .removeClass('bg-amber-800 text-white')
                    .addClass('bg-gray-200 text-gray-800 hover:bg-gray-300');
                
                $(this)
                    .removeClass('bg-gray-200 text-gray-800 hover:bg-gray-300')
                    .addClass('bg-amber-800 text-white');

                if (category === 'all') {
                    $('.product-item').stop(true, true).fadeIn(300);
                } else {
                    $('.product-item').stop(true, true).hide();
                    $('.' + category).stop(true, true).fadeIn(300);
                }
            });
        });
    </script>
</body>
</html>