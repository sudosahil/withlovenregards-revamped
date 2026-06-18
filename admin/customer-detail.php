<?php
/**
 * CRM — single customer profile: info, order history, lifetime metrics,
 * internal notes and tags.
 */
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$id = (int) ($_GET['id'] ?? 0);
$cust = get_customer_by_id($id);
if (!$cust) {
    $adminTitle = 'Customer Not Found';
    $adminActive = 'customers';
    require __DIR__ . '/includes/admin-header.php';
    echo '<div class="panel"><div class="panel__body">Customer not found. <a href="' . e(url('admin/customers')) . '">Back</a></div></div>';
    require __DIR__ . '/includes/admin-footer.php';
    exit;
}

$flash = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify($_POST['csrf_token'] ?? null)) {
    $flash = 'Notes saved (placeholder — wire to DB when live).';
}

$orders = get_orders_by_customer($id);
usort($orders, fn($x, $y) => strcmp($y['created_at'], $x['created_at']));
$aov = $cust['total_orders'] > 0 ? $cust['total_spent'] / $cust['total_orders'] : 0;

// Derive a tag from behaviour.
$tags = [];
if ($cust['total_spent'] >= 10000) $tags[] = ['VIP', 'vip'];
if ($cust['total_orders'] === 1) $tags[] = ['New', 'new'];
if ($cust['total_orders'] >= 3) $tags[] = ['Repeat', 'repeat'];
if (strtotime($cust['last_order_date']) < strtotime('-60 days')) $tags[] = ['At Risk', 'at-risk'];

$adminTitle = $cust['name'];
$adminActive = 'customers';
require __DIR__ . '/includes/admin-header.php';
?>
<p><a class="abtn abtn--ghost abtn--sm" href="<?= e(url('admin/customers')) ?>"><i class="fa-solid fa-arrow-left"></i> Back to Customers</a></p>
<?php if ($flash): ?><div class="alert-inline alert-inline--success"><?= e($flash) ?></div><?php endif; ?>

<div class="stat-grid">
    <div class="stat-card"><div class="stat-card__icon stat-card__icon--blue"><i class="fa-solid fa-receipt"></i></div><div><div class="stat-card__label">Total Orders</div><div class="stat-card__value"><?= (int) $cust['total_orders'] ?></div></div></div>
    <div class="stat-card"><div class="stat-card__icon stat-card__icon--green"><i class="fa-solid fa-indian-rupee-sign"></i></div><div><div class="stat-card__label">Total Spent</div><div class="stat-card__value"><?= e(price($cust['total_spent'])) ?></div></div></div>
    <div class="stat-card"><div class="stat-card__icon stat-card__icon--orange"><i class="fa-solid fa-chart-simple"></i></div><div><div class="stat-card__label">Avg Order Value</div><div class="stat-card__value"><?= e(price($aov)) ?></div></div></div>
    <div class="stat-card"><div class="stat-card__icon stat-card__icon--red"><i class="fa-solid fa-clock"></i></div><div><div class="stat-card__label">Last Order</div><div class="stat-card__value" style="font-size:1.1rem;"><?= e(date('d M Y', strtotime($cust['last_order_date']))) ?></div></div></div>
</div>

<div class="panel-grid panel-grid--thirds">
    <div class="panel">
        <div class="panel__head"><h2>Order History</h2></div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>Order</th><th>Amount</th><th>Status</th><th>Date</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                        <tr>
                            <td><?= e($o['order_number']) ?></td>
                            <td><?= e(price($o['total'])) ?></td>
                            <td><span class="badge <?= e(status_class($o['status'])) ?>"><?= e(ucfirst($o['status'])) ?></span></td>
                            <td><?= e(date('d M Y', strtotime($o['created_at']))) ?></td>
                            <td><a class="abtn abtn--ghost abtn--sm" href="<?= e(url('admin/order-detail?id=' . $o['id'])) ?>">View</a></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$orders): ?><tr><td colspan="5" style="color:#8a8a9a;">No orders.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <div class="panel">
            <div class="panel__head"><h2>Profile</h2></div>
            <div class="panel__body">
                <p><strong><?= e($cust['name']) ?></strong></p>
                <p><i class="fa-solid fa-envelope"></i> <?= e($cust['email']) ?></p>
                <p><i class="fa-solid fa-phone"></i> <?= e($cust['phone']) ?></p>
                <p><i class="fa-solid fa-location-dot"></i> <?= e($cust['city']) ?></p>
                <p><i class="fa-solid fa-calendar"></i> Joined <?= e(date('d M Y', strtotime($cust['created_at']))) ?></p>
                <div style="margin-top:10px;display:flex;gap:6px;flex-wrap:wrap;">
                    <?php foreach ($tags as [$label, $cls]): ?><span class="badge badge--<?= e($cls) ?>"><?= e($label) ?></span><?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel__head"><h2>Internal Notes</h2></div>
            <div class="panel__body">
                <form method="post">
                    <?= csrf_field() ?>
                    <div class="afield"><textarea name="notes" rows="4"><?= e($cust['notes']) ?></textarea></div>
                    <button class="abtn abtn--primary" type="submit">Save Notes</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/includes/admin-footer.php'; ?>
