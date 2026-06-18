<?php
/**
 * Reusable product card partial.
 * Expects $product (array) in scope. Used by homepage, category, search, wishlist.
 */
if (!isset($product) || !is_array($product)) {
    return;
}
$onSale = is_on_sale($product);
$wished = in_wishlist((int) $product['id']);
?>
<article class="product-card">
    <a class="product-card__media" href="<?= e(product_url($product)) ?>">
        <?php if ($onSale): ?>
            <span class="badge badge--sale product-card__sale">Sale</span>
        <?php endif; ?>
        <img src="<?= e(media(ltrim($product['image'], '/'))) ?>"
             alt="<?= e($product['name']) ?>" width="300" height="300" loading="lazy">
    </a>
    <button class="product-card__wish<?= $wished ? ' active' : '' ?>"
            data-wishlist="<?= (int) $product['id'] ?>" aria-label="Add to wishlist">
        <i class="fa-solid fa-heart"></i>
    </button>
    <div class="product-card__body">
        <h3 class="product-card__title">
            <a href="<?= e(product_url($product)) ?>"><?= e($product['name']) ?></a>
        </h3>
        <div class="product-card__price">
            <?= e(price(effective_price($product))) ?>
            <?php if ($onSale): ?>
                <span class="old"><?= e(price($product['price'])) ?></span>
            <?php endif; ?>
        </div>
        <div class="product-card__actions">
            <button class="btn btn--primary product-card__cart" data-add-cart="<?= (int) $product['id'] ?>">
                <i class="fa-solid fa-bag-shopping"></i> Add to Cart
            </button>
        </div>
    </div>
</article>
