<?php
/**
 * Single order view: customer, items, delivery, status update, internal note,
 * resend invoice, CC Avenue tracking id.
 */
require_once __DIR__ . '/../core/functions.php';
require_admin();

$id = (int) ($_GET['id'] ?? 0);
$order = get_order_by_id($id);
if (!$order) {
    $adminTitle = 'Order Not Found';
    $adminActive = 'orders';
    require __DIR__ . '/partials/admin-header.php';
    echo '<div class="panel"><div class="panel__body">Order not found. <a href="' . e(url('admin/orders')) . '">Back to orders</a></div></div>';
    require __DIR__ . '/partials/admin-footer.php';
    exit;
}

$flash = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify($_POST['csrf_token'] ?? null)) {
    // Placeholder persistence — live: UPDATE orders SET status=?, note=? WHERE id=?
    $flash = 'Order updated successfully (placeholder — wire to DB when live).';
}

$cust = get_customer_by_id((int) $order['customer_id']);
$statuses = ['pending', 'processing', 'dispatched', 'delivered', 'cancelled'];

$adminTitle = 'Order ' . $order['order_number'];
$adminActive = 'orders';
require __DIR__ . '/partials/admin-header.php';
?>
<p><a class="abtn abtn--ghost abtn--sm" href="<?= e(url('admin/orders')) ?>"><i class="fa-solid fa-arrow-left"></i> Back to Orders</a></p>
<?php if ($flash): ?><div class="alert-inline alert-inline--success"><?= e($flash) ?></div><?php endif; ?>

<div class="panel-grid panel-grid--thirds">
    <div>
        <div class="panel">
            <div class="panel__head"><h2>Items</h2></div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
                    <tbody>
                        <?php foreach ($order['items'] as $item):
                            $p = get_product_by_id((int) $item['product_id']); ?>
                            <tr>
                                <td><?= e($p['name'] ?? ('Product #' . $item['product_id'])) ?></td>
                                <td><?= (int) $item['qty'] ?></td>
                                <td><?= e(price($item['price'])) ?></td>
                                <td><?= e(price($item['price'] * $item['qty'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr><td colspan="3" style="text-align:right;font-weight:700;">Order Total</td><td style="font-weight:700;"><?= e(price($order['total'])) ?></td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel">
            <div class="panel__head"><h2>Delivery</h2></div>
            <div class="panel__body">
                <p><strong>Address:</strong> <?= e($order['address']) ?>, <?= e($order['city']) ?></p>
                <p><strong>Date:</strong> <?= e(date('d M Y', strtotime($order['delivery_date']))) ?></p>
                <p><strong>Slot:</strong> <?= e($order['delivery_slot']) ?></p>
            </div>
        </div>
    </div>

    <div>
        <div class="panel">
            <div class="panel__head"><h2>Summary</h2></div>
            <div class="panel__body">
                <p><strong>Status:</strong> <span class="badge <?= e(status_class($order['status'])) ?>"><?= e(ucfirst($order['status'])) ?></span></p>
                <p><strong>Payment:</strong> <span class="badge badge--<?= e($order['payment_status']) ?>"><?= e(ucfirst($order['payment_status'])) ?></span> · <?= e($order['payment_method']) ?></p>
                <p><strong>CC Avenue Tracking ID:</strong><br><?= $order['ccavenue_tracking_id'] !== '' ? e($order['ccavenue_tracking_id']) : '<em>Not captured</em>' ?></p>
                <p><strong>Placed:</strong> <?= e(date('d M Y, H:i', strtotime($order['created_at']))) ?></p>
            </div>
        </div>

        <div class="panel">
            <div class="panel__head"><h2>Customer</h2></div>
            <div class="panel__body">
                <p><strong><?= e($cust['name'] ?? '—') ?></strong></p>
                <p><i class="fa-solid fa-envelope"></i> <?= e($cust['email'] ?? '—') ?></p>
                <p><i class="fa-solid fa-phone"></i> <?= e($cust['phone'] ?? '—') ?></p>
                <?php if ($cust): ?><a class="abtn abtn--ghost abtn--sm" href="<?= e(url('admin/customer-detail?id=' . $cust['id'])) ?>">View profile</a><?php endif; ?>
            </div>
        </div>

        <div class="panel">
            <div class="panel__head"><h2>Update Order</h2></div>
            <div class="panel__body">
                <form method="post">
                    <?= csrf_field() ?>
                    <div class="afield"><label>Status</label>
                        <select name="status">
                            <?php foreach ($statuses as $s): ?><option value="<?= e($s) ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="afield"><label>Internal Note</label><textarea name="note" rows="3" placeholder="Add a note for your team"></textarea></div>
                    <button class="abtn abtn--primary" type="submit">Save Changes</button>
                    <button class="abtn abtn--ghost" type="submit" name="resend" value="1" formnovalidate><i class="fa-solid fa-paper-plane"></i> Resend Invoice</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/partials/admin-footer.php'; ?>
