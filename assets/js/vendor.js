/* =========================================================================
   Vendor JS bundle slot.
   jQuery 3.7.1 and jQuery UI 1.13.2 are loaded from CDN in footer.php.
   Any additional third-party plugins (lightbox, lazyload polyfill, etc.)
   should be concatenated here so the page makes a single extra request.
   ========================================================================= */
(function () {
    'use strict';

    // Native lazy-loading fallback for older browsers: if loading="lazy" is
    // unsupported, swap data-src -> src on scroll. Images already use
    // loading="lazy" natively, so this only matters on legacy engines.
    if ('loading' in HTMLImageElement.prototype) {
        return; // native lazy-loading available, nothing to polyfill
    }

    var lazyImages = [].slice.call(document.querySelectorAll('img[data-src]'));
    function load(img) {
        img.src = img.getAttribute('data-src');
        img.removeAttribute('data-src');
    }
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) { load(entry.target); io.unobserve(entry.target); }
            });
        });
        lazyImages.forEach(function (img) { io.observe(img); });
    } else {
        lazyImages.forEach(load);
    }
})();
