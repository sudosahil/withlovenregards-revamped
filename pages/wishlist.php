<?php
/**
 * Standalone wishlist page (session-based, available to guests).
 */
require_once __DIR__ . '/../includes/functions.php';

$wishProducts = array_values(array_filter(array_map(fn($id) => get_product_by_id((int) $id), wishlist_items())));

$seo = [
    'title'       => 'My Wishlist | ' . SITE_NAME,
    'description' => 'Your saved flowers, cakes and gifts.',
    'canonical'   => BASE_URL . '/wishlist/',
];
require __DIR__ . '/../includes/header.php';
?>
<main class="container">
    <h1 class="section__title" style="margin:24px 0;">My Wishlist</h1>
    <?php if (!$wishProducts): ?>
        <div class="empty-state">
            <i class="fa-regular fa-heart"></i>
            <h3>Your wishlist is empty</h3>
            <p>Tap the heart on any product to save it here.</p>
            <a class="btn btn--primary" href="<?= e(url('flowers')) ?>">Browse Products</a>
        </div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($wishProducts as $product) {
                include __DIR__ . '/../includes/product-card.php';
            } ?>
        </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
