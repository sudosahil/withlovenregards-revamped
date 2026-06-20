<?php
/**
 * Category management — list top-level and sub categories, add new.
 */
require_once __DIR__ . '/../core/functions.php';
require_admin();

$top = get_top_categories();
$products = get_products();
$countByCat = [];
foreach ($products as $p) {
    $countByCat[(int) $p['category_id']] = ($countByCat[(int) $p['category_id']] ?? 0) + 1;
}

$flash = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify($_POST['csrf_token'] ?? null)) {
    $flash = 'Category saved (placeholder — wire to DB when live).';
}

$adminTitle = 'Categories';
$adminActive = 'categories';
require __DIR__ . '/partials/admin-header.php';
?>
<?php if ($flash): ?><div class="alert-inline alert-inline--success"><?= e($flash) ?></div><?php endif; ?>
<div class="panel-grid panel-grid--thirds">
    <div class="panel">
        <div class="panel__head"><h2>Categories</h2></div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>Name</th><th>Slug</th><th>Parent</th><th>Products</th><th></th></tr></thead>
                <tbody>
                    <?php foreach (get_categories() as $c):
                        $parent = $c['parent_id'] ? get_category_by_id((int) $c['parent_id']) : null; ?>
                        <tr>
                            <td><strong><?= e($c['name']) ?></strong></td>
                            <td><code><?= e($c['slug']) ?></code></td>
                            <td><?= $parent ? e($parent['name']) : '<em>Top level</em>' ?></td>
                            <td><?= (int) ($countByCat[(int) $c['id']] ?? 0) ?></td>
                            <td><a class="abtn abtn--ghost abtn--sm" href="#"><i class="fa-solid fa-pen"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel">
        <div class="panel__head"><h2>Add Category</h2></div>
        <div class="panel__body">
            <form method="post">
                <?= csrf_field() ?>
                <div class="afield"><label>Name</label><input type="text" name="name" required></div>
                <div class="afield"><label>Slug</label><input type="text" name="slug" placeholder="auto if blank"></div>
                <div class="afield"><label>Parent</label>
                    <select name="parent_id">
                        <option value="">— Top level —</option>
                        <?php foreach ($top as $c): ?><option value="<?= (int) $c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="afield"><label>Description</label><textarea name="description" rows="3"></textarea></div>
                <div class="afield"><label>Meta Title</label><input type="text" name="meta_title"></div>
                <div class="afield"><label>Meta Description</label><textarea name="meta_description" rows="2"></textarea></div>
                <button class="abtn abtn--primary" type="submit">Add Category</button>
            </form>
        </div>
    </div>
</div>
<?php require __DIR__ . '/partials/admin-footer.php'; ?>
