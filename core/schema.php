<?php
/**
 * JSON-LD structured data builder.
 *
 * Schema::render($keys, $data) prints one <script type="application/ld+json">
 * block per requested schema. $keys may be a string or an array of:
 *   website | organization | product | breadcrumb | faq | itemlist
 */

require_once __DIR__ . '/functions.php';

final class Schema
{
    /** @param string|array $keys */
    public static function render($keys, array $data = []): void
    {
        $keys = (array) $keys;
        foreach ($keys as $key) {
            $node = match ($key) {
                'website'      => self::website(),
                'organization' => self::organization(),
                'product'      => self::product($data['product'] ?? null),
                'breadcrumb'   => self::breadcrumb($data['breadcrumb'] ?? []),
                'faq'          => self::faq($data['faqs'] ?? get_faqs()),
                'itemlist'     => self::itemList($data['items'] ?? [], $data['list_name'] ?? SITE_NAME),
                default        => null,
            };
            if ($node) {
                self::emit($node);
            }
        }
    }

    private static function emit(array $node): void
    {
        echo '<script type="application/ld+json">'
            . json_encode($node, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            . "</script>\n";
    }

    private static function website(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type'    => 'WebSite',
            'name'     => SITE_NAME,
            'url'      => BASE_URL . '/',
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => [
                    '@type'       => 'EntryPoint',
                    'urlTemplate' => BASE_URL . '/search?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    private static function organization(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => SITE_NAME,
            'url'      => BASE_URL . '/',
            'logo'     => asset('img/logo/logo.png'),
            'email'    => ADMIN_EMAIL,
            'telephone'=> CONTACT_PHONE,
            'sameAs'   => [SOCIAL_FACEBOOK, SOCIAL_INSTAGRAM, SOCIAL_TWITTER, SOCIAL_PINTEREST],
            'address'  => [
                '@type'           => 'PostalAddress',
                'addressLocality' => 'Pune',
                'addressRegion'   => 'Maharashtra',
                'addressCountry'  => 'IN',
            ],
        ];
    }

    private static function product(?array $product): ?array
    {
        if (!$product) {
            return null;
        }
        $available = ((int) ($product['stock'] ?? 0) > 0)
            ? 'https://schema.org/InStock'
            : 'https://schema.org/OutOfStock';

        return [
            '@context'    => 'https://schema.org',
            '@type'       => 'Product',
            'name'        => $product['name'],
            'description' => strip_tags($product['short_description'] ?? $product['description'] ?? ''),
            'image'       => BASE_URL . $product['image'],
            'sku'         => 'WLNR-' . $product['id'],
            'brand'       => ['@type' => 'Brand', 'name' => SITE_NAME],
            'offers'      => [
                '@type'         => 'Offer',
                'url'           => product_url($product),
                'priceCurrency' => CURRENCY_CODE,
                'price'         => number_format(effective_price($product), 2, '.', ''),
                'availability'  => $available,
                'seller'        => ['@type' => 'Organization', 'name' => SITE_NAME],
            ],
        ];
    }

    /** @param array $crumbs [[name, url], ...] */
    private static function breadcrumb(array $crumbs): ?array
    {
        if (!$crumbs) {
            return null;
        }
        $items = [];
        foreach ($crumbs as $i => $crumb) {
            $items[] = [
                '@type'    => 'ListItem',
                'position' => $i + 1,
                'name'     => $crumb['name'],
                'item'     => $crumb['url'],
            ];
        }
        return [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    /** @param array $faqs [[q, a], ...] */
    private static function faq(array $faqs): ?array
    {
        if (!$faqs) {
            return null;
        }
        $entries = [];
        foreach ($faqs as $faq) {
            $entries[] = [
                '@type'          => 'Question',
                'name'           => $faq['q'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['a']],
            ];
        }
        return [
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => $entries,
        ];
    }

    /** @param array $items product rows */
    private static function itemList(array $items, string $name): ?array
    {
        if (!$items) {
            return null;
        }
        $elements = [];
        foreach ($items as $i => $product) {
            $elements[] = [
                '@type'    => 'ListItem',
                'position' => $i + 1,
                'url'      => product_url($product),
                'name'     => $product['name'],
            ];
        }
        return [
            '@context'        => 'https://schema.org',
            '@type'           => 'ItemList',
            'name'            => $name,
            'itemListElement' => $elements,
        ];
    }
}
