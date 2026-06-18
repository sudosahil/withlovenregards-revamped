/* =========================================================================
   WithLoveNRegards — main frontend JS (jQuery 3.7.1 + jQuery UI 1.13.2)
   Hero slider · mobile nav · cart/wishlist AJAX · filters URL state ·
   datepicker (5PM IST cutoff) · FAQ · live search · carousels
   ========================================================================= */
(function ($) {
    'use strict';

    var WLNR = window.WLNR || {};
    var API = WLNR.apiUrl || '/api';

    /* ---------------------------------------------------------------------
       Mobile offcanvas nav
    --------------------------------------------------------------------- */
    function openNav() {
        $('#mobileNav').addClass('open').attr('aria-hidden', 'false');
        $('#offcanvasBackdrop').prop('hidden', false);
        $('#navToggle').attr('aria-expanded', 'true');
        $('body').css('overflow', 'hidden');
    }
    function closeNav() {
        $('#mobileNav').removeClass('open').attr('aria-hidden', 'true');
        $('#offcanvasBackdrop').prop('hidden', true);
        $('#navToggle').attr('aria-expanded', 'false');
        $('body').css('overflow', '');
    }
    $('#navToggle').on('click', openNav);
    $('#navClose, #offcanvasBackdrop').on('click', closeNav);
    $('.mobile-nav__expander').on('click', function () {
        var $btn = $(this);
        var $sub = $('#' + $btn.data('target'));
        var open = $btn.attr('aria-expanded') === 'true';
        $btn.attr('aria-expanded', String(!open));
        $sub.prop('hidden', open);
    });

    /* ---------------------------------------------------------------------
       Account dropdown
    --------------------------------------------------------------------- */
    $('#accountToggle').on('click', function (e) {
        e.stopPropagation();
        $('#accountDropdown').toggleClass('open');
    });
    $(document).on('click', function () { $('#accountDropdown').removeClass('open'); });

    /* ---------------------------------------------------------------------
       Hero slider
    --------------------------------------------------------------------- */
    (function heroSlider() {
        var $hero = $('.hero');
        if (!$hero.length) return;
        var $track = $hero.find('.hero__track');
        var $slides = $hero.find('.hero__slide');
        var count = $slides.length;
        if (count < 2) return;
        var index = 0, timer;

        function go(i) {
            index = (i + count) % count;
            $track.css('transform', 'translateX(' + (-index * 100) + '%)');
            $hero.find('.hero__dot').removeClass('active').eq(index).addClass('active');
        }
        function next() { go(index + 1); }
        function start() { timer = setInterval(next, 5000); }
        function stop() { clearInterval(timer); }

        $hero.find('.hero__arrow--next').on('click', function () { stop(); next(); start(); });
        $hero.find('.hero__arrow--prev').on('click', function () { stop(); go(index - 1); start(); });
        $hero.on('click', '.hero__dot', function () { stop(); go($(this).index()); start(); });
        $hero.on('mouseenter', stop).on('mouseleave', start);
        go(0); start();
    })();

    /* ---------------------------------------------------------------------
       Generic carousels (.carousel)
    --------------------------------------------------------------------- */
    $('.carousel').each(function () {
        var $c = $(this);
        var $track = $c.find('.carousel__track');
        var offset = 0;
        function step() { return $track.children().first().outerWidth(true) || 0; }
        function maxOffset() {
            var visible = $c.find('.carousel__viewport').width();
            return Math.max(0, $track[0].scrollWidth - visible);
        }
        function update() {
            offset = Math.max(0, Math.min(offset, maxOffset()));
            $track.css('transform', 'translateX(' + (-offset) + 'px)');
            $c.find('.carousel__arrow--prev').prop('disabled', offset <= 0);
            $c.find('.carousel__arrow--next').prop('disabled', offset >= maxOffset() - 1);
        }
        $c.find('.carousel__arrow--next').on('click', function () { offset += step() * 2; update(); });
        $c.find('.carousel__arrow--prev').on('click', function () { offset -= step() * 2; update(); });
        $(window).on('resize', update);
        update();
    });

    /* ---------------------------------------------------------------------
       FAQ accordion
    --------------------------------------------------------------------- */
    $('.faq-item__q').on('click', function () {
        $(this).closest('.faq-item').toggleClass('open');
    });

    /* ---------------------------------------------------------------------
       Cart AJAX
    --------------------------------------------------------------------- */
    function refreshCartCount() {
        $.getJSON(API + '/cart.php?action=count').done(function (res) {
            if (res && res.data) $('#cartCount').text(res.data.count);
        });
    }

    $(document).on('click', '[data-add-cart]', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var id = $btn.data('add-cart');
        var qty = parseInt($('#qtyInput').val(), 10) || 1;
        $btn.prop('disabled', true);
        $.post(API + '/cart.php', { action: 'add', product_id: id, qty: qty, csrf_token: WLNR.csrfToken }, null, 'json')
            .done(function (res) {
                if (res.data) {
                    $('#cartCount').text(res.data.count);
                    toast('Added to cart');
                } else {
                    toast((res.error && res.error.message) || 'Could not add to cart', true);
                }
            })
            .fail(function () { toast('Network error', true); })
            .always(function () { $btn.prop('disabled', false); });
    });

    $(document).on('change', '[data-cart-qty]', function () {
        var id = $(this).data('cart-qty');
        var qty = parseInt($(this).val(), 10) || 1;
        $.post(API + '/cart.php', { action: 'update', product_id: id, qty: qty, csrf_token: WLNR.csrfToken }, null, 'json')
            .done(function () { window.location.reload(); });
    });

    $(document).on('click', '[data-cart-remove]', function (e) {
        e.preventDefault();
        var id = $(this).data('cart-remove');
        $.post(API + '/cart.php', { action: 'remove', product_id: id, csrf_token: WLNR.csrfToken }, null, 'json')
            .done(function () { window.location.reload(); });
    });

    /* ---------------------------------------------------------------------
       Wishlist AJAX
    --------------------------------------------------------------------- */
    $(document).on('click', '[data-wishlist]', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var id = $btn.data('wishlist');
        $.post(API + '/wishlist.php', { action: 'toggle', product_id: id, csrf_token: WLNR.csrfToken }, null, 'json')
            .done(function (res) {
                if (res.data) {
                    $('#wishlistCount').text(res.data.count);
                    $btn.toggleClass('active', res.data.added);
                    toast(res.data.added ? 'Added to wishlist' : 'Removed from wishlist');
                }
            });
    });

    /* ---------------------------------------------------------------------
       Quantity stepper
    --------------------------------------------------------------------- */
    $(document).on('click', '[data-step]', function () {
        var $input = $(this).closest('.qty-stepper').find('input');
        var val = parseInt($input.val(), 10) || 1;
        val += parseInt($(this).data('step'), 10);
        if (val < 1) val = 1;
        $input.val(val).trigger('change');
    });

    /* ---------------------------------------------------------------------
       Delivery datepicker — 5PM IST cutoff resolved server-side.
       WLNR.earliestDeliveryDate is the first selectable date (Y-m-d).
    --------------------------------------------------------------------- */
    if ($.fn.datepicker) {
        var min = WLNR.earliestDeliveryDate ? new Date(WLNR.earliestDeliveryDate + 'T00:00:00') : 0;
        $('.js-datepicker').datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: min,
            maxDate: '+60d',
            beforeShow: function () { return true; }
        });
        if (!WLNR.sameDayAvailable) {
            $('.cutoff-note').show();
        }
    }

    /* ---------------------------------------------------------------------
       Category filters — persist state in the URL
    --------------------------------------------------------------------- */
    (function filters() {
        var $form = $('#filterForm');
        if (!$form.length) return;

        // Apply filters by writing them to the query string and reloading.
        function apply() {
            var params = new URLSearchParams(window.location.search);
            // Preserve category/subcategory routing params if present.
            ['price_min', 'price_max', 'sort'].forEach(function (k) { params.delete(k); });
            params.delete('type');

            var pmin = $('#priceMin').val(), pmax = $('#priceMax').val();
            if (pmin) params.set('price_min', pmin);
            if (pmax) params.set('price_max', pmax);

            var sort = $('#sortSelect').val();
            if (sort) params.set('sort', sort);

            var types = $form.find('input[name="type"]:checked').map(function () { return this.value; }).get();
            if (types.length) params.set('type', types.join(','));

            window.location.search = params.toString();
        }

        $('#applyFilters').on('click', apply);
        $('#sortSelect').on('change', apply);
        $('#clearFilters').on('click', function () {
            var params = new URLSearchParams(window.location.search);
            ['price_min', 'price_max', 'sort', 'type'].forEach(function (k) { params.delete(k); });
            window.location.search = params.toString();
        });

        // Mobile filter toggle
        $('#filterToggle').on('click', function () {
            $('.filter-sidebar').toggleClass('open').toggle();
        });
    })();

    /* ---------------------------------------------------------------------
       Live header search (AJAX suggestions)
    --------------------------------------------------------------------- */
    (function liveSearch() {
        var $input = $('#headerSearchInput');
        var $results = $('#headerSearchResults');
        if (!$input.length) return;
        var t;
        $input.on('input', function () {
            var q = $.trim($(this).val());
            clearTimeout(t);
            if (q.length < 2) { $results.prop('hidden', true).empty(); return; }
            t = setTimeout(function () {
                $.getJSON(API + '/search.php', { q: q }).done(function (res) {
                    var items = (res.data && res.data.results) || [];
                    if (!items.length) { $results.prop('hidden', true).empty(); return; }
                    var html = items.slice(0, 6).map(function (p) {
                        return '<a href="' + p.url + '"><img src="' + WLNR.baseUrl + p.image +
                               '" alt="" width="42" height="42"><span>' + p.name +
                               '<br><small>' + WLNR.currency + ' ' + p.price + '</small></span></a>';
                    }).join('');
                    $results.html(html).prop('hidden', false);
                });
            }, 220);
        });
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.header-search').length) $results.prop('hidden', true);
        });
    })();

    /* ---------------------------------------------------------------------
       Auth tabs (login / register)
    --------------------------------------------------------------------- */
    function activateAuthTab(name) {
        $('.auth-tab').removeClass('active').filter('[data-tab="' + name + '"]').addClass('active');
        $('.auth-panel').removeClass('active').filter('#panel-' + name).addClass('active');
    }
    $('.auth-tab').on('click', function () { activateAuthTab($(this).data('tab')); });
    if (window.location.hash === '#register') activateAuthTab('register');
    else if ($('.auth-tab').length) activateAuthTab('login');

    /* ---------------------------------------------------------------------
       Toast
    --------------------------------------------------------------------- */
    function toast(msg, isError) {
        var $t = $('<div class="wlnr-toast"></div>').text(msg);
        if (isError) $t.addClass('wlnr-toast--error');
        $('body').append($t);
        requestAnimationFrame(function () { $t.addClass('show'); });
        setTimeout(function () { $t.removeClass('show'); setTimeout(function () { $t.remove(); }, 300); }, 2400);
    }
    window.wlnrToast = toast;

    // Toast styling injected once.
    $('<style>.wlnr-toast{position:fixed;bottom:84px;left:50%;transform:transl(-50%,20px);' +
      'background:#2c2c2c;color:#fff;padding:12px 22px;border-radius:30px;font-size:.9rem;z-index:500;' +
      'opacity:0;transition:.3s;transform:translateX(-50%) translateY(20px)}' +
      '.wlnr-toast.show{opacity:1;transform:translateX(-50%) translateY(0)}' +
      '.wlnr-toast--error{background:#d23c3c}</style>').appendTo('head');

})(jQuery);
