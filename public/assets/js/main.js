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

    // Reusable jQuery AJAX Form Handler (preserves inputs, highlights per-field errors, no full page refresh)
    const ERROR_INPUT_CLASSES = 'border-rose-500 ring-2 ring-rose-200 focus:ring-rose-500 focus:border-rose-500';

    function clearFieldErrors($form) {
        $form.find('[data-field-error]').remove();
        $form.find('input, textarea, select').removeClass(ERROR_INPUT_CLASSES);
        const targetSelector = $form.data('alert-target');
        const $box = targetSelector ? $(targetSelector) : $form.find('[data-ajax-alert]').first();
        if ($box.length) {
            $box.addClass('hidden').empty();
        }
    }

    function renderFieldErrors($form, errors) {
        $.each(errors, function(fieldName, message) {
            const $input = $form.find('[name="' + fieldName + '"]').first();
            if ($input.length) {
                $input.addClass(ERROR_INPUT_CLASSES);
                $('<p data-field-error class="mt-1.5 text-xs font-medium text-rose-600"></p>')
                    .text(message)
                    .insertAfter($input);
            } else {
                renderAjaxAlert($form, 'error', message);
            }
        });
    }

    function renderAjaxAlert($form, type, message) {
        const targetSelector = $form.data('alert-target');
        let $box = targetSelector ? $(targetSelector) : $form.find('[data-ajax-alert]').first();
        if (!$box.length) {
            $box = $('<div data-ajax-alert class="mb-4"></div>').prependTo($form);
        }

        const isError = type === 'error';
        const classes = isError
            ? 'p-3.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-sm font-medium'
            : 'p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium';

        $box
            .stop(true, true)
            .removeClass('hidden')
            .html($('<div>').addClass(classes).text(message))
            .fadeIn(150);
    }

    $(document).on('input change', 'form[data-ajax="true"] input, form[data-ajax="true"] textarea, form[data-ajax="true"] select', function() {
        const $input = $(this);
        $input.removeClass(ERROR_INPUT_CLASSES);
        $input.next('[data-field-error]').remove();
    });

    $(document).on('submit', 'form[data-ajax="true"]', function(e) {
        e.preventDefault();
        const $form = $(this);
        if ($form.data('submitting')) return;

        const confirmMsg = $form.data('confirm');
        if (confirmMsg && !window.confirm(confirmMsg)) {
            return;
        }

        clearFieldErrors($form);

        const $submitBtn = $form.find('button[type="submit"]').first();
        const originalBtnText = $submitBtn.text();
        const loadingText = $submitBtn.data('loading-text') || 'Saving...';

        $form.data('submitting', true);
        $submitBtn.prop('disabled', true).addClass('opacity-60 cursor-not-allowed').text(loadingText);

        $.ajax({
            url: $form.attr('action'),
            method: ($form.attr('method') || 'POST').toUpperCase(),
            data: $form.serialize(),
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .done(function(res) {
            if (res && res.success) {
                if (res.redirect) {
                    window.location.href = res.redirect;
                    return;
                }
                if (res.message) {
                    renderAjaxAlert($form, 'success', res.message);
                }
                if ($form.data('reset-on-success')) {
                    $form[0].reset();
                }
                $form.trigger('ajax:success', [res]);
            } else if (res && res.errors && Object.keys(res.errors).length > 0) {
                renderFieldErrors($form, res.errors);
            } else {
                renderAjaxAlert($form, 'error', (res && res.error) ? res.error : 'Something went wrong. Please try again.');
            }
        })
        .fail(function(xhr) {
            const json = xhr.responseJSON;
            if (json && json.errors && Object.keys(json.errors).length > 0) {
                renderFieldErrors($form, json.errors);
            } else {
                const errMsg = (json && json.error) ? json.error : 'Could not connect to server. Please try again.';
                renderAjaxAlert($form, 'error', errMsg);
            }
        })
        .always(function() {
            $form.data('submitting', false);
            $submitBtn.prop('disabled', false).removeClass('opacity-60 cursor-not-allowed').text(originalBtnText);
        });
    });
});
