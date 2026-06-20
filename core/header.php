<?php
/**
 * Site header: announcement bar, main header row, mega-menu nav, mobile offcanvas.
 *
 * A page sets $seo (array) before including this file; SEO::render is called here
 * so the <head> is consistent everywhere. The page also opens its own <main>.
 */

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/seo.php';

$seo = $seo ?? [];

// Single source of truth for the nav (drives both desktop mega-menu and mobile tree).
$navMenu = [
    ['label' => 'Flowers', 'url' => url('flowers'), 'children' => [
        ['label' => 'Roses', 'url' => url('flowers/roses')],
        ['label' => 'Carnations', 'url' => url('flowers/carnations')],
        ['label' => 'Lilies', 'url' => url('flowers/lilies')],
        ['label' => 'Orchids', 'url' => url('flowers?type=Orchid')],
        ['label' => 'Mixed Bouquet', 'url' => url('flowers?type=Bouquet')],
        ['label' => 'Anniversary', 'url' => url('occasions?occasion=anniversary')],
    ]],
    ['label' => 'Cakes', 'url' => url('cakes'), 'children' => [
        ['label' => 'Half Kg', 'url' => url('cakes?weight=500g')],
        ['label' => 'Special', 'url' => url('cakes?type=special')],
        ['label' => 'Theme Cakes', 'url' => url('cakes?type=theme')],
        ['label' => 'Heart Shapes', 'url' => url('cakes?type=heart')],
        ['label' => 'Birthday Cakes', 'url' => url('cakes?occasion=birthday')],
    ]],
    ['label' => 'Chocolates', 'url' => url('chocolates'), 'children' => [
        ['label' => 'Chocolate Bouquet', 'url' => url('chocolates?type=Bouquet')],
        ['label' => 'Chocolate Hampers', 'url' => url('chocolates?type=Hamper')],
    ]],
    ['label' => 'Combos', 'url' => url('combos'), 'children' => [
        ['label' => 'Flowers N Cakes', 'url' => url('combos?type=flowers-cakes')],
        ['label' => 'Flowers N Chocolate', 'url' => url('combos?type=flowers-chocolate')],
    ]],
    ['label' => 'Occasions', 'url' => url('occasions'), 'children' => [
        ['label' => 'Birthday', 'url' => url('occasions?occasion=birthday')],
        ['label' => "Father's Day", 'url' => url('occasions?occasion=fathers-day')],
        ['label' => "Mother's Day", 'url' => url('occasions?occasion=mothers-day')],
        ['label' => "Valentine's Day", 'url' => url('occasions?occasion=valentines-day')],
        ['label' => "Women's Day", 'url' => url('occasions?occasion=womens-day')],
    ]],
    ['label' => 'Send Flowers', 'url' => url('sendflowers/pune'), 'children' => [
        ['label' => 'Pune', 'url' => url('sendflowers/pune')],
        ['label' => 'Mumbai', 'url' => url('sendflowers/mumbai')],
        ['label' => 'Delhi', 'url' => url('sendflowers/delhi')],
        ['label' => 'Bangalore', 'url' => url('sendflowers/bangalore')],
        ['label' => 'Hyderabad', 'url' => url('sendflowers/hyderabad')],
        ['label' => 'Kolkata', 'url' => url('sendflowers/kolkata')],
        ['label' => 'Gurgaon', 'url' => url('sendflowers/gurgaon')],
    ]],
    ['label' => 'Contact Us', 'url' => url('contact-us'), 'children' => []],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#e8335a">
<?php SEO::gtmHead(); ?>
<?php SEO::ga4(); ?>
<?php SEO::render($seo); ?>

<link rel="icon" href="<?= e(asset('img/favicon.ico')) ?>" type="image/x-icon">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.min.css">
<link rel="stylesheet" href="<?= e(asset('css/vendor.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
<?php if (!empty($seo['preload_image'])): ?>
<link rel="preload" as="image" href="<?= e($seo['preload_image']) ?>" fetchpriority="high">
<?php endif; ?>
</head>
<body>
<?php SEO::gtmBody(); ?>

<!-- Announcement bar -->
<div class="announce-bar">
    <div class="container announce-bar__inner">
        <span class="announce-bar__badge"><i class="fa-solid fa-truck-fast"></i> Same Day Delivery Available</span>
        <span class="announce-bar__right">
            <a href="tel:<?= e(CONTACT_PHONE_TEL) ?>"><i class="fa-solid fa-phone"></i> <?= e(CONTACT_PHONE) ?></a>
            <span class="announce-bar__currency"><?= e(CURRENCY_CODE) ?></span>
        </span>
    </div>
</div>

<!-- Main header -->
<header class="site-header">
    <div class="container site-header__inner">
        <button class="hamburger" id="navToggle" aria-label="Open menu" aria-expanded="false" aria-controls="mobileNav">
            <i class="fa-solid fa-bars"></i>
        </button>

        <a class="site-logo" href="<?= e(url()) ?>">
            <img src="<?= e(asset('img/logo/logo.png')) ?>" alt="<?= e(SITE_NAME) ?> — online florist and gifting" width="274" height="44">
        </a>

        <form class="header-search" action="<?= e(url('search')) ?>" method="get" role="search">
            <input type="search" name="q" placeholder="Search for flowers, cakes, gifts…" aria-label="Search products" autocomplete="off" id="headerSearchInput">
            <button type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
            <div class="header-search__results" id="headerSearchResults" hidden></div>
        </form>

        <div class="header-actions">
            <div class="header-actions__item header-account">
                <button class="header-actions__btn" aria-haspopup="true" aria-expanded="false" id="accountToggle">
                    <i class="fa-regular fa-user"></i>
                    <span class="header-actions__label"><?= is_logged_in() ? e(explode(' ', current_user()['name'])[0]) : 'Account' ?></span>
                </button>
                <div class="header-dropdown" id="accountDropdown">
                    <?php if (is_logged_in()): ?>
                        <a href="<?= e(url('my-account')) ?>">My Account</a>
                        <a href="<?= e(url('my-account?tab=orders')) ?>">My Orders</a>
                        <a href="<?= e(url('api/auth.php?action=logout')) ?>">Logout</a>
                    <?php else: ?>
                        <a href="<?= e(url('login-register#login')) ?>">Login</a>
                        <a href="<?= e(url('login-register#register')) ?>">Register</a>
                        <a href="<?= e(url('my-account?tab=orders')) ?>">Track Order</a>
                    <?php endif; ?>
                </div>
            </div>

            <a class="header-actions__item" href="<?= e(url('wishlist')) ?>" aria-label="Wishlist">
                <i class="fa-regular fa-heart"></i>
                <span class="header-badge" id="wishlistCount"><?= wishlist_count() ?></span>
            </a>

            <a class="header-actions__item" href="<?= e(url('cart')) ?>" aria-label="Cart">
                <i class="fa-solid fa-bag-shopping"></i>
                <span class="header-badge" id="cartCount"><?= cart_count() ?></span>
            </a>
        </div>
    </div>

    <!-- Desktop mega-menu -->
    <nav class="main-nav" aria-label="Primary">
        <div class="container">
            <ul class="main-nav__list">
                <?php foreach ($navMenu as $item): ?>
                    <li class="main-nav__item<?= !empty($item['children']) ? ' has-mega' : '' ?>">
                        <a href="<?= e($item['url']) ?>"><?= e($item['label']) ?><?php if (!empty($item['children'])): ?> <i class="fa-solid fa-chevron-down"></i><?php endif; ?></a>
                        <?php if (!empty($item['children'])): ?>
                            <div class="mega-menu">
                                <ul>
                                    <?php foreach ($item['children'] as $child): ?>
                                        <li><a href="<?= e($child['url']) ?>"><?= e($child['label']) ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </nav>
</header>

<!-- Mobile offcanvas -->
<div class="offcanvas-backdrop" id="offcanvasBackdrop" hidden></div>
<aside class="mobile-nav" id="mobileNav" aria-hidden="true">
    <div class="mobile-nav__head">
        <span class="mobile-nav__title"><?= e(SITE_NAME) ?></span>
        <button class="mobile-nav__close" id="navClose" aria-label="Close menu"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <form class="mobile-nav__search" action="<?= e(url('search')) ?>" method="get" role="search">
        <input type="search" name="q" placeholder="Search…" aria-label="Search products">
        <button type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
    </form>
    <ul class="mobile-nav__list">
        <?php foreach ($navMenu as $i => $item): ?>
            <li class="mobile-nav__item">
                <?php if (!empty($item['children'])): ?>
                    <button class="mobile-nav__expander" aria-expanded="false" data-target="msub-<?= $i ?>">
                        <?= e($item['label']) ?> <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <ul class="mobile-nav__sub" id="msub-<?= $i ?>" hidden>
                        <li><a href="<?= e($item['url']) ?>">All <?= e($item['label']) ?></a></li>
                        <?php foreach ($item['children'] as $child): ?>
                            <li><a href="<?= e($child['url']) ?>"><?= e($child['label']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <a class="mobile-nav__link" href="<?= e($item['url']) ?>"><?= e($item['label']) ?></a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <div class="mobile-nav__account">
        <?php if (is_logged_in()): ?>
            <a href="<?= e(url('my-account')) ?>"><i class="fa-regular fa-user"></i> My Account</a>
            <a href="<?= e(url('api/auth.php?action=logout')) ?>"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        <?php else: ?>
            <a href="<?= e(url('login-register#login')) ?>"><i class="fa-regular fa-user"></i> Login / Register</a>
        <?php endif; ?>
    </div>
    <div class="mobile-nav__social">
        <a href="<?= e(SOCIAL_FACEBOOK) ?>" aria-label="Facebook" target="_blank" rel="noopener"><i class="fa-brands fa-facebook-f"></i></a>
        <a href="<?= e(SOCIAL_INSTAGRAM) ?>" aria-label="Instagram" target="_blank" rel="noopener"><i class="fa-brands fa-instagram"></i></a>
        <a href="<?= e(SOCIAL_TWITTER) ?>" aria-label="Twitter" target="_blank" rel="noopener"><i class="fa-brands fa-x-twitter"></i></a>
        <a href="<?= e(SOCIAL_PINTEREST) ?>" aria-label="Pinterest" target="_blank" rel="noopener"><i class="fa-brands fa-pinterest-p"></i></a>
    </div>
</aside>
