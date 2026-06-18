<?php
/**
 * Single product page. Routed by .htaccess: ?slug=red-roses-bunch.
 * Product title is the single H1. Delivery datepicker enforces the 5 PM IST
 * cutoff (resolved server-side, see WLNR.earliestDeliveryDate in footer.php).
 */
require_once __DIR__ . '/../includes/functions.php';

$slug = preg_replace('/[^a-z0-9\-]/', '', strtolower($_GET['slug'] ?? ''));
$product = get_product_by_slug($slug);
if (!$product) {
    require __DIR__ . '/404.php';
    exit;
}

$category = get_category_by_id((int) $product['category_id']);
$subcategory = !empty($product['subcategory_id']) ? get_category_by_id((int) $product['subcategory_id']) : null;
$onSale = is_on_sale($product);
$inStock = (int) $product['stock'] > 0;
$similar = get_similar_products($product, 8);

// Breadcrumb
$crumbs = [['name' => 'Home', 'url' => url()]];
if ($category)    $crumbs[] = ['name' => $category['name'], 'url' => category_url($category)];
if ($subcategory) $crumbs[] = ['name' => $subcategory['name'], 'url' => category_url($subcategory)];
$crumbs[] = ['name' => $product['name'], 'url' => product_url($product)];

$seo = [
    'title'       => $product['meta_title'] ?: ($product['name'] . ' | ' . SITE_NAME),
    'description' => $product['meta_description'] ?: $product['short_description'],
    'canonical'   => product_url($product),
    'og_type'     => 'product',   // fix: product, not article
    'og_image'    => BASE_URL . $product['image'],
    'schema'      => ['product', 'breadcrumb'],
    'schema_data' => ['product' => $product, 'breadcrumb' => $crumbs],
];

require __DIR__ . '/../includes/header.php';

$shareUrl = rawurlencode(product_url($product));
$shareText = rawurlencode($product['name'] . ' — ' . SITE_NAME);
?>
<main>
    <div class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <?php foreach ($crumbs as $i => $c): ?>
                <?php if ($i > 0): ?><span>/</span><?php endif; ?>
                <a href="<?= e($c['url']) ?>"><?= e($c['name']) ?></a>
            <?php endforeach; ?>
        </nav>

        <div class="product-detail">
            <div class="product-gallery">
                <img src="<?= e(media(ltrim($product['image'], '/'))) ?>"
                     alt="<?= e($product['name']) ?>" width="600" height="600" fetchpriority="high">
            </div>

            <div class="product-info">
                <!-- Single H1 -->
                <h1><?= e($product['name']) ?></h1>

                <div class="product-info__price">
                    <?= e(price(effective_price($product))) ?>
                    <?php if ($onSale): ?><span class="old"><?= e(price($product['price'])) ?></span><?php endif; ?>
                </div>

                <p class="product-info__stock <?= $inStock ? 'in-stock' : 'out-stock' ?>">
                    <i class="fa-solid <?= $inStock ? 'fa-circle-check' : 'fa-circle-xmark' ?>"></i>
                    <?= $inStock ? 'In Stock' : 'Out of Stock' ?>
                </p>

                <p><?= e($product['short_description']) ?></p>

                <table class="spec-list">
                    <tr><th>Type</th><td><?= e($product['type']) ?></td></tr>
                    <tr><th>Weight / Size</th><td><?= e($product['weight']) ?></td></tr>
                    <tr><th>Category</th><td><?= e($category['name'] ?? '—') ?></td></tr>
                    <tr><th>SKU</th><td>WLNR-<?= (int) $product['id'] ?></td></tr>
                </table>

                <!-- Delivery date picker -->
                <div class="delivery-picker">
                    <label for="deliveryDate">Select Delivery Date</label>
                    <input type="text" id="deliveryDate" class="js-datepicker" placeholder="Choose a date" readonly
                           value="<?= e(earliest_delivery_date()) ?>">
                    <p class="cutoff-note" <?= same_day_available() ? 'style="display:none"' : '' ?>>
                        <i class="fa-solid fa-clock"></i> Same-day delivery has closed for today (after 5 PM IST). Earliest delivery is tomorrow.
                    </p>
                </div>

                <!-- Buy row -->
                <div class="product-buy">
                    <div class="qty-stepper">
                        <button type="button" data-step="-1" aria-label="Decrease quantity">−</button>
                        <input type="number" id="qtyInput" value="1" min="1" aria-label="Quantity">
                        <button type="button" data-step="1" aria-label="Increase quantity">+</button>
                    </div>
                    <button class="btn btn--primary btn--lg" data-add-cart="<?= (int) $product['id'] ?>" <?= $inStock ? '' : 'disabled' ?>>
                        <i class="fa-solid fa-bag-shopping"></i> Add to Cart
                    </button>
                    <button class="btn btn--outline" data-wishlist="<?= (int) $product['id'] ?>">
                        <i class="fa-solid fa-heart"></i> Wishlist
                    </button>
                </div>

                <!-- Social share -->
                <div class="share-row">
                    <span>Share:</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>" target="_blank" rel="noopener" aria-label="Share on Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://pinterest.com/pin/create/button/?url=<?= $shareUrl ?>&description=<?= $shareText ?>" target="_blank" rel="noopener" aria-label="Pin on Pinterest"><i class="fa-brands fa-pinterest-p"></i></a>
                    <a href="https://wa.me/?text=<?= $shareText ?>%20<?= $shareUrl ?>" target="_blank" rel="noopener" aria-label="Share on WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                </div>
            </div>
        </div>

        <!-- Description -->
        <section class="section">
            <h2 class="section__title" style="text-align:left;">Product Description</h2>
            <p><?= e($product['description']) ?></p>
        </section>
    </div>

    <!-- Similar products -->
    <?php if ($similar): ?>
    <section class="section container">
        <h2 class="section__title">You May Also Like</h2>
        <div class="carousel">
            <button class="carousel__arrow carousel__arrow--prev" aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></button>
            <div class="carousel__viewport">
                <div class="carousel__track">
                    <?php foreach ($similar as $product) {
                        include __DIR__ . '/../includes/product-card.php';
                    } ?>
                </div>
            </div>
            <button class="carousel__arrow carousel__arrow--next" aria-label="Next"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
    </section>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
