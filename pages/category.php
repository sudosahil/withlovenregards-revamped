<?php
/**
 * Category / subcategory listing with filter sidebar and URL-persisted state.
 * Routed by .htaccess: ?category=flowers[&subcategory=roses].
 * Filter params: ?price_min, ?price_max, ?sort, ?type (comma list), ?occasion.
 */
require_once __DIR__ . '/../includes/functions.php';

$catSlug = preg_replace('/[^a-z0-9\-]/', '', strtolower($_GET['category'] ?? ''));
$subSlug = preg_replace('/[^a-z0-9\-]/', '', strtolower($_GET['subcategory'] ?? ''));

$category = get_category_by_slug($catSlug);
if (!$category) {
    require __DIR__ . '/404.php';
    exit;
}
$subcategory = $subSlug ? get_category_by_slug($subSlug) : null;

// --- Resolve product set --------------------------------------------------
$products = get_products_by_category((int) $category['id'], $subcategory ? (int) $subcategory['id'] : null);

// --- Filters from URL (state persistence) --------------------------------
$priceMin = isset($_GET['price_min']) ? (float) $_GET['price_min'] : null;
$priceMax = isset($_GET['price_max']) ? (float) $_GET['price_max'] : null;
$sort = $_GET['sort'] ?? '';
$types = isset($_GET['type']) && $_GET['type'] !== '' ? explode(',', $_GET['type']) : [];

// Available type facets within this set.
$allTypes = array_values(array_unique(array_map(fn($p) => $p['type'], $products)));

$filtered = array_filter($products, function ($p) use ($priceMin, $priceMax, $types) {
    $price = effective_price($p);
    if ($priceMin !== null && $price < $priceMin) return false;
    if ($priceMax !== null && $price > $priceMax) return false;
    if ($types && !in_array($p['type'], $types, true)) return false;
    return true;
});
$filtered = array_values($filtered);

// Sorting
usort($filtered, function ($a, $b) use ($sort) {
    return match ($sort) {
        'price_asc'  => effective_price($a) <=> effective_price($b),
        'price_desc' => effective_price($b) <=> effective_price($a),
        'newest'     => strcmp($b['created_at'], $a['created_at']),
        'bestselling'=> ($b['is_bestseller'] <=> $a['is_bestseller']),
        default      => 0,
    };
});

// Pagination
$perPage = 9;
$page = max(1, (int) ($_GET['page'] ?? 1));
$totalResults = count($filtered);
$totalPages = max(1, (int) ceil($totalResults / $perPage));
$page = min($page, $totalPages);
$pageItems = array_slice($filtered, ($page - 1) * $perPage, $perPage);

// Breadcrumb + SEO
$crumbs = [['name' => 'Home', 'url' => url()]];
$crumbs[] = ['name' => $category['name'], 'url' => category_url($category)];
if ($subcategory) {
    $crumbs[] = ['name' => $subcategory['name'], 'url' => category_url($subcategory)];
}
$active = $subcategory ?: $category;
$canonical = category_url($active);

$seo = [
    'title'       => $active['meta_title'] ?: ($active['name'] . ' | ' . SITE_NAME),
    'description' => $active['meta_description'] ?: $active['description'],
    'canonical'   => $canonical,
    'og_type'     => 'website',
    'og_image'    => BASE_URL . ($active['image'] ?? '/assets/img/banners/banner-1.webp'),
    'schema'      => ['breadcrumb', 'itemlist', 'faq'],
    'schema_data' => ['breadcrumb' => $crumbs, 'items' => $pageItems, 'list_name' => $active['name'], 'faqs' => get_faqs()],
];

require __DIR__ . '/../includes/header.php';

/** Build a URL preserving the current query but overriding one key. */
function page_url(int $n): string
{
    $params = $_GET;
    unset($params['category'], $params['subcategory']); // routed, not query
    $params['page'] = $n;
    return '?' . http_build_query($params);
}
?>
<main>
    <div class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <?php foreach ($crumbs as $i => $c): ?>
                <?php if ($i > 0): ?><span>/</span><?php endif; ?>
                <a href="<?= e($c['url']) ?>"><?= e($c['name']) ?></a>
            <?php endforeach; ?>
        </nav>
    </div>

    <section class="category-hero">
        <div class="container">
            <h1><?= e($active['name']) ?></h1>
            <p><?= e($active['description']) ?></p>
        </div>
    </section>

    <div class="container section">
        <div class="filter-toolbar">
            <button class="btn btn--outline filter-toggle" id="filterToggle"><i class="fa-solid fa-sliders"></i> Filters</button>
            <span class="result-count">Showing <?= $totalResults ?> result<?= $totalResults === 1 ? '' : 's' ?></span>
            <label class="visually-hidden" for="sortSelect">Sort by</label>
            <select id="sortSelect" aria-label="Sort products">
                <option value="">Sort by: Featured</option>
                <option value="price_asc"   <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                <option value="price_desc"  <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                <option value="newest"      <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
                <option value="bestselling" <?= $sort === 'bestselling' ? 'selected' : '' ?>>Bestselling</option>
            </select>
        </div>

        <div class="category-layout">
            <!-- Filter sidebar -->
            <aside class="filter-sidebar" id="filterSidebar">
                <form id="filterForm">
                    <div class="filter-group">
                        <h4>Price Range</h4>
                        <div class="price-range">
                            <input type="number" id="priceMin" placeholder="Min" min="0" value="<?= $priceMin !== null ? e((string) $priceMin) : '' ?>">
                            <span>—</span>
                            <input type="number" id="priceMax" placeholder="Max" min="0" value="<?= $priceMax !== null ? e((string) $priceMax) : '' ?>">
                        </div>
                    </div>
                    <?php if ($allTypes): ?>
                    <div class="filter-group">
                        <h4>Type</h4>
                        <?php foreach ($allTypes as $t): ?>
                            <label class="filter-check">
                                <input type="checkbox" name="type" value="<?= e($t) ?>" <?= in_array($t, $types, true) ? 'checked' : '' ?>>
                                <?= e($t) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <div class="filter-group">
                        <button type="button" class="btn btn--primary btn--block" id="applyFilters">Apply Filters</button>
                        <button type="button" class="btn btn--outline btn--block" id="clearFilters" style="margin-top:10px;">Clear</button>
                    </div>
                </form>
            </aside>

            <!-- Product grid -->
            <div>
                <?php if ($pageItems): ?>
                    <div class="product-grid">
                        <?php foreach ($pageItems as $product) {
                            include __DIR__ . '/../includes/product-card.php';
                        } ?>
                    </div>

                    <?php if ($totalPages > 1): ?>
                        <nav class="pagination" aria-label="Pagination">
                            <?php if ($page > 1): ?><a href="<?= e(page_url($page - 1)) ?>">&laquo; Prev</a><?php endif; ?>
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php if ($i === $page): ?>
                                    <span class="current"><?= $i ?></span>
                                <?php else: ?>
                                    <a href="<?= e(page_url($i)) ?>"><?= $i ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>
                            <?php if ($page < $totalPages): ?><a href="<?= e(page_url($page + 1)) ?>">Next &raquo;</a><?php endif; ?>
                        </nav>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fa-regular fa-face-frown"></i>
                        <h3>No products match your filters</h3>
                        <p>Try widening your price range or clearing filters.</p>
                        <a class="btn btn--primary" href="<?= e(category_url($active)) ?>">Reset</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- SEO content -->
    <section class="section seo-content">
        <div class="container">
            <h2>About <?= e($active['name']) ?></h2>
            <p><?= e($active['description']) ?> At <?= e(SITE_NAME) ?>, every order is hand-prepared and delivered fresh with same-day options across our serviceable cities. Order before 5 PM IST for same-day delivery.</p>
            <h2>Why Shop <?= e($active['name']) ?> With Us</h2>
            <p>Fresh quality, fair prices and reliable doorstep delivery. Browse our full range of <?= e(strtolower($active['name'])) ?> and find the perfect gift for every occasion.</p>
        </div>
    </section>

    <!-- FAQ -->
    <section class="section container">
        <h2 class="section__title">Frequently Asked Questions</h2>
        <div class="faq-list">
            <?php foreach (get_faqs() as $faq): ?>
                <div class="faq-item">
                    <button class="faq-item__q"><?= e($faq['q']) ?> <i class="fa-solid fa-plus"></i></button>
                    <div class="faq-item__a"><p><?= e($faq['a']) ?></p></div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
