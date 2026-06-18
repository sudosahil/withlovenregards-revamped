/* =========================================================================
   WithLoveNRegards — admin panel JS
   Sidebar toggle · Chart.js dashboards · inline edits · bulk actions
   Chart.js is loaded via CDN on pages that render charts.
   ========================================================================= */
(function ($) {
    'use strict';

    /* Sidebar toggle (mobile) */
    $('#adminMenuToggle').on('click', function () {
        $('#adminSidebar').toggleClass('open');
    });
    $(document).on('click', function (e) {
        if ($(window).width() < 992 &&
            !$(e.target).closest('#adminSidebar, #adminMenuToggle').length) {
            $('#adminSidebar').removeClass('open');
        }
    });

    /* Bulk order status: select-all checkbox */
    $('#bulkSelectAll').on('change', function () {
        $('.row-check').prop('checked', this.checked);
    });

    /* Inline price edit (products list) */
    $('.inline-price').on('blur', function () {
        var $cell = $(this);
        var id = $cell.data('product');
        var val = $cell.text().replace(/[^\d.]/g, '');
        $cell.addClass('saving');
        // Placeholder persistence — wire to api/admin endpoint when DB is live.
        setTimeout(function () { $cell.removeClass('saving').addClass('saved'); }, 400);
        console.log('Inline price update', id, val);
    });

    /* Auto-slug from product name */
    $('#productName').on('input', function () {
        var $slug = $('#productSlug');
        if ($slug.data('touched')) return;
        var slug = $(this).val().toLowerCase()
            .replace(/[^\w\s-]/g, '').trim().replace(/[\s_]+/g, '-').replace(/-+/g, '-');
        $slug.val(slug);
    });
    $('#productSlug').on('input', function () { $(this).data('touched', true); });

    /* Charts */
    function chart(id, config) {
        var el = document.getElementById(id);
        if (!el || typeof Chart === 'undefined') return;
        return new Chart(el.getContext('2d'), config);
    }

    if (window.WLNR_CHARTS) {
        var d = window.WLNR_CHARTS;
        var brand = '#e8335a';

        // Revenue line — last 30 days
        if (d.revenue) {
            chart('revenueChart', {
                type: 'line',
                data: {
                    labels: d.revenue.labels,
                    datasets: [{
                        label: 'Revenue (Rs.)',
                        data: d.revenue.values,
                        borderColor: brand,
                        backgroundColor: 'rgba(232,51,90,0.12)',
                        fill: true, tension: 0.35, pointRadius: 2
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } } }
            });
        }

        // Orders by status — doughnut
        if (d.status) {
            chart('statusChart', {
                type: 'doughnut',
                data: {
                    labels: d.status.labels,
                    datasets: [{
                        data: d.status.values,
                        backgroundColor: ['#e0a800', '#2f80ed', '#8e5cd9', '#2e9e5b', '#d23c3c']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } } }
            });
        }

        // Top products — horizontal bar
        if (d.top) {
            chart('topProductsChart', {
                type: 'bar',
                data: {
                    labels: d.top.labels,
                    datasets: [{ label: 'Units sold', data: d.top.values, backgroundColor: brand }]
                },
                options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } } }
            });
        }
    }

})(jQuery);
