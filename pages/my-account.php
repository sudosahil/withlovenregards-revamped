<?php
/**
 * Customer account area. Tabs: orders | wishlist | details.
 * Protected — redirects to login if not authenticated.
 */
require_once __DIR__ . '/../core/functions.php';
require_login();

$user = current_user();
$tab = $_GET['tab'] ?? 'orders';
$allowed = ['orders', 'wishlist', 'details'];
if (!in_array($tab, $allowed, true)) {
    $tab = 'orders';
}

// Orders for the logged-in customer (placeholder: match by id when available).
$orders = !empty($user['id']) ? get_orders_by_customer((int) $user['id']) : [];

// Wishlist products
$wishProducts = array_filter(array_map(fn($id) => get_product_by_id((int) $id), wishlist_items()));

$seo = [
    'title'       => 'My Account | ' . SITE_NAME,
    'description' => 'Manage your orders, wishlist and account details.',
    'canonical'   => BASE_URL . '/my-account/',
];
require __DIR__ . '/../core/header.php';

$tabUrl = fn(string $t) => url('my-account?tab=' . $t);
?>
<main class="container">
    <h1 class="section__title" style="margin:24px 0;">My Account</h1>
    <p style="color:var(--muted);margin-top:-12px;">Welcome back, <strong><?= e($user['name']) ?></strong></p>

    <div class="account-layout">
        <nav class="account-nav">
            <a href="<?= e($tabUrl('orders')) ?>" class="<?= $tab === 'orders' ? 'active' : '' ?>"><i class="fa-solid fa-box"></i> My Orders</a>
            <a href="<?= e($tabUrl('wishlist')) ?>" class="<?= $tab === 'wishlist' ? 'active' : '' ?>"><i class="fa-solid fa-heart"></i> My Wishlist</a>
            <a href="<?= e($tabUrl('details')) ?>" class="<?= $tab === 'details' ? 'active' : '' ?>"><i class="fa-solid fa-user"></i> Account Details</a>
            <a href="<?= e(url('api/auth.php?action=logout')) ?>"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </nav>

        <div>
            <?php if ($tab === 'orders'): ?>
                <div class="card-box">
                    <h3>Order History</h3>
                    <?php if (!$orders): ?>
                        <p style="color:var(--muted);">You have no orders yet. <a href="<?= e(url('flowers')) ?>" style="color:var(--primary);">Start shopping</a>.</p>
                    <?php else: ?>
                        <?php foreach ($orders as $o): ?>
                            <div class="order-row">
                                <div class="order-row__head">
                                    <strong><?= e($o['order_number']) ?></strong>
                                    <span class="badge <?= e(status_class($o['status'])) ?>"><?= e(ucfirst($o['status'])) ?></span>
                                </div>
                                <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:8px;color:var(--muted);font-size:.88rem;">
                                    <span>Placed: <?= e(date('d M Y', strtotime($o['created_at']))) ?></span>
                                    <span>Delivery: <?= e(date('d M Y', strtotime($o['delivery_date']))) ?> · <?= e($o['delivery_slot']) ?></span>
                                    <span style="font-weight:700;color:var(--text);"><?= e(price($o['total'])) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            <?php elseif ($tab === 'wishlist'): ?>
                <div class="card-box">
                    <h3>My Wishlist</h3>
                    <?php if (!$wishProducts): ?>
                        <p style="color:var(--muted);">Your wishlist is empty.</p>
                    <?php else: ?>
                        <div class="product-grid">
                            <?php foreach ($wishProducts as $product) {
                                include __DIR__ . '/../core/product-card.php';
                            } ?>
                        </div>
                    <?php endif; ?>
                </div>

            <?php else: ?>
                <div class="card-box">
                    <h3>Account Details</h3>
                    <form method="post" action="#">
                        <?= csrf_field() ?>
                        <div class="form-grid">
                            <div class="form-field"><label>Name</label><input type="text" name="name" value="<?= e($user['name']) ?>"></div>
                            <div class="form-field"><label>Email</label><input type="email" name="email" value="<?= e($user['email']) ?>"></div>
                            <div class="form-field"><label>Phone</label><input type="tel" name="phone" value="<?= e($user['phone'] ?? '') ?>"></div>
                            <div class="form-field"><label>New Password</label><input type="password" name="password" placeholder="Leave blank to keep current"></div>
                        </div>
                        <button type="submit" class="btn btn--primary" style="margin-top:16px;">Save Changes</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../core/footer.php'; ?>
