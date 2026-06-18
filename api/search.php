<?php
/**
 * Search AJAX endpoint. GET ?q= returns up to 10 matching products.
 */
require_once __DIR__ . '/../includes/functions.php';

$term = trim((string) ($_GET['q'] ?? ''));
if ($term === '') {
    json_response(['results' => [], 'count' => 0]);
}

$matches = search_products($term);
$results = array_map(function ($p) {
    return [
        'id'    => $p['id'],
        'name'  => $p['name'],
        'price' => number_format(effective_price($p), 0, '.', ','),
        'image' => $p['image'],
        'url'   => product_url($p),
    ];
}, array_slice($matches, 0, 10));

json_response(['results' => $results, 'count' => count($matches)]);
