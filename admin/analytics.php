<?php
/**
 * Deep analytics — revenue trend, status split, top products and a KPI summary.
 */
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$a = get_analytics();
$totalRevenue = array_sum(array_map(fn($r) => $r['revenue'], $a['revenue_by_day']));
$avgDaily = $totalRevenue / max(1, count($a['revenue_by_day']));

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

$adminTitle = 'Analytics';
$adminActive = 'analytics';
require __DIR__ . '/includes/admin-header.php';
?>
<div class="stat-grid">
    <div class="stat-card"><div class="stat-card__icon stat-card__icon--green"><i class="fa-solid fa-indian-rupee-sign"></i></div><div><div class="stat-card__label">30-Day Revenue</div><div class="stat-card__value"><?= e(price($totalRevenue)) ?></div></div></div>
    <div class="stat-card"><div class="stat-card__icon stat-card__icon--blue"><i class="fa-solid fa-chart-line"></i></div><div><div class="stat-card__label">Avg Daily Revenue</div><div class="stat-card__value"><?= e(price($avgDaily)) ?></div></div></div>
    <div class="stat-card"><div class="stat-card__icon stat-card__icon--orange"><i class="fa-solid fa-receipt"></i></div><div><div class="stat-card__label">This Month Orders</div><div class="stat-card__value"><?= (int) $a['this_month_orders'] ?></div></div></div>
    <div class="stat-card"><div class="stat-card__icon stat-card__icon--red"><i class="fa-solid fa-users"></i></div><div><div class="stat-card__label">Total Customers</div><div class="stat-card__value"><?= (int) $a['total_customers'] ?></div></div></div>
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
        <div class="panel__head"><h2>Top Products by Units</h2></div>
        <div class="panel__body"><div class="chart-box"><canvas id="topProductsChart"></canvas></div></div>
    </div>
</div>

<div class="panel">
    <div class="panel__head"><h2>Top Products by Revenue</h2></div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Product</th><th>Units Sold</th><th>Revenue</th></tr></thead>
            <tbody>
                <?php foreach ($a['top_products'] as $t): ?>
                    <tr><td><strong><?= e($t['name']) ?></strong></td><td><?= (int) $t['units_sold'] ?></td><td><?= e(price($t['revenue'])) ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/includes/admin-footer.php'; ?>
