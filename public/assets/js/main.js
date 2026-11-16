$(document).ready(function() {
    // Mobile navigation menu toggle
    $('#nav-toggle').on('click', function() {
        $('#mobile-menu').slideToggle(300);
    });

    // Products filter buttons
    $('.filter-btn').on('click', function() {
        const category = $(this).data('filter');

        // Toggle active button style
        $('.filter-btn')
            .removeClass('bg-amber-800 text-white')
            .addClass('bg-gray-200 text-gray-800 hover:bg-gray-300');
        
        $(this)
            .removeClass('bg-gray-200 text-gray-800 hover:bg-gray-300')
            .addClass('bg-amber-800 text-white');

        // Filter product items
        if (category === 'all') {
            $('.product-item').stop(true, true).fadeIn(300);
        } else {
            $('.product-item').stop(true, true).hide();
            $('.' + category).stop(true, true).fadeIn(300);
        }
    });
});
