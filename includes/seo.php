<?php
/**
 * SEO meta-tag engine.
 *
 * SEO::render($ctx) is called once in the <head> of every page. It outputs a
 * complete, correct set of meta tags and delegates JSON-LD to Schema (schema.php).
 *
 * Fixes baked in vs. the legacy site:
 *  - meta description is run through strip_tags (no raw <br>)
 *  - og:description spelled correctly
 *  - all og/twitter images are absolute URLs
 *  - og:type is honoured (product on product pages, not article)
 *  - <html lang="en"> handled in the template, not as a bogus meta tag
 */

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/schema.php';

final class SEO
{
    /**
     * @param array $ctx {
     *   title, description, keywords, og_image, og_type, canonical,
     *   schema (string|array of schema keys), schema_data (payload for schema),
     *   is_home (bool)
     * }
     */
    public static function render(array $ctx): void
    {
        $title       = $ctx['title']       ?? SITE_NAME . ' | ' . SITE_TAGLINE;
        $description  = self::clean($ctx['description'] ?? 'Send flowers, cakes, chocolates and gifts online with same-day delivery across India.');
        $keywords    = $ctx['keywords']    ?? 'flowers online, send flowers, cake delivery, gifts, Pune florist';
        $ogType      = $ctx['og_type']     ?? 'website';
        $canonical   = self::absolute($ctx['canonical'] ?? self::currentUrl());
        $ogImage     = self::absolute($ctx['og_image'] ?? asset('img/banners/banner-1.webp'));
        $isHome      = !empty($ctx['is_home']);

        echo "\n<!-- SEO -->\n";
        echo '<title>' . e($title) . "</title>\n";
        echo '<meta name="description" content="' . e($description) . "\">\n";
        echo '<meta name="keywords" content="' . e($keywords) . "\">\n";
        echo '<link rel="canonical" href="' . e($canonical) . "\">\n";

        // Open Graph
        echo '<meta property="og:site_name" content="' . e(SITE_NAME) . "\">\n";
        echo '<meta property="og:title" content="' . e($title) . "\">\n";
        echo '<meta property="og:description" content="' . e($description) . "\">\n";
        echo '<meta property="og:image" content="' . e($ogImage) . "\">\n";
        echo '<meta property="og:url" content="' . e($canonical) . "\">\n";
        echo '<meta property="og:type" content="' . e($ogType) . "\">\n";
        echo '<meta property="og:locale" content="en_IN">' . "\n";

        // Twitter Card
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
        echo '<meta name="twitter:title" content="' . e($title) . "\">\n";
        echo '<meta name="twitter:description" content="' . e($description) . "\">\n";
        echo '<meta name="twitter:image" content="' . e($ogImage) . "\">\n";

        // hreflang on homepage only
        if ($isHome) {
            echo '<link rel="alternate" hreflang="en-in" href="' . e(BASE_URL . '/') . "\">\n";
            echo '<link rel="alternate" hreflang="x-default" href="' . e(BASE_URL . '/') . "\">\n";
        }

        // Verification
        if (GOOGLE_SITE_VERIFICATION !== '') {
            echo '<meta name="google-site-verification" content="' . e(GOOGLE_SITE_VERIFICATION) . "\">\n";
        }
        if (PINTEREST_VERIFICATION !== '') {
            echo '<meta name="p:domain_verify" content="' . e(PINTEREST_VERIFICATION) . "\">\n";
        }

        // Non-standard but harmless legacy meta the client wants retained
        echo '<meta name="author" content="' . e(SITE_NAME) . "\">\n";
        echo '<meta name="copyright" content="' . e(SITE_NAME) . "\">\n";
        echo '<meta name="distribution" content="global">' . "\n";
        echo '<meta name="revisit-after" content="7 days">' . "\n";
        echo '<meta name="geo.region" content="IN-MH">' . "\n";
        echo '<meta name="geo.placename" content="Pune">' . "\n";
        echo '<meta name="robots" content="index, follow">' . "\n";

        // JSON-LD
        if (!empty($ctx['schema'])) {
            Schema::render($ctx['schema'], $ctx['schema_data'] ?? []);
        }
        echo "<!-- /SEO -->\n";
    }

    /** Strip tags + collapse whitespace for meta description. */
    private static function clean(string $text): string
    {
        $text = strip_tags($text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    /** Force an absolute URL. */
    private static function absolute(string $url): string
    {
        if (preg_match('~^https?://~i', $url)) {
            return $url;
        }
        return BASE_URL . '/' . ltrim($url, '/');
    }

    /** Best-effort canonical for the current request (always trailing slash). */
    private static function currentUrl(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $path = rtrim($path, '/') . '/';
        return BASE_URL . $path;
    }

    /** GTM head snippet — call once inside <head>. */
    public static function gtmHead(): void
    {
        ?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?= e(GTM_ID) ?>');</script>
<!-- End Google Tag Manager -->
<?php
    }

    /** GTM body noscript — call once immediately after <body>. */
    public static function gtmBody(): void
    {
        ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= e(GTM_ID) ?>"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<?php
    }

    /** Single GA4 gtag.js loader — call once in <head>. */
    public static function ga4(): void
    {
        ?>
<!-- Google Analytics 4 (single load) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= e(GA4_ID) ?>"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '<?= e(GA4_ID) ?>');
</script>
<?php
    }
}
