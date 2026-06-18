<?php
require_once __DIR__ . '/../includes/functions.php';
$seo = [
    'title'       => 'About Us | ' . SITE_NAME,
    'description' => 'Learn about WithLoveNRegards — Pune\'s trusted online florist and gifting destination delivering fresh flowers, cakes and gifts across India.',
    'canonical'   => BASE_URL . '/about-us/',
    'schema'      => ['organization'],
];
require __DIR__ . '/../includes/header.php';
?>
<main class="container section">
    <h1 class="section__title">About WithLoveNRegards</h1>
    <div style="max-width:820px;margin:0 auto;color:var(--muted);line-height:1.8;">
        <p>WithLoveNRegards was born in Pune with a simple idea: make it effortless to send love and good wishes to the people who matter, wherever they are. What began as a small neighbourhood florist has grown into a trusted online gifting destination serving customers across India.</p>
        <h2 style="color:var(--secondary);">Our Promise</h2>
        <p>Every bouquet is hand-tied by experienced florists, every cake is freshly baked, and every order is delivered with care. We believe a gift is more than a product — it's an emotion, and we treat it that way.</p>
        <h2 style="color:var(--secondary);">What We Offer</h2>
        <p>Fresh flowers, freshly baked cakes (with eggless options), premium chocolates and thoughtfully curated combos — with same-day delivery across Pune, Mumbai, Delhi, Bangalore, Hyderabad, Kolkata and Gurgaon when you order before 5 PM IST.</p>
        <h2 style="color:var(--secondary);">Why Customers Trust Us</h2>
        <p>Reliable doorstep delivery, secure payments, responsive support and a genuine commitment to quality. Thousands of celebrations later, our mission remains the same — to deliver happiness, with love and regards.</p>
        <p style="margin-top:24px;"><a class="btn btn--primary" href="<?= e(url('flowers')) ?>">Start Shopping</a></p>
    </div>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
