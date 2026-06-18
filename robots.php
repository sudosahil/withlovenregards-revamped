<?php
/**
 * Dynamic robots.txt. Served at /robots.txt via .htaccess.
 */
require_once __DIR__ . '/config/constants.php';
header('Content-Type: text/plain; charset=utf-8');
?>
User-agent: *
Allow: /
Disallow: /admin/
Disallow: /api/
Disallow: /checkout/
Disallow: /cart/
Disallow: /my-account/

Sitemap: <?= BASE_URL ?>/sitemap.xml
