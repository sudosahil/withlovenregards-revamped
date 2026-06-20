<?php
/**
 * Abandoned cart tracker — email/phone, cart contents, value, time since,
 * recovery status with quick actions and a WhatsApp follow-up link.
 */
require_once __DIR__ . '/../core/functions.php';
require_admin();

$carts = get_abandoned_carts();
usort($carts, fn($x, $y) => strcmp($y['last_activity'], $x['last_activity']));

$adminTitle = 'Abandoned Carts';
$adminActive = 'abandoned-carts';
require __DIR__ . '/partials/admin-header.php';

$recoveryMsg = rawurlencode('Hi! You left some lovely items in your cart at ' . SITE_NAME . '. Complete your order now and we\'ll get them delivered fresh!');
?>
<div class="panel">
    <div class="panel__head"><h2><?= count($carts) ?> Abandoned Carts</h2></div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Customer</th><th>Items</th><th>Cart Value</th><th>Abandoned</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($carts as $c):
                    $names = array_map(function ($i) {
                        $p = get_product_by_id((int) $i['product_id']);
                        return ($p['name'] ?? 'Item') . ' ×' . (int) $i['qty'];
                    }, $c['cart_items']);
                    $wa = preg_replace('/\D/', '', $c['customer_phone']); ?>
                    <tr>
                        <td><strong><?= e($c['customer_email']) ?></strong><br><small style="color:#8a8a9a;"><?= e($c['customer_phone']) ?></small></td>
                        <td><small><?= e(implode(', ', $names)) ?></small></td>
                        <td><?= e(price($c['total_value'])) ?></td>
                        <td><?= e(time_ago($c['last_activity'])) ?></td>
                        <td><span class="badge <?= e(status_class($c['recovery_status'])) ?>"><?= e(ucfirst($c['recovery_status'])) ?></span></td>
                        <td style="display:flex;gap:6px;">
                            <a class="abtn abtn--ghost abtn--sm" href="https://wa.me/<?= e($wa) ?>?text=<?= $recoveryMsg ?>" target="_blank" rel="noopener" title="WhatsApp follow-up"><i class="fa-brands fa-whatsapp"></i></a>
                            <button class="abtn abtn--ghost abtn--sm" onclick="this.closest('tr').querySelector('.badge').textContent='Contacted';return false;" title="Mark contacted"><i class="fa-solid fa-envelope"></i></button>
                            <button class="abtn abtn--ghost abtn--sm" onclick="this.closest('tr').querySelector('.badge').textContent='Recovered';return false;" title="Mark recovered"><i class="fa-solid fa-check"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/partials/admin-footer.php'; ?>
