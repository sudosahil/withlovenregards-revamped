<?php
/**
 * Homepage. Reads editable content from data/homepage_config.json so the admin
 * homepage editor can change banners, featured products, promos, SEO and FAQs.
 */
require_once __DIR__ . '/core/functions.php';

$cfg = homepage_config();
$slides = $cfg['hero_slides'] ?? [];
$promoTiles = $cfg['promo_tiles'] ?? [];
$featuredIds = $cfg['featured_product_ids'] ?? [];
$seoContent = $cfg['seo'] ?? [];
$promise = $cfg['promise'] ?? [];
$reviews = $cfg['reviews'] ?? [];
$faqs = get_faqs();

/** Initials for a reviewer avatar, e.g. "Priya Sharma" -> "PS". */
$initials = static function (string $name): string {
    $parts = preg_split('/\s+/', trim($name)) ?: [];
    $first = mb_substr($parts[0] ?? '', 0, 1);
    $last = count($parts) > 1 ? mb_substr(end($parts), 0, 1) : '';
    return mb_strtoupper($first . $last);
};

// Resolve featured products from configured IDs (fall back to is_featured flag).
$featured = [];
foreach ($featuredIds as $id) {
    $prod = get_product_by_id((int) $id);
    if ($prod) {
        $featured[] = $prod;
    }
}
if (!$featured) {
    $featured = get_bestseller_products(8);
}

$flowers = get_products_by_category(1);
$cakes = get_products_by_category(2);
$chocolates = get_products_by_category(3);

$firstBanner = $slides[0]['image'] ?? '/assets/img/banners/banner-1.webp';

$seo = [
    'is_home'       => true,
    'title'         => SITE_NAME . ' | Send Flowers, Cakes & Gifts Online with Same Day Delivery',
    'description'   => 'Send flowers, cakes, chocolates and gifts online with WithLoveNRegards. Same-day delivery across Pune, Mumbai, Delhi, Bangalore, Hyderabad, Kolkata & Gurgaon.',
    'keywords'      => 'online flowers, send flowers online, cake delivery, gifts online, Pune florist, same day flower delivery',
    'canonical'     => BASE_URL . '/',
    'og_image'      => BASE_URL . $firstBanner,
    'og_type'       => 'website',
    'preload_image' => BASE_URL . $firstBanner,
    'schema'        => ['website', 'organization', 'faq'],
    'schema_data'   => ['faqs' => $faqs],
];

require __DIR__ . '/core/header.php';
?>

<main>
    <!-- Section 1: Hero slider -->
    <section class="hero" aria-label="Featured promotions">
        <div class="hero__track">
            <?php foreach ($slides as $i => $slide): ?>
                <div class="hero__slide">
                    <img src="<?= e(media(ltrim($slide['image'], '/'))) ?>"
                         alt="<?= e($slide['heading']) ?>"
                         width="1200" height="480"
                         <?= $i === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>>
                    <div class="hero__caption">
                        <div class="hero__caption-inner">
                            <span class="hero__eyebrow"><i class="fa-solid fa-truck-fast"></i> Same-Day Delivery Across India</span>
                            <h2><?= e($slide['heading']) ?></h2>
                            <p><?= e($slide['subtext']) ?></p>
                            <a class="btn btn--primary btn--lg hero__cta" href="<?= e(url(ltrim($slide['cta_link'], '/'))) ?>"><?= e($slide['cta_text']) ?> <i class="fa-solid fa-arrow-right"></i></a>
                            <span class="hero__trust"><i class="fa-solid fa-star"></i> 4.9/5 from 2,400+ verified orders &nbsp;·&nbsp; <i class="fa-solid fa-leaf"></i> 7-day freshness guarantee</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="hero__arrow hero__arrow--prev" aria-label="Previous slide"><i class="fa-solid fa-chevron-left"></i></button>
        <button class="hero__arrow hero__arrow--next" aria-label="Next slide"><i class="fa-solid fa-chevron-right"></i></button>
        <div class="hero__dots">
            <?php foreach ($slides as $i => $slide): ?>
                <button class="hero__dot<?= $i === 0 ? ' active' : '' ?>" aria-label="Go to slide <?= $i + 1 ?>"></button>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Section 2: Category icon grid -->
    <section class="section container">
        <div class="cat-grid">
            <?php
            $iconCats = [
                ['Flowers', 'flowers', 'fa-seedling'],
                ['Cakes', 'cakes', 'fa-cake-candles'],
                ['Chocolates', 'chocolates', 'fa-gift'],
                ['Combos', 'combos', 'fa-box-open'],
            ];
            foreach ($iconCats as [$label, $slug, $icon]): ?>
                <a class="cat-card" href="<?= e(url($slug)) ?>">
                    <span style="font-size:2.4rem;color:var(--primary);"><i class="fa-solid <?= $icon ?>"></i></span>
                    <span><?= e($label) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Section 3: Best sellers (carousel — flows neatly regardless of count) -->
    <section class="section container">
        <h2 class="section__title">Best Sellers</h2>
        <p class="section__subtitle">Our most-loved flowers, cakes and gifts</p>
        <div class="carousel">
            <button class="carousel__arrow carousel__arrow--prev" aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></button>
            <div class="carousel__viewport">
                <div class="carousel__track">
                    <?php foreach ($featured as $product) {
                        include __DIR__ . '/core/product-card.php';
                    } ?>
                </div>
            </div>
            <button class="carousel__arrow carousel__arrow--next" aria-label="Next"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
        <p class="text-center" style="margin-top:22px;">
            <a class="btn btn--outline" href="<?= e(url('flowers')) ?>">View All Products</a>
        </p>
    </section>

    <!-- Section 3b: Our Promise — authenticity & guarantee band -->
    <?php if (!empty($promise['items'])): ?>
        <section class="section promise">
            <div class="container">
                <p class="section__eyebrow"><?= e($promise['eyebrow'] ?? 'Why WithLoveNRegards') ?></p>
                <h2 class="section__title"><?= e($promise['heading'] ?? 'Our Promise to You') ?></h2>
                <p class="section__subtitle"><?= e($promise['subtext'] ?? '') ?></p>
                <div class="promise-grid">
                    <?php foreach ($promise['items'] as $item): ?>
                        <div class="promise-card">
                            <span class="promise-card__icon"><i class="fa-solid <?= e($item['icon'] ?? 'fa-circle-check') ?>"></i></span>
                            <h3 class="promise-card__title"><?= e($item['title'] ?? '') ?></h3>
                            <p class="promise-card__body"><?= e($item['body'] ?? '') ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Section 4: Promo split banner -->
    <section class="section container">
        <div class="promo-split">
            <?php foreach ($promoTiles as $tile): ?>
                <a class="promo-tile" href="<?= e(url(ltrim($tile['link'], '/'))) ?>">
                    <img src="<?= e(media(ltrim($tile['image'], '/'))) ?>" alt="<?= e($tile['heading']) ?>" width="585" height="200" loading="lazy">
                    <span class="promo-tile__caption"><h2><?= e($tile['heading']) ?></h2></span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Sections 5-7: Carousels -->
    <?php
    $carousels = [
        ['Fresh Flowers', $flowers, 'flowers'],
        ['Delicious Cakes', $cakes, 'cakes'],
        ['Chocolate Gifts', $chocolates, 'chocolates'],
    ];
    foreach ($carousels as [$title, $items, $slug]):
        if (!$items) continue; ?>
        <section class="section container">
            <h2 class="section__title"><?= e($title) ?></h2>
            <div class="carousel">
                <button class="carousel__arrow carousel__arrow--prev" aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></button>
                <div class="carousel__viewport">
                    <div class="carousel__track">
                        <?php foreach (array_slice($items, 0, 8) as $product) {
                            include __DIR__ . '/core/product-card.php';
                        } ?>
                    </div>
                </div>
                <button class="carousel__arrow carousel__arrow--next" aria-label="Next"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        </section>
    <?php endforeach; ?>

    <!-- Section 7b: Customer reviews — social proof / authenticity -->
    <?php if (!empty($reviews['items'])): ?>
        <section class="section reviews">
            <div class="container">
                <p class="section__eyebrow"><?= e($reviews['eyebrow'] ?? 'Real Customer Stories') ?></p>
                <h2 class="section__title"><?= e($reviews['heading'] ?? 'What Our Customers Say') ?></h2>
                <p class="reviews__rating">
                    <span class="reviews__stars" aria-hidden="true"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span>
                    <strong><?= e($reviews['rating'] ?? '4.9') ?>/5</strong> from <?= e($reviews['rating_count'] ?? '2,400+') ?> verified orders
                </p>
                <div class="reviews-grid">
                    <?php foreach ($reviews['items'] as $i => $review): ?>
                        <?php $rate = (int) ($review['rating'] ?? 5); ?>
                        <article class="review-card">
                            <div class="review-card__stars" aria-label="<?= $rate ?> out of 5 stars">
                                <?php for ($s = 1; $s <= 5; $s++): ?>
                                    <i class="fa-<?= $s <= $rate ? 'solid' : 'regular' ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="review-card__text"><?= e($review['text'] ?? '') ?></p>
                            <div class="review-card__author">
                                <span class="review-card__avatar" data-tone="<?= $i % 4 ?>"><?= e($initials($review['name'] ?? '')) ?></span>
                                <span class="review-card__meta">
                                    <strong><?= e($review['name'] ?? '') ?></strong>
                                    <small><?= e($review['city'] ?? '') ?> &nbsp;·&nbsp; <i class="fa-solid fa-circle-check"></i> Verified order</small>
                                </span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Section 8: SEO content block — the single H1 of the page -->
    <section class="section seo-content">
        <div class="container">
            <h1><?= e($seoContent['h1'] ?? 'Send Flowers, Cakes & Gifts Online') ?></h1>
            <p><?= e($seoContent['intro'] ?? '') ?></p>
            <?php foreach ($seoContent['blocks'] ?? [] as $block): ?>
                <h2><?= e($block['h2']) ?></h2>
                <p><?= e($block['body']) ?></p>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Section 9: FAQ -->
    <section class="section container">
        <h2 class="section__title">Frequently Asked Questions</h2>
        <div class="faq-list">
            <?php foreach ($faqs as $faq): ?>
                <div class="faq-item">
                    <button class="faq-item__q"><?= e($faq['q']) ?> <i class="fa-solid fa-plus"></i></button>
                    <div class="faq-item__a"><p><?= e($faq['a']) ?></p></div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Section 10: Trust badges -->
    <section class="section" style="background:var(--accent);">
        <div class="container">
            <div class="trust-grid">
                <div class="trust-item"><i class="fa-solid fa-truck-fast"></i><strong>Free Shipping</strong><span>On all prepaid orders</span></div>
                <div class="trust-item"><i class="fa-solid fa-headset"></i><strong>Dedicated Support</strong><span>We're here to help</span></div>
                <div class="trust-item"><i class="fa-solid fa-shield-halved"></i><strong>100% Secure</strong><span>Safe & encrypted payments</span></div>
                <div class="trust-item"><i class="fa-solid fa-tags"></i><strong>Best Deal</strong><span>Fresh quality, fair prices</span></div>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/core/footer.php'; ?>
