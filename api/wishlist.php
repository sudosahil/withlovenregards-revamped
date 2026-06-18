<?php
/**
 * Wishlist AJAX endpoint. Actions: toggle | count | get.
 */
require_once __DIR__ . '/../includes/functions.php';

$action = $_REQUEST['action'] ?? '';

if ($action === 'toggle') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_response(null, ['message' => 'Method not allowed', 'code' => 'METHOD'], 405);
    }
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        json_response(null, ['message' => 'Invalid session token', 'code' => 'CSRF'], 419);
    }
    $id = (int) ($_POST['product_id'] ?? 0);
    $added = wishlist_toggle($id);
    json_response(['added' => $added, 'count' => wishlist_count()]);
}

if ($action === 'count') {
    json_response(['count' => wishlist_count()]);
}

if ($action === 'get') {
    json_response(['items' => wishlist_items(), 'count' => wishlist_count()]);
}

json_response(null, ['message' => 'Unknown action', 'code' => 'BAD_ACTION'], 400);
