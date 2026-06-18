<?php
/**
 * Cart page. Session-based; line edits and removals go through api/cart.php.
 */
require_once __DIR__ . '/../includes/functions.php';

$items = cart_items();
$subtotal = cart_subtotal();
$deliveryFee = 0.0; // free shipping placeholder
$total = $subtotal + $deliveryFee;

$seo = [
    'title'       => 'Your Cart | ' . SITE_NAME,
    'description' => 'Review the flowers, cakes and gifts in your cart before checkout.',
    'canonical'   => BASE_URL . '/cart/',
];
require __DIR__ . '/../includes/header.php';
?>
<main class="container">
    <h1 class="section__title" style="margin:24px 0;">Shopping Cart</h1>

    <?php if (!$items): ?>
        <div class="empty-state">
            <i class="fa-solid fa-bag-shopping"></i>
            <h3>Your cart is empty</h3>
            <p>Looks like you haven't added anything yet.</p>
            <a class="btn btn--primary" href="<?= e(url('flowers')) ?>">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="cart-layout">
            <div class="cart-items">
                <?php foreach ($items as $item):
                    $line = $item['price'] * $item['qty']; ?>
                    <div class="cart-item">
                        <div class="cart-item__img">
                            <img src="<?= e(media(ltrim($item['image'], '/'))) ?>" alt="<?= e($item['name']) ?>" width="80" height="80" loading="lazy">
                        </div>
                        <div>
                            <div class="cart-item__name"><?= e($item['name']) ?></div>
                            <div class="cart-item__price"><?= e(price($item['price'])) ?> each</div>
                            <div class="qty-stepper" style="margin-top:8px;">
                                <button type="button" data-step="-1" aria-label="Decrease">−</button>
                                <input type="number" value="<?= (int) $item['qty'] ?>" min="1" data-cart-qty="<?= (int) $item['id'] ?>" aria-label="Quantity">
                                <button type="button" data-step="1" aria-label="Increase">+</button>
                            </div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-weight:700;"><?= e(price($line)) ?></div>
                            <a href="#" class="cart-item__remove" data-cart-remove="<?= (int) $item['id'] ?>"><i class="fa-solid fa-trash"></i> Remove</a>
                        </div>
                    </div>
                <?php endforeach; ?>
                <p style="margin-top:18px;"><a class="btn btn--outline" href="<?= e(url('flowers')) ?>"><i class="fa-solid fa-arrow-left"></i> Continue Shopping</a></p>
            </div>

            <aside class="cart-summary">
                <h3>Order Summary</h3>
                <div class="coupon-row">
                    <input type="text" placeholder="Coupon code">
                    <button class="btn btn--secondary">Apply</button>
                </div>
                <div class="summary-row"><span>Subtotal</span><span><?= e(price($subtotal)) ?></span></div>
                <div class="summary-row"><span>Delivery</span><span><?= $deliveryFee > 0 ? e(price($deliveryFee)) : 'Free' ?></span></div>
                <div class="summary-row summary-row--total"><span>Total</span><span><?= e(price($total)) ?></span></div>
                <a class="btn btn--primary btn--block btn--lg" href="<?= e(url('checkout')) ?>" style="margin-top:16px;">Proceed to Checkout</a>
            </aside>
        </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
