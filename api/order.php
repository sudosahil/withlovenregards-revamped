<?php
/**
 * Order placement. Validates the checkout form, recomputes the total from
 * server-side prices (the client total is NEVER trusted), records a pending
 * order in the session and hands off to CC Avenue.
 */
require_once __DIR__ . '/../core/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('checkout'));
    exit;
}
if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    $_SESSION['checkout_error'] = 'Your session expired. Please review and submit again.';
    header('Location: ' . url('checkout'));
    exit;
}

$cart = cart_items();
if (!$cart) {
    header('Location: ' . url('cart'));
    exit;
}

// --- Validate customer input at the boundary -----------------------------
$customer = [
    'first_name' => trim((string) ($_POST['first_name'] ?? '')),
    'last_name'  => trim((string) ($_POST['last_name'] ?? '')),
    'email'      => filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL) ?: '',
    'phone'      => preg_replace('/[^\d+]/', '', (string) ($_POST['phone'] ?? '')),
    'address'    => trim((string) ($_POST['address'] ?? '')),
    'city'       => trim((string) ($_POST['city'] ?? '')),
    'state'      => trim((string) ($_POST['state'] ?? '')),
    'pincode'    => preg_replace('/\D/', '', (string) ($_POST['pincode'] ?? '')),
];
$deliveryDate = (string) ($_POST['delivery_date'] ?? '');
$deliverySlot = (string) ($_POST['delivery_slot'] ?? '');
$instructions = trim((string) ($_POST['instructions'] ?? ''));

$errors = [];
if ($customer['first_name'] === '') $errors[] = 'First name is required.';
if ($customer['email'] === '')      $errors[] = 'A valid email is required.';
if (strlen($customer['phone']) < 10) $errors[] = 'A valid phone number is required.';
if ($customer['address'] === '')    $errors[] = 'Delivery address is required.';
if ($customer['city'] === '')       $errors[] = 'City is required.';
if (strlen($customer['pincode']) !== 6) $errors[] = 'A valid 6-digit pincode is required.';
if ($deliveryDate === '')           $errors[] = 'Please choose a delivery date.';
if ($deliverySlot === '')           $errors[] = 'Please choose a delivery time slot.';

// Enforce the 5 PM IST same-day cutoff server-side.
if ($deliveryDate !== '' && $deliveryDate < earliest_delivery_date()) {
    $errors[] = 'The selected delivery date is no longer available. Same-day orders close at 5 PM IST.';
}

if ($errors) {
    $_SESSION['checkout_error'] = implode(' ', $errors);
    $_SESSION['checkout_old'] = $_POST;
    header('Location: ' . url('checkout'));
    exit;
}

// --- Recalculate the total from trusted server-side prices ---------------
$submitted = array_map(fn($i) => ['product_id' => $i['id'], 'qty' => $i['qty'], 'price' => $i['price']], array_values($cart));
$calc = recalculate_cart($submitted);

if ($calc['tampered']) {
    // Prices were altered client-side — reject and rebuild the cart from source.
    $_SESSION['checkout_error'] = 'We detected a price mismatch and refreshed your cart. Please review the total and try again.';
    header('Location: ' . url('cart'));
    exit;
}

// --- Build the pending order --------------------------------------------
$orderNumber = 'WLNR-' . date('Y') . '-' . str_pad((string) random_int(1000, 99999), 5, '0', STR_PAD_LEFT);
$_SESSION['pending_order'] = [
    'order_number'  => $orderNumber,
    'total'         => $calc['total'],
    'items'         => $calc['items'],
    'delivery_date' => $deliveryDate,
    'delivery_slot' => $deliverySlot,
    'instructions'  => $instructions,
    'customer'      => [
        'name'    => trim($customer['first_name'] . ' ' . $customer['last_name']),
        'email'   => $customer['email'],
        'phone'   => $customer['phone'],
        'address' => $customer['address'],
        'city'    => $customer['city'],
        'state'   => $customer['state'],
        'pincode' => $customer['pincode'],
    ],
    'created_at'    => date('Y-m-d H:i:s'),
];

// Live equivalent: INSERT INTO orders (...) plus order_items rows in a transaction.

// Hand off to CC Avenue.
header('Location: ' . url('api/ccavenue-request.php'));
exit;
