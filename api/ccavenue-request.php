<?php
/**
 * CC Avenue payment initiation.
 *
 * Structured to match the official CC Avenue PHP integration kit:
 *  1. Build the request parameter string from the order.
 *  2. AES-128-CBC encrypt it with the WORKING_KEY.
 *  3. POST encRequest + access_code to the CC Avenue transaction URL.
 *
 * Keys are intentionally empty until live. When MERCHANT_ID / ACCESS_CODE /
 * WORKING_KEY are filled in settings, this auto-submits to CC Avenue.
 */
require_once __DIR__ . '/../includes/functions.php';

// --- Credentials (empty until live; overridable via admin settings) ------
$settings = site_settings();
$MERCHANT_ID = $settings['ccavenue']['merchant_id'] ?? '';
$ACCESS_CODE = $settings['ccavenue']['access_code'] ?? '';
$WORKING_KEY = $settings['ccavenue']['working_key'] ?? '';
$CCA_URL = 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction';

/* ---- CC Avenue AES helpers (from the official kit) ---------------------- */
function ccav_encrypt(string $plain, string $key): string
{
    if ($key === '') {
        return ''; // no key yet — caller renders a notice instead of submitting
    }
    $secretKey = hex2bin(md5($key));
    $iv = "\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f";
    $cipher = openssl_encrypt($plain, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $iv);
    return bin2hex($cipher);
}

// --- Pull the pending order prepared by api/order.php --------------------
$order = $_SESSION['pending_order'] ?? null;
if (!$order) {
    header('Location: ' . url('checkout'));
    exit;
}

// --- Build the CC Avenue request string ----------------------------------
$merchantData =
    'merchant_id=' . $MERCHANT_ID .
    '&order_id=' . $order['order_number'] .
    '&currency=' . CURRENCY_CODE .
    '&amount=' . number_format($order['total'], 2, '.', '') .
    '&redirect_url=' . urlencode(BASE_URL . '/api/ccavenue-response.php') .
    '&cancel_url=' . urlencode(BASE_URL . '/api/ccavenue-response.php') .
    '&language=EN' .
    '&billing_name=' . urlencode($order['customer']['name']) .
    '&billing_address=' . urlencode($order['customer']['address']) .
    '&billing_city=' . urlencode($order['customer']['city']) .
    '&billing_state=' . urlencode($order['customer']['state']) .
    '&billing_zip=' . urlencode($order['customer']['pincode']) .
    '&billing_country=India' .
    '&billing_tel=' . urlencode($order['customer']['phone']) .
    '&billing_email=' . urlencode($order['customer']['email']) .
    '&merchant_param1=' . urlencode($order['order_number']); // internal order id mapping

$encRequest = ccav_encrypt($merchantData, $WORKING_KEY);
$keysReady = ($MERCHANT_ID !== '' && $ACCESS_CODE !== '' && $WORKING_KEY !== '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Redirecting to secure payment…</title>
<meta name="robots" content="noindex">
</head>
<body onload="<?= $keysReady ? 'document.ccaForm.submit();' : '' ?>" style="font-family:sans-serif;text-align:center;padding:60px 20px;">
<?php if ($keysReady): ?>
    <p>Redirecting you to CC Avenue's secure payment page…</p>
    <form name="ccaForm" method="post" action="<?= e($CCA_URL) ?>">
        <input type="hidden" name="encRequest" value="<?= e($encRequest) ?>">
        <input type="hidden" name="access_code" value="<?= e($ACCESS_CODE) ?>">
        <noscript><button type="submit">Continue to payment</button></noscript>
    </form>
<?php else: ?>
    <h2>Payment gateway not configured</h2>
    <p>CC Avenue credentials have not been added yet. Add the Merchant ID, Access Code
       and Working Key in <strong>Admin &rarr; Settings</strong> to enable live payments.</p>
    <p>Order <strong><?= e($order['order_number']) ?></strong> for
       <strong><?= e(price($order['total'])) ?></strong> has been recorded as pending.</p>
    <p><a href="<?= e(url('order-confirmation?order=' . urlencode($order['order_number']))) ?>">View order summary &rarr;</a></p>
<?php endif; ?>
</body>
</html>
