<?php
/**
 * Homepage editor — edits data/homepage_config.json which index.php reads.
 * Edits hero slides, featured products, promo tiles, SEO content and FAQs.
 */
require_once __DIR__ . '/../core/functions.php';
require_admin();

$configFile = ROOT_PATH . '/data/homepage_config.json';
$cfg = homepage_config();
$flash = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify($_POST['csrf_token'] ?? null)) {
    // Hero slides
    $slides = [];
    foreach (($_POST['slide_heading'] ?? []) as $i => $heading) {
        $slides[] = [
            'image'    => trim((string) ($_POST['slide_image'][$i] ?? '')),
            'heading'  => trim((string) $heading),
            'subtext'  => trim((string) ($_POST['slide_subtext'][$i] ?? '')),
            'cta_text' => trim((string) ($_POST['slide_cta_text'][$i] ?? '')),
            'cta_link' => trim((string) ($_POST['slide_cta_link'][$i] ?? '')),
        ];
    }
    // Promo tiles
    $tiles = [];
    foreach (($_POST['tile_heading'] ?? []) as $i => $heading) {
        $tiles[] = [
            'image'   => trim((string) ($_POST['tile_image'][$i] ?? '')),
            'heading' => trim((string) $heading),
            'link'    => trim((string) ($_POST['tile_link'][$i] ?? '')),
        ];
    }
    // Featured ids
    $featured = array_values(array_filter(array_map('intval', $_POST['featured'] ?? [])));
    // SEO
    $blocks = [];
    foreach (($_POST['block_h2'] ?? []) as $i => $h2) {
        if (trim((string) $h2) === '') continue;
        $blocks[] = ['h2' => trim((string) $h2), 'body' => trim((string) ($_POST['block_body'][$i] ?? ''))];
    }
    // FAQs handled on homepage from data layer; SEO + content here.
    $cfg['hero_slides'] = $slides ?: $cfg['hero_slides'];
    $cfg['promo_tiles'] = $tiles ?: $cfg['promo_tiles'];
    $cfg['featured_product_ids'] = $featured ?: $cfg['featured_product_ids'];
    $cfg['seo'] = [
        'h1'     => trim((string) ($_POST['seo_h1'] ?? ($cfg['seo']['h1'] ?? ''))),
        'intro'  => trim((string) ($_POST['seo_intro'] ?? ($cfg['seo']['intro'] ?? ''))),
        'blocks' => $blocks ?: ($cfg['seo']['blocks'] ?? []),
    ];

    if (file_put_contents($configFile, json_encode($cfg, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !== false) {
        $flash = 'Homepage updated. Changes are live.';
    } else {
        $flash = 'Could not write config file — check permissions on data/homepage_config.json.';
    }
}

$slides = $cfg['hero_slides'] ?? [];
$tiles = $cfg['promo_tiles'] ?? [];
$featuredIds = $cfg['featured_product_ids'] ?? [];
$seoC = $cfg['seo'] ?? [];
$allProducts = get_products();

$adminTitle = 'Homepage Editor';
$adminActive = 'homepage-editor';
require __DIR__ . '/partials/admin-header.php';
?>
<?php if ($flash): ?><div class="alert-inline alert-inline--success"><?= e($flash) ?></div><?php endif; ?>
<form method="post">
    <?= csrf_field() ?>

    <div class="panel">
        <div class="panel__head"><h2>Hero Slides</h2></div>
        <div class="panel__body">
            <?php foreach ($slides as $i => $s): ?>
                <div class="card-box" style="background:#fafbff;">
                    <strong>Slide <?= $i + 1 ?></strong>
                    <div class="afield-row">
                        <div class="afield"><label>Image path</label><input type="text" name="slide_image[]" value="<?= e($s['image']) ?>"></div>
                        <div class="afield"><label>CTA link</label><input type="text" name="slide_cta_link[]" value="<?= e($s['cta_link']) ?>"></div>
                    </div>
                    <div class="afield"><label>Heading</label><input type="text" name="slide_heading[]" value="<?= e($s['heading']) ?>"></div>
                    <div class="afield"><label>Subtext</label><input type="text" name="slide_subtext[]" value="<?= e($s['subtext']) ?>"></div>
                    <div class="afield"><label>CTA text</label><input type="text" name="slide_cta_text[]" value="<?= e($s['cta_text']) ?>"></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="panel">
        <div class="panel__head"><h2>Featured Products (Best Sellers grid)</h2></div>
        <div class="panel__body">
            <p style="color:#8a8a9a;font-size:.85rem;">Tick the products to show in the homepage Best Sellers grid.</p>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:8px;">
                <?php foreach ($allProducts as $p): ?>
                    <label class="filter-check" style="border:1px solid #e7e7ef;padding:8px;border-radius:6px;">
                        <input type="checkbox" name="featured[]" value="<?= (int) $p['id'] ?>" <?= in_array((int) $p['id'], $featuredIds, true) ? 'checked' : '' ?>>
                        <?= e($p['name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel__head"><h2>Promotional Banners</h2></div>
        <div class="panel__body">
            <?php foreach ($tiles as $i => $t): ?>
                <div class="card-box" style="background:#fafbff;">
                    <strong>Tile <?= $i + 1 ?></strong>
                    <div class="afield"><label>Image path</label><input type="text" name="tile_image[]" value="<?= e($t['image']) ?>"></div>
                    <div class="afield-row">
                        <div class="afield"><label>Heading</label><input type="text" name="tile_heading[]" value="<?= e($t['heading']) ?>"></div>
                        <div class="afield"><label>Link</label><input type="text" name="tile_link[]" value="<?= e($t['link']) ?>"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="panel">
        <div class="panel__head"><h2>SEO Content</h2></div>
        <div class="panel__body">
            <div class="afield"><label>Main H1</label><input type="text" name="seo_h1" value="<?= e($seoC['h1'] ?? '') ?>"></div>
            <div class="afield"><label>Intro paragraph</label><textarea name="seo_intro" rows="3"><?= e($seoC['intro'] ?? '') ?></textarea></div>
            <?php foreach ($seoC['blocks'] ?? [] as $b): ?>
                <div class="afield-row">
                    <div class="afield"><label>Section heading</label><input type="text" name="block_h2[]" value="<?= e($b['h2']) ?>"></div>
                    <div class="afield"><label>Section body</label><input type="text" name="block_body[]" value="<?= e($b['body']) ?>"></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <button class="abtn abtn--primary" type="submit" style="justify-content:center;"><i class="fa-solid fa-floppy-disk"></i> Save Homepage</button>
</form>
<?php require __DIR__ . '/partials/admin-footer.php'; ?>
