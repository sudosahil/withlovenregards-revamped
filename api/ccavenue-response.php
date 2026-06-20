<?php
/**
 * CC Avenue payment callback.
 *
 * Decrypts encResp with the WORKING_KEY, reads the order_status, matches the
 * CC Avenue tracking_id to the internal order number (merchant_param1), then on
 * success sends the invoice email via PHPMailer and clears the cart.
 */
require_once __DIR__ . '/../core/functions.php';
require_once __DIR__ . '/../config/mail.php';

$settings = site_settings();
$WORKING_KEY = $settings['ccavenue']['working_key'] ?? '';

function ccav_decrypt(string $encrypted, string $key): string
{
    if ($key === '' || $encrypted === '') {
        return '';
    }
    $secretKey = hex2bin(md5($key));
    $iv = "\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f";
    $cipher = hex2bin($encrypted);
    return (string) openssl_decrypt($cipher, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $iv);
}

$order = $_SESSION['pending_order'] ?? null;
$status = 'Pending';
$trackingId = '';
$orderNumber = $order['order_number'] ?? ($_GET['order'] ?? '');

// Parse the real CC Avenue response when present.
$encResp = $_POST['encResp'] ?? '';
if ($encResp !== '' && $WORKING_KEY !== '') {
    parse_str(ccav_decrypt($encResp, $WORKING_KEY), $resp);
    $status = $resp['order_status'] ?? 'Failure';
    $trackingId = $resp['tracking_id'] ?? '';
    $orderNumber = $resp['order_id'] ?? $orderNumber;
}

$success = strcasecmp($status, 'Success') === 0;

// In placeholder mode (no live keys) treat the order as recorded-pending so the
// flow is demonstrable end-to-end.
if ($WORKING_KEY === '') {
    $success = false;
    $status = 'Pending (gateway not configured)';
}

if ($success && $order) {
    // Live equivalent: UPDATE orders SET payment_status='paid', ccavenue_tracking_id=?, status='processing' WHERE order_number=?
    send_invoice_email($order, $trackingId);
    cart_clear();
    unset($_SESSION['pending_order']);
    $_SESSION['last_order'] = ['order_number' => $orderNumber, 'tracking_id' => $trackingId, 'paid' => true];
}

header('Location: ' . url('order-confirmation?order=' . urlencode($orderNumber) . ($success ? '&paid=1' : '')));
exit;

/**
 * Compose and send the invoice email. The invoice order id equals the CC Avenue
 * transaction id when available, otherwise the internal order number.
 */
function send_invoice_email(array $order, string $trackingId): bool
{
    $mail = mailer();
    if (!$mail) {
        error_log('Invoice not sent (PHPMailer unavailable) for ' . $order['order_number']);
        return false;
    }
    $invoiceId = $trackingId !== '' ? $trackingId : $order['order_number'];
    $rows = '';
    foreach ($order['items'] as $item) {
        $rows .= '<tr><td>' . e($item['name']) . '</td><td align="center">' . (int) $item['qty'] .
                 '</td><td align="right">' . price($item['line_total']) . '</td></tr>';
    }
    $body = '<h2>Thank you for your order!</h2>'
        . '<p>Invoice / Transaction ID: <strong>' . e($invoiceId) . '</strong></p>'
        . '<p>Order Number: ' . e($order['order_number']) . '</p>'
        . '<p>Delivery Date: ' . e($order['delivery_date']) . ' (' . e($order['delivery_slot']) . ')</p>'
        . '<table border="1" cellpadding="8" cellspacing="0" width="100%">'
        . '<tr><th align="left">Item</th><th>Qty</th><th align="right">Total</th></tr>'
        . $rows
        . '<tr><td colspan="2" align="right"><strong>Total</strong></td><td align="right"><strong>'
        . price($order['total']) . '</strong></td></tr></table>'
        . '<p>Payment Method: CC Avenue</p>';

    try {
        $mail->addAddress($order['customer']['email'], $order['customer']['name']);
        $mail->addBCC(ADMIN_EMAIL);
        $mail->Subject = 'Your ' . SITE_NAME . ' Invoice — ' . $invoiceId;
        $mail->Body = $body;
        $mail->send();
        return true;
    } catch (\Throwable $e) {
        error_log('Invoice email failed: ' . $e->getMessage());
        return false;
    }
}
