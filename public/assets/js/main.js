$(document).ready(function() {
    // Mobile navigation menu toggle
    $('#nav-toggle').on('click', function() {
        $('#mobile-menu').slideToggle(300);
    });

    // Products filter buttons (strictly filtered by Database category_id)
    $('.filter-btn').on('click', function() {
        // Read database category id from attribute or data
        const selectedId = String($(this).attr('data-category-id') || $(this).data('categoryId') || $(this).data('category-id') || 'all');

        // Toggle active button style
        $('.filter-btn')
            .removeClass('bg-amber-800 text-white')
            .addClass('bg-gray-200 text-gray-800 hover:bg-gray-300');
        
        $(this)
            .removeClass('bg-gray-200 text-gray-800 hover:bg-gray-300')
            .addClass('bg-amber-800 text-white');

        // Filter product items strictly by numerical category ID
        if (selectedId === 'all') {
            $('.product-item').stop(true, true).fadeIn(250);
        } else {
            $('.product-item').stop(true, true).hide();
            $('.category-' + selectedId).stop(true, true).fadeIn(250);
        }
    });

    $('#price-sort, #rating-sort').on('change', function() {
        const priceSort = $('#price-sort').val();
        const ratingSort = $('#rating-sort').val();
        const products = $('#product-grid').children('.product-item').get();
        const sortComparisons = [];

        if (priceSort === 'low-high') {
            sortComparisons.push((a, b) => Number(a.dataset.price) - Number(b.dataset.price));
        } else if (priceSort === 'high-low') {
            sortComparisons.push((a, b) => Number(b.dataset.price) - Number(a.dataset.price));
        }

        if (ratingSort === 'high-low') {
            sortComparisons.push((a, b) => Number(b.dataset.rating) - Number(a.dataset.rating)
                || Number(b.dataset.reviewCount) - Number(a.dataset.reviewCount));
        } else if (ratingSort === 'low-high') {
            sortComparisons.push((a, b) => Number(a.dataset.rating) - Number(b.dataset.rating));
        } else if (ratingSort === 'most-reviewed') {
            sortComparisons.push((a, b) => Number(b.dataset.reviewCount) - Number(a.dataset.reviewCount)
                || Number(b.dataset.rating) - Number(a.dataset.rating));
        }

        products.sort(function(a, b) {
            for (const compare of sortComparisons) {
                const result = compare(a, b);
                if (result !== 0) return result;
            }
            return Number(a.dataset.defaultOrder) - Number(b.dataset.defaultOrder);
        });

        $('#product-grid').append(products);
    });
});
