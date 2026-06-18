<?php
/**
 * Site footer: four-column layout, social links, WhatsApp float, scripts.
 * Closes <body> and <html> — every page includes this last.
 */
require_once __DIR__ . '/functions.php';
$waMessage = rawurlencode('Hi! I would like to know more about your flowers and gifts.');
?>
<footer class="site-footer">
    <div class="container site-footer__grid">
        <div class="footer-col">
            <a class="footer-logo" href="<?= e(url()) ?>">
                <img src="<?= e(asset('img/logo/logo-white.png')) ?>" alt="<?= e(SITE_NAME) ?>" width="287" height="46" loading="lazy">
            </a>
            <p class="footer-about">Pune's trusted online florist and gifting destination. Fresh flowers, freshly baked cakes and curated gifts delivered with love and regards across India.</p>
            <div class="footer-social">
                <a href="<?= e(SOCIAL_FACEBOOK) ?>" aria-label="Facebook" target="_blank" rel="noopener"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="<?= e(SOCIAL_INSTAGRAM) ?>" aria-label="Instagram" target="_blank" rel="noopener"><i class="fa-brands fa-instagram"></i></a>
                <a href="<?= e(SOCIAL_TWITTER) ?>" aria-label="Twitter" target="_blank" rel="noopener"><i class="fa-brands fa-x-twitter"></i></a>
                <a href="<?= e(SOCIAL_PINTEREST) ?>" aria-label="Pinterest" target="_blank" rel="noopener"><i class="fa-brands fa-pinterest-p"></i></a>
            </div>
        </div>

        <div class="footer-col">
            <h3 class="footer-col__title">Shop</h3>
            <ul class="footer-links">
                <li><a href="<?= e(url('flowers')) ?>">Flowers</a></li>
                <li><a href="<?= e(url('cakes')) ?>">Cakes</a></li>
                <li><a href="<?= e(url('chocolates')) ?>">Chocolates</a></li>
                <li><a href="<?= e(url('combos')) ?>">Combos</a></li>
                <li><a href="<?= e(url('occasions')) ?>">Occasions</a></li>
                <li><a href="<?= e(url('sendflowers/pune')) ?>">Online Flowers Delivery</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h3 class="footer-col__title">Information</h3>
            <ul class="footer-links">
                <li><a href="<?= e(url('about-us')) ?>">About Us</a></li>
                <li><a href="<?= e(url('contact-us')) ?>">Contact Us</a></li>
                <li><a href="<?= e(url('privacy-policy')) ?>">Privacy Policy</a></li>
                <li><a href="<?= e(url('shipping-disclaimer')) ?>">Shipping Disclaimer</a></li>
                <li><a href="<?= e(url('terms-and-conditions')) ?>">Terms &amp; Conditions</a></li>
                <li><a href="<?= e(url('return-and-refund-policy')) ?>">Return &amp; Refund Policy</a></li>
                <li><a href="<?= e(url('sitemap.xml')) ?>">Sitemap</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h3 class="footer-col__title">Get In Touch</h3>
            <ul class="footer-contact">
                <li><i class="fa-solid fa-location-dot"></i> Pune, Maharashtra, India</li>
                <li><i class="fa-solid fa-phone"></i> <a href="tel:<?= e(CONTACT_PHONE_TEL) ?>"><?= e(CONTACT_PHONE) ?></a></li>
                <li><i class="fa-solid fa-envelope"></i> <a href="mailto:<?= e(ADMIN_EMAIL) ?>"><?= e(ADMIN_EMAIL) ?></a></li>
                <li><i class="fa-brands fa-whatsapp"></i> <a href="https://wa.me/<?= e(WHATSAPP_NUMBER) ?>" target="_blank" rel="noopener">Chat on WhatsApp</a></li>
            </ul>
            <div class="footer-payments" aria-label="Accepted payment methods">
                <i class="fa-brands fa-cc-visa"></i>
                <i class="fa-brands fa-cc-mastercard"></i>
                <i class="fa-brands fa-cc-amex"></i>
                <i class="fa-solid fa-indian-rupee-sign"></i>
            </div>
        </div>
    </div>

    <div class="site-footer__bottom">
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= e(SITE_NAME) ?>. All rights reserved. Made with <i class="fa-solid fa-heart" style="color:#e8335a"></i> in Pune.</p>
        </div>
    </div>
</footer>

<!-- WhatsApp floating button -->
<a class="whatsapp-float" href="https://wa.me/<?= e(WHATSAPP_NUMBER) ?>?text=<?= $waMessage ?>" target="_blank" rel="noopener" aria-label="Chat with us on WhatsApp">
    <i class="fa-brands fa-whatsapp"></i>
</a>

<!-- Scripts: jQuery 3.7.1 + jQuery UI 1.13.2 -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>
<script>
    // Expose server-evaluated config to the frontend (5 PM IST cutoff, base URL, CSRF).
    window.WLNR = {
        baseUrl: <?= json_encode(BASE_URL) ?>,
        apiUrl: <?= json_encode(BASE_URL . '/api') ?>,
        csrfToken: <?= json_encode(csrf_token()) ?>,
        sameDayAvailable: <?= same_day_available() ? 'true' : 'false' ?>,
        earliestDeliveryDate: <?= json_encode(earliest_delivery_date()) ?>,
        currency: <?= json_encode(CURRENCY) ?>
    };
</script>
<script src="<?= e(asset('js/vendor.js')) ?>"></script>
<script src="<?= e(asset('js/active.js')) ?>"></script>
</body>
</html>
