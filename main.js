$(document).ready(function() {
    $('#nav-toggle').on('click', function() {
        $('#mobile-menu').slideToggle(300);
    });

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