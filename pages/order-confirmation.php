<?php
/**
 * Post-purchase confirmation. Reads the last order from the session (set by the
 * CC Avenue callback) or shows a generic recorded-pending message.
 */
require_once __DIR__ . '/../core/functions.php';

$orderNumber = preg_replace('/[^A-Za-z0-9\-]/', '', $_GET['order'] ?? '');
$paid = isset($_GET['paid']);
$last = $_SESSION['last_order'] ?? null;
$trackingId = $last['tracking_id'] ?? '';

$seo = [
    'title'       => 'Order Confirmation | ' . SITE_NAME,
    'description' => 'Your order has been received.',
    'canonical'   => BASE_URL . '/order-confirmation/',
];
require __DIR__ . '/../core/header.php';
?>
<main class="container">
    <div class="card-box" style="max-width:640px;margin:40px auto;text-align:center;">
        <?php if ($paid): ?>
            <i class="fa-solid fa-circle-check" style="font-size:3rem;color:var(--success);"></i>
            <h1 style="margin-top:14px;">Thank you! Your order is confirmed</h1>
            <p>A confirmation and invoice have been emailed to you.</p>
        <?php else: ?>
            <i class="fa-solid fa-clock" style="font-size:3rem;color:var(--warning);"></i>
            <h1 style="margin-top:14px;">Order Received</h1>
            <p>Your order has been recorded and is awaiting payment confirmation.</p>
        <?php endif; ?>

        <?php if ($orderNumber): ?>
            <p style="margin-top:16px;">Order Number: <strong><?= e($orderNumber) ?></strong></p>
        <?php endif; ?>
        <?php if ($trackingId): ?>
            <p>Transaction ID: <strong><?= e($trackingId) ?></strong></p>
        <?php endif; ?>

        <div style="margin-top:24px;display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
            <a class="btn btn--primary" href="<?= e(url('my-account?tab=orders')) ?>">View My Orders</a>
            <a class="btn btn--outline" href="<?= e(url()) ?>">Continue Shopping</a>
        </div>
    </div>
</main>
<?php
unset($_SESSION['last_order']);
require __DIR__ . '/../core/footer.php';
?>
