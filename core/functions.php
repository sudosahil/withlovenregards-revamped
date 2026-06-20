<?php
/**
 * Shared helper functions and the data-access layer.
 *
 * All data accessors are written to mirror a prepared-statement DB call so that
 * swapping placeholder mode for live PDO is a localised change. While
 * $use_placeholder is true they read from data/placeholder_data.php.
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../data/placeholder_data.php';

// ---------------------------------------------------------------------------
// Session bootstrap
// ---------------------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ===========================================================================
// Output / escaping
// ===========================================================================

/** Escape a string for safe HTML output. */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Format a numeric amount as a currency string, e.g. "Rs. 1,299". */
function price(float|int|string $amount): string
{
    return CURRENCY . ' ' . number_format((float) $amount, 0, '.', ',');
}

/** Build an absolute URL from a site-relative path. */
function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

/** Build an absolute asset URL from a path relative to /assets (e.g. "css/style.css"). */
function asset(string $path): string
{
    return ASSET_URL . '/' . ltrim($path, '/');
}

/**
 * Resolve a media path stored in the data layer (already site-absolute, e.g.
 * "/assets/img/products/x.webp") to a host-absolute URL. Use this for product,
 * category and banner images — NOT asset(), which would double the /assets prefix.
 */
function media(string $path): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Slugify a string: lowercase, hyphenated, ascii-safe.
 */
function slugify(string $text): string
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text) ?: $text;
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return strtolower($text) ?: 'n-a';
}

// ===========================================================================
// CSRF
// ===========================================================================

/** Get (creating if needed) the per-session CSRF token. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Render a hidden CSRF input field. */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/** Validate a submitted CSRF token in constant time. */
function csrf_verify(?string $token): bool
{
    return is_string($token)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

// ===========================================================================
// Same-day delivery cutoff (server-evaluated in IST)
// ===========================================================================

/**
 * Whether today is still orderable for same-day delivery.
 * Evaluated against Asia/Kolkata regardless of the visitor's timezone.
 */
function same_day_available(): bool
{
    $nowIst = new DateTime('now', new DateTimeZone(TIMEZONE));
    return (int) $nowIst->format('G') < SAME_DAY_CUTOFF_HOUR;
}

/**
 * Earliest selectable delivery date (Y-m-d) given the 5 PM IST cutoff.
 * Returns today if still orderable, otherwise tomorrow.
 */
function earliest_delivery_date(): string
{
    $nowIst = new DateTime('now', new DateTimeZone(TIMEZONE));
    if (!same_day_available()) {
        $nowIst->modify('+1 day');
    }
    return $nowIst->format('Y-m-d');
}

// ===========================================================================
// Data access — CATEGORIES
// ===========================================================================

function get_categories(): array
{
    // Live equivalent: SELECT ... FROM categories ORDER BY id
    return wlnr_data()['categories'];
}

function get_top_categories(): array
{
    return array_values(array_filter(get_categories(), fn($c) => $c['parent_id'] === null));
}

function get_subcategories(int $parentId): array
{
    return array_values(array_filter(get_categories(), fn($c) => $c['parent_id'] === $parentId));
}

function get_category_by_slug(string $slug): ?array
{
    foreach (get_categories() as $c) {
        if ($c['slug'] === $slug) {
            return $c;
        }
    }
    return null;
}

function get_category_by_id(int $id): ?array
{
    foreach (get_categories() as $c) {
        if ($c['id'] === $id) {
            return $c;
        }
    }
    return null;
}

// ===========================================================================
// Data access — PRODUCTS
// ===========================================================================

function get_products(): array
{
    return wlnr_data()['products'];
}

function get_product_by_slug(string $slug): ?array
{
    foreach (get_products() as $p) {
        if ($p['slug'] === $slug) {
            return $p;
        }
    }
    return null;
}

function get_product_by_id(int $id): ?array
{
    foreach (get_products() as $p) {
        if ((int) $p['id'] === $id) {
            return $p;
        }
    }
    return null;
}

function get_featured_products(int $limit = 8): array
{
    $out = array_values(array_filter(get_products(), fn($p) => !empty($p['is_featured'])));
    return array_slice($out, 0, $limit);
}

function get_bestseller_products(int $limit = 8): array
{
    $out = array_values(array_filter(get_products(), fn($p) => !empty($p['is_bestseller'])));
    return array_slice($out, 0, $limit);
}

/**
 * Products in a top-level category (optionally a subcategory).
 */
function get_products_by_category(int $categoryId, ?int $subcategoryId = null): array
{
    return array_values(array_filter(get_products(), function ($p) use ($categoryId, $subcategoryId) {
        if ((int) $p['category_id'] !== $categoryId) {
            return false;
        }
        if ($subcategoryId !== null) {
            return (int) ($p['subcategory_id'] ?? 0) === $subcategoryId;
        }
        return true;
    }));
}

/** Similar products: same category, excluding the given product. */
function get_similar_products(array $product, int $limit = 8): array
{
    $out = array_values(array_filter(get_products(), fn($p) =>
        (int) $p['category_id'] === (int) $product['category_id'] && $p['id'] !== $product['id']));
    return array_slice($out, 0, $limit);
}

/** Naive product search over name + short description. */
function search_products(string $term): array
{
    $term = trim(mb_strtolower($term));
    if ($term === '') {
        return [];
    }
    return array_values(array_filter(get_products(), function ($p) use ($term) {
        $haystack = mb_strtolower($p['name'] . ' ' . $p['short_description'] . ' ' . $p['type']);
        return str_contains($haystack, $term);
    }));
}

/** The effective sale-aware unit price of a product. */
function effective_price(array $product): float
{
    $sale = $product['sale_price'] ?? null;
    if ($sale !== null && (float) $sale > 0) {
        return (float) $sale;
    }
    return (float) $product['price'];
}

function is_on_sale(array $product): bool
{
    return !empty($product['sale_price']) && (float) $product['sale_price'] < (float) $product['price'];
}

// ===========================================================================
// Data access — ORDERS / CUSTOMERS / ABANDONED / ANALYTICS
// ===========================================================================

function get_orders(): array
{
    return wlnr_data()['orders'];
}

function get_order_by_id(int $id): ?array
{
    foreach (get_orders() as $o) {
        if ((int) $o['id'] === $id) {
            return $o;
        }
    }
    return null;
}

function get_orders_by_customer(int $customerId): array
{
    return array_values(array_filter(get_orders(), fn($o) => (int) $o['customer_id'] === $customerId));
}

function get_customers(): array
{
    return wlnr_data()['customers'];
}

function get_customer_by_id(int $id): ?array
{
    foreach (get_customers() as $c) {
        if ((int) $c['id'] === $id) {
            return $c;
        }
    }
    return null;
}

function get_abandoned_carts(): array
{
    return wlnr_data()['abandoned_carts'];
}

function get_analytics(): array
{
    return wlnr_data()['analytics'];
}

function get_faqs(): array
{
    return wlnr_data()['faqs'];
}

// ===========================================================================
// Session cart
// ===========================================================================

/** Cart shape: $_SESSION['cart'][product_id] = [id, qty, price, name, image, slug]. */
function cart_items(): array
{
    return $_SESSION['cart'] ?? [];
}

function cart_count(): int
{
    return array_sum(array_map(fn($i) => (int) $i['qty'], cart_items()));
}

function cart_subtotal(): float
{
    return array_sum(array_map(fn($i) => (float) $i['price'] * (int) $i['qty'], cart_items()));
}

/** Add (or increment) a product in the cart. Prices are sourced server-side. */
function cart_add(int $productId, int $qty = 1): bool
{
    $product = get_product_by_id($productId);
    if (!$product || $qty < 1) {
        return false;
    }
    $cart = cart_items();
    if (isset($cart[$productId])) {
        $cart[$productId]['qty'] += $qty;
    } else {
        $cart[$productId] = [
            'id'    => $product['id'],
            'qty'   => $qty,
            'price' => effective_price($product),
            'name'  => $product['name'],
            'image' => $product['image'],
            'slug'  => $product['slug'],
        ];
    }
    $_SESSION['cart'] = $cart;
    return true;
}

function cart_update(int $productId, int $qty): bool
{
    $cart = cart_items();
    if (!isset($cart[$productId])) {
        return false;
    }
    if ($qty < 1) {
        unset($cart[$productId]);
    } else {
        $cart[$productId]['qty'] = $qty;
    }
    $_SESSION['cart'] = $cart;
    return true;
}

function cart_remove(int $productId): bool
{
    $cart = cart_items();
    if (!isset($cart[$productId])) {
        return false;
    }
    unset($cart[$productId]);
    $_SESSION['cart'] = $cart;
    return true;
}

function cart_clear(): void
{
    unset($_SESSION['cart']);
}

/**
 * Recompute a trusted total from server-side prices for a set of submitted
 * line items. Used at checkout — the client total is never trusted.
 *
 * @param array $submitted [[product_id, qty], ...]
 * @return array{total: float, items: array, tampered: bool}
 */
function recalculate_cart(array $submitted): array
{
    $total = 0.0;
    $items = [];
    $tampered = false;
    foreach ($submitted as $row) {
        $product = get_product_by_id((int) ($row['product_id'] ?? 0));
        $qty = max(1, (int) ($row['qty'] ?? 1));
        if (!$product) {
            $tampered = true;
            continue;
        }
        $unit = effective_price($product);
        if (isset($row['price']) && abs((float) $row['price'] - $unit) > 0.001) {
            $tampered = true; // client sent a price that doesn't match the server
        }
        $line = $unit * $qty;
        $total += $line;
        $items[] = [
            'product_id' => $product['id'],
            'name'       => $product['name'],
            'qty'        => $qty,
            'price'      => $unit,
            'line_total' => $line,
        ];
    }
    return ['total' => $total, 'items' => $items, 'tampered' => $tampered];
}

// ===========================================================================
// Wishlist (session)
// ===========================================================================

function wishlist_items(): array
{
    return $_SESSION['wishlist'] ?? [];
}

function wishlist_count(): int
{
    return count(wishlist_items());
}

function wishlist_toggle(int $productId): bool
{
    $list = wishlist_items();
    $key = array_search($productId, $list, true);
    if ($key !== false) {
        unset($list[$key]);
        $_SESSION['wishlist'] = array_values($list);
        return false; // now removed
    }
    if (get_product_by_id($productId)) {
        $list[] = $productId;
        $_SESSION['wishlist'] = array_values($list);
        return true; // now added
    }
    return false;
}

function in_wishlist(int $productId): bool
{
    return in_array($productId, wishlist_items(), true);
}

// ===========================================================================
// Auth helpers (customer + admin sessions kept separate)
// ===========================================================================

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: ' . url('login-register'));
        exit;
    }
}

function is_admin(): bool
{
    return !empty($_SESSION['admin_logged_in']);
}

function require_admin(): void
{
    if (!is_admin()) {
        header('Location: ' . url('admin/'));
        exit;
    }
}

// ===========================================================================
// JSON response helper for API endpoints
// ===========================================================================

/** Emit a JSON envelope { data, error } and stop. */
function json_response($data = null, ?array $error = null, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['data' => $data, 'error' => $error], JSON_UNESCAPED_SLASHES);
    exit;
}

// ===========================================================================
// Misc view helpers
// ===========================================================================

/** Human-readable "time since" string. */
function time_ago(string $datetime): string
{
    $then = strtotime($datetime);
    $diff = max(0, time() - $then);
    if ($diff < 3600) {
        $m = max(1, (int) ($diff / 60));
        return $m . ' min' . ($m > 1 ? 's' : '') . ' ago';
    }
    if ($diff < 86400) {
        $h = (int) ($diff / 3600);
        return $h . ' hour' . ($h > 1 ? 's' : '') . ' ago';
    }
    $d = (int) ($diff / 86400);
    return $d . ' day' . ($d > 1 ? 's' : '') . ' ago';
}

/** Map an order/recovery status to a CSS badge modifier. */
function status_class(string $status): string
{
    return 'badge--' . slugify($status);
}

/** Load homepage editor config (data/homepage_config.json) with sane defaults. */
function homepage_config(): array
{
    static $cfg = null;
    if ($cfg !== null) {
        return $cfg;
    }
    $file = ROOT_PATH . '/data/homepage_config.json';
    $cfg = [];
    if (file_exists($file)) {
        $decoded = json_decode((string) file_get_contents($file), true);
        if (is_array($decoded)) {
            $cfg = $decoded;
        }
    }
    return $cfg;
}

/** Load site settings (config/settings.json) written by the admin settings page. */
function site_settings(): array
{
    static $s = null;
    if ($s !== null) {
        return $s;
    }
    $file = ROOT_PATH . '/config/settings.json';
    $s = [];
    if (file_exists($file)) {
        $decoded = json_decode((string) file_get_contents($file), true);
        if (is_array($decoded)) {
            $s = $decoded;
        }
    }
    return $s;
}

/** Build the canonical category URL (clean path). */
function category_url(array $category): string
{
    if ($category['parent_id'] === null) {
        return url($category['slug']);
    }
    $parent = get_category_by_id((int) $category['parent_id']);
    return url(($parent['slug'] ?? 'shop') . '/' . $category['slug']);
}

/** Build the canonical product URL using its category path. */
function product_url(array $product): string
{
    $cat = get_category_by_id((int) $product['category_id']);
    $path = $cat['slug'] ?? 'shop';
    if (!empty($product['subcategory_id'])) {
        $sub = get_category_by_id((int) $product['subcategory_id']);
        if ($sub) {
            $path .= '/' . $sub['slug'];
        }
    }
    return url($path . '/' . $product['slug']);
}
