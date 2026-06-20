<?php
/**
 * Dynamic XML sitemap. Served at /sitemap.xml via .htaccess.
 */
require_once __DIR__ . '/core/functions.php';
header('Content-Type: application/xml; charset=utf-8');

$urls = [];
$urls[] = ['loc' => BASE_URL . '/', 'priority' => '1.0', 'freq' => 'daily'];

foreach (get_top_categories() as $cat) {
    $urls[] = ['loc' => category_url($cat), 'priority' => '0.8', 'freq' => 'weekly'];
    foreach (get_subcategories((int) $cat['id']) as $sub) {
        $urls[] = ['loc' => category_url($sub), 'priority' => '0.7', 'freq' => 'weekly'];
    }
}
foreach (get_products() as $p) {
    $urls[] = ['loc' => product_url($p), 'priority' => '0.6', 'freq' => 'weekly'];
}
foreach (['pune', 'mumbai', 'delhi', 'bangalore', 'hyderabad', 'kolkata', 'gurgaon'] as $c) {
    $urls[] = ['loc' => BASE_URL . '/sendflowers/' . $c . '/', 'priority' => '0.7', 'freq' => 'monthly'];
}
foreach (['about-us', 'contact-us', 'privacy-policy', 'shipping-disclaimer', 'terms-and-conditions', 'return-and-refund-policy'] as $page) {
    $urls[] = ['loc' => BASE_URL . '/' . $page . '/', 'priority' => '0.4', 'freq' => 'yearly'];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $u) {
    echo "  <url>\n";
    echo '    <loc>' . e($u['loc']) . "</loc>\n";
    echo '    <changefreq>' . $u['freq'] . "</changefreq>\n";
    echo '    <priority>' . $u['priority'] . "</priority>\n";
    echo "  </url>\n";
}
echo '</urlset>';
