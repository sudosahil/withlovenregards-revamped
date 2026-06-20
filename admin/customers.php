<?php
/**
 * CRM — customer list with search and filter.
 */
require_once __DIR__ . '/../core/functions.php';
require_admin();

$customers = get_customers();
$q = trim((string) ($_GET['q'] ?? ''));
$fCity = $_GET['city'] ?? '';

if ($q !== '' || $fCity !== '') {
    $customers = array_filter($customers, function ($c) use ($q, $fCity) {
        if ($fCity && strcasecmp($c['city'], $fCity) !== 0) return false;
        if ($q) {
            $hay = strtolower($c['name'] . ' ' . $c['email'] . ' ' . $c['phone']);
            if (!str_contains($hay, strtolower($q))) return false;
        }
        return true;
    });
}
$cities = array_values(array_unique(array_map(fn($c) => $c['city'], get_customers())));

$adminTitle = 'Customers';
$adminActive = 'customers';
require __DIR__ . '/partials/admin-header.php';
?>
<div class="panel">
    <div class="panel__body">
        <form class="filters-bar" method="get">
            <div class="afield"><label>Search</label><input type="text" name="q" value="<?= e($q) ?>" placeholder="Name, email or phone"></div>
            <div class="afield"><label>City</label>
                <select name="city"><option value="">All</option>
                    <?php foreach ($cities as $c): ?><option value="<?= e($c) ?>" <?= $fCity === $c ? 'selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?>
                </select>
            </div>
            <button class="abtn abtn--primary" type="submit"><i class="fa-solid fa-filter"></i> Filter</button>
            <a class="abtn abtn--ghost" href="<?= e(url('admin/customers')) ?>">Reset</a>
        </form>
    </div>
</div>

<div class="panel">
    <div class="panel__head"><h2><?= count($customers) ?> Customers</h2></div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>City</th><th>Orders</th><th>Total Spent</th><th>Last Order</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($customers as $c): ?>
                    <tr>
                        <td><strong><?= e($c['name']) ?></strong></td>
                        <td><?= e($c['email']) ?></td>
                        <td><?= e($c['phone']) ?></td>
                        <td><?= e($c['city']) ?></td>
                        <td><?= (int) $c['total_orders'] ?></td>
                        <td><?= e(price($c['total_spent'])) ?></td>
                        <td><?= e(date('d M Y', strtotime($c['last_order_date']))) ?></td>
                        <td><a class="abtn abtn--ghost abtn--sm" href="<?= e(url('admin/customer-detail?id=' . $c['id'])) ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/partials/admin-footer.php'; ?>
