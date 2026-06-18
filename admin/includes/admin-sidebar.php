<?php
/**
 * Admin sidebar nav. Expects $adminActive (slug) for highlighting.
 */
$adminActive = $adminActive ?? '';
$nav = [
    'Main' => [
        ['dashboard', 'Dashboard', 'fa-gauge-high'],
        ['analytics', 'Analytics', 'fa-chart-line'],
    ],
    'Sales' => [
        ['orders', 'Orders', 'fa-receipt'],
        ['abandoned-carts', 'Abandoned Carts', 'fa-cart-arrow-down'],
    ],
    'Catalogue' => [
        ['products', 'Products', 'fa-box'],
        ['categories', 'Categories', 'fa-layer-group'],
    ],
    'CRM' => [
        ['customers', 'Customers', 'fa-users'],
    ],
    'Content' => [
        ['homepage-editor', 'Homepage Editor', 'fa-pen-to-square'],
        ['settings', 'Settings', 'fa-gear'],
    ],
];
?>
<aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-brand">With<span>Love</span>NRegards</div>
    <nav class="admin-nav">
        <?php foreach ($nav as $group => $links): ?>
            <div class="admin-nav__group"><?= e($group) ?></div>
            <?php foreach ($links as [$slug, $label, $icon]): ?>
                <a href="<?= e(url('admin/' . $slug)) ?>" class="<?= $adminActive === $slug ? 'active' : '' ?>">
                    <i class="fa-solid <?= $icon ?>"></i> <?= e($label) ?>
                </a>
            <?php endforeach; ?>
        <?php endforeach; ?>
        <div class="admin-nav__group">Session</div>
        <a href="<?= e(url('admin/index.php?action=logout')) ?>"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>
</aside>
