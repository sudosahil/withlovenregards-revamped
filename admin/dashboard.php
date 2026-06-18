<?php
/**
 * Admin dashboard — KPI cards, charts and recent activity.
 */
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$a = get_analytics();
$orders = get_orders();
$abandoned = get_abandoned_carts();

// Recent orders (last 10 by created_at desc)
usort($orders, fn($x, $y) => strcmp($y['created_at'], $x['created_at']));
$recentOrders = array_slice($orders, 0, 10);
$recentAbandoned = array_slice($abandoned, 0, 5);

// Chart payloads
$adminCharts = [
    'revenue' => [
        'labels' => array_map(fn($r) => date('d M', strtotime($r['date'])), $a['revenue_by_day']),
        'values' => array_map(fn($r) => $r['revenue'], $a['revenue_by_day']),
    ],
    'status' => [
        'labels' => array_map('ucfirst', array_keys($a['orders_by_status'])),
        'values' => array_values($a['orders_by_status']),
    ],
    'top' => [
        'labels' => array_map(fn($t) => $t['name'], $a['top_products']),
        'values' => array_map(fn($t) => $t['units_sold'], $a['top_products']),
    ],
];

$adminTitle = 'Dashboard';
$adminActive = 'dashboard';
require __DIR__ . '/includes/admin-header.php';
?>
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-card__icon stat-card__icon--blue"><i class="fa-solid fa-receipt"></i></div>
        <div><div class="stat-card__label">Today's Orders</div><div class="stat-card__value"><?= (int) $a['today_orders'] ?></div><div class="stat-card__sub"><?= e(price($a['today_revenue'])) ?> revenue</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-card__icon stat-card__icon--green"><i class="fa-solid fa-indian-rupee-sign"></i></div>
        <div><div class="stat-card__label">This Month Revenue</div><div class="stat-card__value"><?= e(price($a['this_month_revenue'])) ?></div><div class="stat-card__sub"><?= (int) $a['this_month_orders'] ?> orders</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-card__icon stat-card__icon--orange"><i class="fa-solid fa-clock"></i></div>
        <?php if ($a['pending_orders'] > 0): ?><span class="stat-card__alert"><?= (int) $a['pending_orders'] ?> new</span><?php endif; ?>
        <div><div class="stat-card__label">Pending Orders</div><div class="stat-card__value"><?= (int) $a['pending_orders'] ?></div><div class="stat-card__sub">Awaiting processing</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-card__icon stat-card__icon--red"><i class="fa-solid fa-cart-arrow-down"></i></div>
        <?php if ($a['abandoned_carts_count'] > 0): ?><span class="stat-card__alert"><?= (int) $a['abandoned_carts_count'] ?> new</span><?php endif; ?>
        <div><div class="stat-card__label">Abandoned Carts</div><div class="stat-card__value"><?= count($abandoned) ?></div><div class="stat-card__sub"><?= (int) $a['abandoned_carts_count'] ?> need follow-up</div></div>
    </div>
</div>

<div class="panel">
    <div class="panel__head"><h2>Revenue — Last 30 Days</h2></div>
    <div class="panel__body"><div class="chart-box"><canvas id="revenueChart"></canvas></div></div>
</div>

<div class="panel-grid">
    <div class="panel">
        <div class="panel__head"><h2>Orders by Status</h2></div>
        <div class="panel__body"><div class="chart-box"><canvas id="statusChart"></canvas></div></div>
    </div>
    <div class="panel">
        <div class="panel__head"><h2>Top 5 Products</h2></div>
        <div class="panel__body"><div class="chart-box"><canvas id="topProductsChart"></canvas></div></div>
    </div>
</div>

<div class="panel">
    <div class="panel__head"><h2>Recent Orders</h2><a class="abtn abtn--ghost abtn--sm" href="<?= e(url('admin/orders')) ?>">View all</a></div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Order</th><th>Customer</th><th>City</th><th>Amount</th><th>Status</th><th>Date</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($recentOrders as $o):
                    $cust = get_customer_by_id((int) $o['customer_id']); ?>
                    <tr>
                        <td><strong><?= e($o['order_number']) ?></strong></td>
                        <td><?= e($cust['name'] ?? '—') ?></td>
                        <td><?= e($o['city']) ?></td>
                        <td><?= e(price($o['total'])) ?></td>
                        <td><span class="badge <?= e(status_class($o['status'])) ?>"><?= e(ucfirst($o['status'])) ?></span></td>
                        <td><?= e(date('d M Y', strtotime($o['created_at']))) ?></td>
                        <td><a class="abtn abtn--ghost abtn--sm" href="<?= e(url('admin/order-detail?id=' . $o['id'])) ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="panel">
    <div class="panel__head"><h2>Recent Abandoned Carts</h2><a class="abtn abtn--ghost abtn--sm" href="<?= e(url('admin/abandoned-carts')) ?>">View all</a></div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Email</th><th>Cart Value</th><th>Items</th><th>Abandoned</th><th>Status</th></tr></thead>
            <tbody>
                <?php foreach ($recentAbandoned as $c): ?>
                    <tr>
                        <td><?= e($c['customer_email']) ?></td>
                        <td><?= e(price($c['total_value'])) ?></td>
                        <td><?= count($c['cart_items']) ?></td>
                        <td><?= e(time_ago($c['last_activity'])) ?></td>
                        <td><span class="badge <?= e(status_class($c['recovery_status'])) ?>"><?= e(ucfirst($c['recovery_status'])) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
