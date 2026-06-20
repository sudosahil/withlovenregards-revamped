<?php
/**
 * Admin product list — thumbnail, name, category, price, stock, status toggle,
 * inline price edit, add-new button.
 */
require_once __DIR__ . '/../core/functions.php';
require_admin();

$products = get_products();
$adminTitle = 'Products';
$adminActive = 'products';
require __DIR__ . '/partials/admin-header.php';
?>
<div class="panel">
    <div class="panel__head">
        <h2><?= count($products) ?> Products</h2>
        <a class="abtn abtn--primary" href="<?= e(url('admin/product-edit')) ?>"><i class="fa-solid fa-plus"></i> Add New Product</a>
    </div>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Image</th><th>Name</th><th>Category</th><th>Price (editable)</th><th>Stock</th><th>Status</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($products as $p):
                    $cat = get_category_by_id((int) $p['category_id']); ?>
                    <tr>
                        <td><img class="thumb" src="<?= e(media(ltrim($p['image'], '/'))) ?>" alt="<?= e($p['name']) ?>" loading="lazy"></td>
                        <td><strong><?= e($p['name']) ?></strong><br><small style="color:#8a8a9a;">WLNR-<?= (int) $p['id'] ?></small></td>
                        <td><?= e($cat['name'] ?? '—') ?></td>
                        <td><span class="inline-price" contenteditable="true" data-product="<?= (int) $p['id'] ?>"><?= e(price(effective_price($p))) ?></span></td>
                        <td><?= (int) $p['stock'] ?></td>
                        <td>
                            <label class="toggle">
                                <input type="checkbox" <?= $p['stock'] > 0 ? 'checked' : '' ?> aria-label="Enable product">
                                <span class="track"></span>
                            </label>
                        </td>
                        <td><a class="abtn abtn--ghost abtn--sm" href="<?= e(url('admin/product-edit?id=' . $p['id'])) ?>"><i class="fa-solid fa-pen"></i> Edit</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/partials/admin-footer.php'; ?>
