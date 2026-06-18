<?php
/**
 * Cart AJAX endpoint. Actions: add | update | remove | count | get.
 * Response envelope: { data, error }.
 */
require_once __DIR__ . '/../includes/functions.php';

$action = $_REQUEST['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Mutating actions require POST + CSRF.
$mutating = ['add', 'update', 'remove', 'clear'];
if (in_array($action, $mutating, true)) {
    if ($method !== 'POST') {
        json_response(null, ['message' => 'Method not allowed', 'code' => 'METHOD'], 405);
    }
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        json_response(null, ['message' => 'Invalid session token', 'code' => 'CSRF'], 419);
    }
}

switch ($action) {
    case 'add':
        $id = (int) ($_POST['product_id'] ?? 0);
        $qty = max(1, (int) ($_POST['qty'] ?? 1));
        if (!cart_add($id, $qty)) {
            json_response(null, ['message' => 'Product not found', 'code' => 'NOT_FOUND'], 404);
        }
        json_response(['count' => cart_count(), 'subtotal' => cart_subtotal()]);
        break;

    case 'update':
        $id = (int) ($_POST['product_id'] ?? 0);
        $qty = (int) ($_POST['qty'] ?? 1);
        cart_update($id, $qty);
        json_response(['count' => cart_count(), 'subtotal' => cart_subtotal()]);
        break;

    case 'remove':
        $id = (int) ($_POST['product_id'] ?? 0);
        cart_remove($id);
        json_response(['count' => cart_count(), 'subtotal' => cart_subtotal()]);
        break;

    case 'clear':
        cart_clear();
        json_response(['count' => 0, 'subtotal' => 0]);
        break;

    case 'count':
        json_response(['count' => cart_count()]);
        break;

    case 'get':
        json_response(['items' => array_values(cart_items()), 'count' => cart_count(), 'subtotal' => cart_subtotal()]);
        break;

    default:
        json_response(null, ['message' => 'Unknown action', 'code' => 'BAD_ACTION'], 400);
}
