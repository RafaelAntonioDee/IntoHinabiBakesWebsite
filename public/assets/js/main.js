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
});
