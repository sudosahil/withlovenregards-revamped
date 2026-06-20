<?php
/**
 * Admin order list with status / city / search filters and bulk status update.
 */
require_once __DIR__ . '/../core/functions.php';
require_admin();

$orders = get_orders();

// Filters
$fStatus = $_GET['status'] ?? '';
$fCity = $_GET['city'] ?? '';
$fSearch = trim((string) ($_GET['q'] ?? ''));
$fFrom = $_GET['from'] ?? '';
$fTo = $_GET['to'] ?? '';

$orders = array_filter($orders, function ($o) use ($fStatus, $fCity, $fSearch, $fFrom, $fTo) {
    if ($fStatus && $o['status'] !== $fStatus) return false;
    if ($fCity && strcasecmp($o['city'], $fCity) !== 0) return false;
    if ($fFrom && substr($o['created_at'], 0, 10) < $fFrom) return false;
    if ($fTo && substr($o['created_at'], 0, 10) > $fTo) return false;
    if ($fSearch) {
        $cust = get_customer_by_id((int) $o['customer_id']);
        $hay = strtolower($o['order_number'] . ' ' . ($cust['name'] ?? ''));
        if (!str_contains($hay, strtolower($fSearch))) return false;
    }
    return true;
});
usort($orders, fn($x, $y) => strcmp($y['created_at'], $x['created_at']));

$cities = array_values(array_unique(array_map(fn($o) => $o['city'], get_orders())));
$statuses = ['pending', 'processing', 'dispatched', 'delivered', 'cancelled'];

$adminTitle = 'Orders';
$adminActive = 'orders';
require __DIR__ . '/partials/admin-header.php';
?>
<div class="panel">
    <div class="panel__body">
        <form class="filters-bar" method="get">
            <div class="afield"><label>Search</label><input type="text" name="q" value="<?= e($fSearch) ?>" placeholder="Order # or customer"></div>
            <div class="afield"><label>Status</label>
                <select name="status">
                    <option value="">All</option>
                    <?php foreach ($statuses as $s): ?><option value="<?= e($s) ?>" <?= $fStatus === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="afield"><label>City</label>
                <select name="city">
                    <option value="">All</option>
                    <?php foreach ($cities as $c): ?><option value="<?= e($c) ?>" <?= $fCity === $c ? 'selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="afield"><label>From</label><input type="date" name="from" value="<?= e($fFrom) ?>"></div>
            <div class="afield"><label>To</label><input type="date" name="to" value="<?= e($fTo) ?>"></div>
            <button class="abtn abtn--primary" type="submit"><i class="fa-solid fa-filter"></i> Filter</button>
            <a class="abtn abtn--ghost" href="<?= e(url('admin/orders')) ?>">Reset</a>
        </form>
    </div>
</div>

<div class="panel">
    <div class="panel__head">
        <h2><?= count($orders) ?> Orders</h2>
        <div style="display:flex;gap:8px;align-items:center;">
            <select id="bulkStatus" class="afield" style="margin:0;">
                <option value="">Bulk: set status…</option>
                <?php foreach ($statuses as $s): ?><option value="<?= e($s) ?>"><?= e(ucfirst($s)) ?></option><?php endforeach; ?>
            </select>
            <button class="abtn abtn--ghost abtn--sm" onclick="alert('Bulk status update is wired to the order API when the DB is live.');return false;">Apply</button>
            <a class="abtn abtn--ghost abtn--sm" href="#" onclick="alert('CSV export is generated server-side when the DB is live.');return false;"><i class="fa-solid fa-file-csv"></i> Export</a>
        </div>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr>
                <th><input type="checkbox" id="bulkSelectAll"></th>
                <th>Order</th><th>Customer</th><th>City</th><th>Amount</th><th>Payment</th><th>Status</th><th>Delivery</th><th></th>
            </tr></thead>
            <tbody>
                <?php foreach ($orders as $o):
                    $cust = get_customer_by_id((int) $o['customer_id']); ?>
                    <tr>
                        <td><input type="checkbox" class="row-check" value="<?= (int) $o['id'] ?>"></td>
                        <td><strong><?= e($o['order_number']) ?></strong></td>
                        <td><?= e($cust['name'] ?? '—') ?></td>
                        <td><?= e($o['city']) ?></td>
                        <td><?= e(price($o['total'])) ?></td>
                        <td><span class="badge badge--<?= e($o['payment_status']) ?>"><?= e(ucfirst($o['payment_status'])) ?></span></td>
                        <td><span class="badge <?= e(status_class($o['status'])) ?>"><?= e(ucfirst($o['status'])) ?></span></td>
                        <td><?= e(date('d M', strtotime($o['delivery_date']))) ?> · <?= e($o['delivery_slot']) ?></td>
                        <td><a class="abtn abtn--ghost abtn--sm" href="<?= e(url('admin/order-detail?id=' . $o['id'])) ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$orders): ?><tr><td colspan="9" style="text-align:center;color:#8a8a9a;">No orders match these filters.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/partials/admin-footer.php'; ?>
