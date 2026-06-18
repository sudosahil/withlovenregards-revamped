<?php
/**
 * Search results page. ?q= term.
 */
require_once __DIR__ . '/../includes/functions.php';

$term = trim((string) ($_GET['q'] ?? ''));
$results = $term !== '' ? search_products($term) : [];

$seo = [
    'title'       => ($term !== '' ? 'Search: ' . $term : 'Search') . ' | ' . SITE_NAME,
    'description' => 'Search results for flowers, cakes and gifts.',
    'canonical'   => BASE_URL . '/search/',
];
require __DIR__ . '/../includes/header.php';
?>
<main class="container">
    <h1 class="section__title" style="margin:24px 0;">
        <?= $term !== '' ? 'Results for &ldquo;' . e($term) . '&rdquo;' : 'Search' ?>
    </h1>
    <p class="result-count" style="text-align:center;margin-bottom:20px;"><?= count($results) ?> product<?= count($results) === 1 ? '' : 's' ?> found</p>

    <?php if (!$results): ?>
        <div class="empty-state">
            <i class="fa-solid fa-magnifying-glass"></i>
            <h3>No products found</h3>
            <p>Try a different keyword, or browse our categories.</p>
            <a class="btn btn--primary" href="<?= e(url('flowers')) ?>">Browse Flowers</a>
        </div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($results as $product) {
                include __DIR__ . '/../includes/product-card.php';
            } ?>
        </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
