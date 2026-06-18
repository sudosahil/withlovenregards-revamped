<?php
/**
 * Site-wide constants for WithLoveNRegards.
 * Loaded by every entry point before any output.
 */

if (!defined('WLNR_INIT')) {
    define('WLNR_INIT', true);
}

// --- Identity -------------------------------------------------------------
define('SITE_NAME', 'WithLoveNRegards');
define('SITE_TAGLINE', 'Online Florist & Gifting in Pune');
define('VERSION', '2.0');

// The canonical production host (used for SEO and as the fallback off-web/CLI).
define('PROD_URL', 'https://www.withlovenregards.com');

/**
 * BASE_URL is resolved from the actual request host so the site works on any
 * host it is served from (localhost dev, staging, production). It falls back to
 * the production URL for CLI contexts such as sitemap generation. On production
 * this resolves to https://www.withlovenregards.com exactly as before.
 */
if (!empty($_SERVER['HTTP_HOST'])) {
    $wlnr_https = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
        || ((int) ($_SERVER['SERVER_PORT'] ?? 0) === 443)
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    define('BASE_URL', ($wlnr_https ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']);
} else {
    define('BASE_URL', PROD_URL);
}

// --- Locale / commerce ----------------------------------------------------
define('CURRENCY', 'Rs.');
define('CURRENCY_CODE', 'INR');
define('TIMEZONE', 'Asia/Kolkata');

// Same-day delivery order cutoff (24h clock, server-evaluated in IST).
define('SAME_DAY_CUTOFF_HOUR', 17); // 5 PM IST

// --- Contact --------------------------------------------------------------
define('ADMIN_EMAIL', 'support@withlovenregards.com');
define('CONTACT_PHONE', '+91 982 352 0255');
define('CONTACT_PHONE_TEL', '+919823520255');
define('WHATSAPP_NUMBER', '919823520255');

// --- Analytics / verification --------------------------------------------
define('GTM_ID', 'GTM-WDCFQDH');
define('GA4_ID', 'G-P1NQ4XVN0G');
define('GOOGLE_SITE_VERIFICATION', ''); // fill when available
define('PINTEREST_VERIFICATION', '');   // fill when available

// --- Social ---------------------------------------------------------------
define('SOCIAL_FACEBOOK', 'https://www.facebook.com/withlovenregards');
define('SOCIAL_INSTAGRAM', 'https://www.instagram.com/withlovenregards');
define('SOCIAL_TWITTER', 'https://twitter.com/withlovenregard');
define('SOCIAL_PINTEREST', 'https://in.pinterest.com/withlovenregards');

// --- Paths ----------------------------------------------------------------
define('ROOT_PATH', dirname(__DIR__));
define('ASSET_URL', BASE_URL . '/assets');

// Apply timezone globally.
date_default_timezone_set(TIMEZONE);
