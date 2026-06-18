<?php
/**
 * Dev router for PHP's built-in server — mimics the .htaccess rewrite rules so
 * the site can be smoke-tested without Apache. Not used in production.
 *   php -S localhost:8910 scripts/router.php
 */
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$root = dirname(__DIR__);

// Serve real files (assets) directly.
if ($uri !== '/' && file_exists($root . $uri) && !is_dir($root . $uri)) {
    return false;
}

$path = trim($uri, '/');
$seg = $path === '' ? [] : explode('/', $path);

// Static endpoints
$map = [
    'cart' => 'pages/cart.php', 'checkout' => 'pages/checkout.php',
    'order-confirmation' => 'pages/order-confirmation.php', 'login-register' => 'pages/login-register.php',
    'my-account' => 'pages/my-account.php', 'wishlist' => 'pages/wishlist.php', 'search' => 'pages/search.php',
    'about-us' => 'pages/about-us.php', 'contact-us' => 'pages/contact-us.php',
    'privacy-policy' => 'pages/privacy-policy.php', 'shipping-disclaimer' => 'pages/shipping-disclaimer.php',
    'terms-and-conditions' => 'pages/terms-and-conditions.php', 'return-and-refund-policy' => 'pages/return-and-refund-policy.php',
    'sitemap.xml' => 'sitemap.php', 'robots.txt' => 'robots.php',
];

if ($path === '') { require $root . '/index.php'; return true; }

if (isset($map[$path])) { require $root . '/' . $map[$path]; return true; }

if ($seg[0] === 'admin') {
    $page = $seg[1] ?? 'index';
    $file = $root . '/admin/' . $page . '.php';
    require file_exists($file) ? $file : $root . '/admin/index.php';
    return true;
}

if ($seg[0] === 'api') { require $root . '/api/' . ($seg[1] ?? '') ; return true; }

if ($seg[0] === 'sendflowers') { $_GET['city'] = $seg[1] ?? ''; require $root . '/sendflowers/city.php'; return true; }

// Catalogue
if (count($seg) === 3) { $_GET['category'] = $seg[0]; $_GET['subcategory'] = $seg[1]; $_GET['slug'] = $seg[2]; require $root . '/pages/product.php'; return true; }
if (count($seg) === 2) { $_GET['category'] = $seg[0]; $_GET['subcategory'] = $seg[1]; require $root . '/pages/category.php'; return true; }
if (count($seg) === 1) { $_GET['category'] = $seg[0]; require $root . '/pages/category.php'; return true; }

http_response_code(404);
require $root . '/pages/404.php';
return true;
