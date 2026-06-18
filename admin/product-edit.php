<?php
/**
 * Add / edit a product. Full form with auto-slug, image upload (WebP conversion
 * on the server when live), spec key-value pairs and feature toggles.
 */
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$id = (int) ($_GET['id'] ?? 0);
$product = $id ? get_product_by_id($id) : null;
$isEdit = (bool) $product;

$flash = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify($_POST['csrf_token'] ?? null)) {
    // Placeholder: live would INSERT/UPDATE and convert the uploaded image to WebP.
    $flash = ($isEdit ? 'Product updated' : 'Product created') . ' (placeholder — wire to DB when live).';
}

$val = fn(string $k, $default = '') => e((string) ($product[$k] ?? $default));
$categories = get_top_categories();

$adminTitle = $isEdit ? 'Edit Product' : 'Add Product';
$adminActive = 'products';
require __DIR__ . '/includes/admin-header.php';
?>
<p><a class="abtn abtn--ghost abtn--sm" href="<?= e(url('admin/products')) ?>"><i class="fa-solid fa-arrow-left"></i> Back to Products</a></p>
<?php if ($flash): ?><div class="alert-inline alert-inline--success"><?= e($flash) ?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="panel-grid panel-grid--thirds">
        <div>
            <div class="panel">
                <div class="panel__head"><h2>Product Details</h2></div>
                <div class="panel__body">
                    <div class="afield"><label>Name</label><input type="text" id="productName" name="name" value="<?= $val('name') ?>" required></div>
                    <div class="afield"><label>Slug</label><input type="text" id="productSlug" name="slug" value="<?= $val('slug') ?>"></div>
                    <div class="afield-row">
                        <div class="afield"><label>Category</label>
                            <select name="category_id">
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?= (int) $c['id'] ?>" <?= (int) ($product['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="afield"><label>Type</label><input type="text" name="type" value="<?= $val('type') ?>" placeholder="Bouquet, Cake, Hamper…"></div>
                    </div>
                    <div class="afield-row">
                        <div class="afield"><label>Price (Rs.)</label><input type="number" step="0.01" name="price" value="<?= $val('price') ?>" required></div>
                        <div class="afield"><label>Sale Price (Rs.)</label><input type="number" step="0.01" name="sale_price" value="<?= $val('sale_price') ?>"></div>
                    </div>
                    <div class="afield-row">
                        <div class="afield"><label>Stock</label><input type="number" name="stock" value="<?= $val('stock', '0') ?>"></div>
                        <div class="afield"><label>Weight / Size</label><input type="text" name="weight" value="<?= $val('weight') ?>"></div>
                    </div>
                    <div class="afield"><label>Short Description</label><textarea name="short_description" rows="2"><?= $val('short_description') ?></textarea></div>
                    <div class="afield"><label>Full Description</label><textarea name="description" rows="5"><?= $val('description') ?></textarea></div>
                </div>
            </div>

            <div class="panel">
                <div class="panel__head"><h2>SEO</h2></div>
                <div class="panel__body">
                    <div class="afield"><label>Meta Title</label><input type="text" name="meta_title" value="<?= $val('meta_title') ?>"></div>
                    <div class="afield"><label>Meta Description</label><textarea name="meta_description" rows="2"><?= $val('meta_description') ?></textarea></div>
                </div>
            </div>
        </div>

        <div>
            <div class="panel">
                <div class="panel__head"><h2>Image</h2></div>
                <div class="panel__body">
                    <?php if ($product): ?>
                        <img src="<?= e(media(ltrim($product['image'], '/'))) ?>" alt="" style="width:100%;border-radius:8px;margin-bottom:12px;">
                    <?php endif; ?>
                    <div class="afield"><label>Upload (converted to WebP)</label><input type="file" name="image" accept="image/*"></div>
                    <p style="font-size:.78rem;color:#8a8a9a;">Recommended: square, at least 800×800px.</p>
                </div>
            </div>

            <div class="panel">
                <div class="panel__head"><h2>Flags</h2></div>
                <div class="panel__body">
                    <div class="afield" style="display:flex;justify-content:space-between;align-items:center;">
                        <label style="margin:0;">Featured</label>
                        <label class="toggle"><input type="checkbox" name="is_featured" <?= !empty($product['is_featured']) ? 'checked' : '' ?>><span class="track"></span></label>
                    </div>
                    <div class="afield" style="display:flex;justify-content:space-between;align-items:center;">
                        <label style="margin:0;">Bestseller</label>
                        <label class="toggle"><input type="checkbox" name="is_bestseller" <?= !empty($product['is_bestseller']) ? 'checked' : '' ?>><span class="track"></span></label>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel__head"><h2>Specifications</h2></div>
                <div class="panel__body">
                    <div class="afield-row"><div class="afield"><input type="text" name="spec_key[]" placeholder="Key (e.g. Stems)"></div><div class="afield"><input type="text" name="spec_val[]" placeholder="Value (e.g. 12)"></div></div>
                    <div class="afield-row"><div class="afield"><input type="text" name="spec_key[]" placeholder="Key"></div><div class="afield"><input type="text" name="spec_val[]" placeholder="Value"></div></div>
                </div>
            </div>

            <button class="abtn abtn--primary" type="submit" style="width:100%;justify-content:center;"><?= $isEdit ? 'Update Product' : 'Create Product' ?></button>
        </div>
    </div>
</form>
<?php require __DIR__ . '/includes/admin-footer.php'; ?>
