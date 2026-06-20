<?php
/**
 * Checkout page. Redirects to homepage if the cart is empty. Posts to
 * api/order.php which recomputes the total server-side and hands off to CC Avenue.
 */
require_once __DIR__ . '/../core/functions.php';

$items = cart_items();
if (!$items) {
    header('Location: ' . url());
    exit;
}

$subtotal = cart_subtotal();
$deliveryFee = 0.0;
$total = $subtotal + $deliveryFee;

$error = $_SESSION['checkout_error'] ?? '';
$old = $_SESSION['checkout_old'] ?? [];
unset($_SESSION['checkout_error'], $_SESSION['checkout_old']);

$user = current_user();
$old['email'] = $old['email'] ?? ($user['email'] ?? '');
$old['phone'] = $old['phone'] ?? ($user['phone'] ?? '');

$seo = [
    'title'       => 'Checkout | ' . SITE_NAME,
    'description' => 'Complete your order securely.',
    'canonical'   => BASE_URL . '/checkout/',
];
require __DIR__ . '/../core/header.php';

$slots = ['Morning (8-12)', 'Afternoon (12-4)', 'Evening (4-8)', 'Midnight (11-1)'];
$v = fn(string $k) => e((string) ($old[$k] ?? ''));
?>
<main class="container">
    <h1 class="section__title" style="margin:24px 0;">Checkout</h1>

    <?php if ($error): ?>
        <div class="alert alert--error"><?= e($error) ?></div>
    <?php endif; ?>

    <form action="<?= e(url('api/order.php')) ?>" method="post">
        <?= csrf_field() ?>
        <div class="checkout-layout">
            <div>
                <div class="card-box">
                    <h3>Contact &amp; Delivery</h3>
                    <div class="form-grid">
                        <div class="form-field"><label>First Name *</label><input type="text" name="first_name" value="<?= $v('first_name') ?>" required></div>
                        <div class="form-field"><label>Last Name</label><input type="text" name="last_name" value="<?= $v('last_name') ?>"></div>
                        <div class="form-field"><label>Email *</label><input type="email" name="email" value="<?= $v('email') ?>" required></div>
                        <div class="form-field"><label>Phone *</label><input type="tel" name="phone" value="<?= $v('phone') ?>" required></div>
                        <div class="form-field form-field--full"><label>Delivery Address *</label><input type="text" name="address" value="<?= $v('address') ?>" placeholder="House no, street, area" required></div>
                        <div class="form-field"><label>City *</label><input type="text" name="city" value="<?= $v('city') ?>" required></div>
                        <div class="form-field"><label>State</label><input type="text" name="state" value="<?= $v('state') ?>"></div>
                        <div class="form-field"><label>Pincode *</label><input type="text" name="pincode" value="<?= $v('pincode') ?>" inputmode="numeric" maxlength="6" required></div>
                        <div class="form-field">
                            <label>Delivery Date *</label>
                            <input type="text" name="delivery_date" class="js-datepicker" value="<?= e($old['delivery_date'] ?? earliest_delivery_date()) ?>" readonly required>
                        </div>
                        <div class="form-field">
                            <label>Delivery Time Slot *</label>
                            <select name="delivery_slot" required>
                                <option value="">Select a slot</option>
                                <?php foreach ($slots as $slot): ?>
                                    <option value="<?= e($slot) ?>" <?= ($old['delivery_slot'] ?? '') === $slot ? 'selected' : '' ?>><?= e($slot) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-field form-field--full"><label>Special Instructions</label><textarea name="instructions" rows="3" placeholder="Add a message card or delivery note"><?= $v('instructions') ?></textarea></div>
                    </div>
                    <p class="cutoff-note" <?= same_day_available() ? 'style="display:none"' : '' ?>>
                        <i class="fa-solid fa-clock"></i> Same-day delivery has closed for today (after 5 PM IST). Earliest delivery is tomorrow.
                    </p>
                </div>
            </div>

            <aside>
                <div class="cart-summary">
                    <h3>Your Order</h3>
                    <?php foreach ($items as $item): ?>
                        <div class="summary-row">
                            <span><?= e($item['name']) ?> &times; <?= (int) $item['qty'] ?></span>
                            <span><?= e(price($item['price'] * $item['qty'])) ?></span>
                        </div>
                    <?php endforeach; ?>
                    <div class="summary-row"><span>Subtotal</span><span><?= e(price($subtotal)) ?></span></div>
                    <div class="summary-row"><span>Delivery</span><span><?= $deliveryFee > 0 ? e(price($deliveryFee)) : 'Free' ?></span></div>
                    <div class="summary-row summary-row--total"><span>Total</span><span><?= e(price($total)) ?></span></div>
                    <button type="submit" class="btn btn--primary btn--block btn--lg" style="margin-top:16px;">
                        <i class="fa-solid fa-lock"></i> Pay Securely
                    </button>
                    <p style="font-size:.8rem;color:var(--muted);text-align:center;margin-top:10px;">
                        <i class="fa-solid fa-shield-halved"></i> Payments processed securely via CC Avenue
                    </p>
                </div>
            </aside>
        </div>
    </form>
</main>
<?php require __DIR__ . '/../core/footer.php'; ?>
