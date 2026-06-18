<?php
/**
 * City landing pages. Routed by .htaccess: ?city=pune.
 * Serves pune, mumbai, delhi, bangalore, hyderabad, kolkata, gurgaon.
 * Each city gets a unique H1, meta and local SEO content.
 */
require_once __DIR__ . '/../includes/functions.php';

$cities = [
    'pune'      => ['name' => 'Pune',      'region' => 'Maharashtra', 'areas' => 'Kothrud, Hinjewadi, Viman Nagar, Koregaon Park and Baner'],
    'mumbai'    => ['name' => 'Mumbai',    'region' => 'Maharashtra', 'areas' => 'Bandra, Andheri, Powai, Colaba and Dadar'],
    'delhi'     => ['name' => 'Delhi',     'region' => 'Delhi',       'areas' => 'Connaught Place, Dwarka, Saket, Rohini and Karol Bagh'],
    'bangalore' => ['name' => 'Bangalore', 'region' => 'Karnataka',   'areas' => 'Koramangala, Indiranagar, Whitefield, HSR Layout and Jayanagar'],
    'hyderabad' => ['name' => 'Hyderabad', 'region' => 'Telangana',   'areas' => 'Banjara Hills, Gachibowli, Madhapur, Kukatpally and Secunderabad'],
    'kolkata'   => ['name' => 'Kolkata',   'region' => 'West Bengal',  'areas' => 'Park Street, Salt Lake, Ballygunge, Howrah and New Town'],
    'gurgaon'   => ['name' => 'Gurgaon',   'region' => 'Haryana',     'areas' => 'Cyber City, DLF Phase 1-5, Sohna Road, Golf Course Road and Sushant Lok'],
];

$citySlug = preg_replace('/[^a-z]/', '', strtolower($_GET['city'] ?? ''));
if (!isset($cities[$citySlug])) {
    require __DIR__ . '/../pages/404.php';
    exit;
}
$city = $cities[$citySlug];
$cityName = $city['name'];

// Placeholder: show all products (live: filter to city-serviceable stock).
$products = array_slice(get_products(), 0, 9);

$cityFaqs = [
    ['q' => "Do you offer same-day flower delivery in $cityName?", 'a' => "Yes. Orders placed before 5 PM IST qualify for same-day delivery across $cityName."],
    ['q' => "Which areas of $cityName do you cover?", 'a' => "We deliver to most areas including {$city['areas']}, subject to pincode serviceability."],
    ['q' => "Can I send midnight flowers in $cityName?", 'a' => "Yes, midnight delivery (11 PM–1 AM) is available in $cityName for select products."],
    ['q' => "What gifts can I send to $cityName?", 'a' => "Fresh flowers, cakes, chocolates and combos — all available for delivery in $cityName."],
];

$seo = [
    'title'       => "Flower Delivery in $cityName | Send Flowers Online to $cityName | " . SITE_NAME,
    'description' => "Send flowers, cakes and gifts to $cityName with same-day delivery. Fresh bouquets, eggless cakes and combos delivered across $cityName by " . SITE_NAME . '.',
    'keywords'    => "flower delivery $cityName, send flowers $cityName, online flowers $cityName, cake delivery $cityName",
    'canonical'   => BASE_URL . '/sendflowers/' . $citySlug . '/',
    'og_type'     => 'website',
    'schema'      => ['breadcrumb', 'itemlist', 'faq'],
    'schema_data' => [
        'breadcrumb' => [
            ['name' => 'Home', 'url' => url()],
            ['name' => 'Send Flowers', 'url' => url('sendflowers/pune')],
            ['name' => $cityName, 'url' => BASE_URL . '/sendflowers/' . $citySlug . '/'],
        ],
        'items' => $products,
        'list_name' => "Flowers in $cityName",
        'faqs' => $cityFaqs,
    ],
];
require __DIR__ . '/../includes/header.php';
?>
<main>
    <section class="category-hero">
        <div class="container">
            <h1>Flower Delivery in <?= e($cityName) ?></h1>
            <p>Send fresh flowers, cakes and gifts to <?= e($cityName) ?>, <?= e($city['region']) ?> — with same-day delivery when you order before 5 PM IST.</p>
        </div>
    </section>

    <section class="section container">
        <h2 class="section__title">Popular Gifts in <?= e($cityName) ?></h2>
        <div class="product-grid">
            <?php foreach ($products as $product) {
                include __DIR__ . '/../includes/product-card.php';
            } ?>
        </div>
    </section>

    <section class="section seo-content">
        <div class="container">
            <h2>Send Flowers Online in <?= e($cityName) ?></h2>
            <p>Looking to surprise someone special in <?= e($cityName) ?>? <?= e(SITE_NAME) ?> makes it effortless. Choose from hand-tied bouquets, freshly baked cakes, premium chocolates and curated combos, and we'll deliver them fresh to doorsteps across <?= e($cityName) ?>.</p>
            <h2>Same-Day &amp; Midnight Delivery Across <?= e($cityName) ?></h2>
            <p>We serve all major neighbourhoods including <?= e($city['areas']) ?>. Order before 5 PM IST for same-day delivery, or choose a midnight slot to make birthdays and anniversaries truly memorable.</p>
            <h2>Why Choose <?= e(SITE_NAME) ?> in <?= e($cityName) ?></h2>
            <p>Fresh quality, secure payments and reliable doorstep delivery. From last-minute gestures to planned celebrations, we help you send love and regards to <?= e($cityName) ?>, every time.</p>
        </div>
    </section>

    <section class="section container">
        <h2 class="section__title">FAQs — Flower Delivery in <?= e($cityName) ?></h2>
        <div class="faq-list">
            <?php foreach ($cityFaqs as $faq): ?>
                <div class="faq-item">
                    <button class="faq-item__q"><?= e($faq['q']) ?> <i class="fa-solid fa-plus"></i></button>
                    <div class="faq-item__a"><p><?= e($faq['a']) ?></p></div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
