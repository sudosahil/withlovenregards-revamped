<?php
/**
 * Shared renderer for policy / legal pages.
 * Expects: $legalTitle, $legalSlug, $legalIntro (string), $legalSections (array of [h2, body]).
 */
require_once __DIR__ . '/functions.php';

$seo = [
    'title'       => $legalTitle . ' | ' . SITE_NAME,
    'description' => $legalIntro,
    'canonical'   => BASE_URL . '/' . $legalSlug . '/',
];
require __DIR__ . '/header.php';
?>
<main class="container section">
    <h1 class="section__title"><?= e($legalTitle) ?></h1>
    <div style="max-width:840px;margin:0 auto;color:var(--muted);line-height:1.8;">
        <p><?= e($legalIntro) ?></p>
        <?php foreach ($legalSections as [$h2, $body]): ?>
            <h2 style="color:var(--secondary);"><?= e($h2) ?></h2>
            <p><?= nl2br(e($body)) ?></p>
        <?php endforeach; ?>
        <p style="margin-top:24px;font-size:.85rem;">Last updated: <?= e(date('F Y')) ?>. For any questions, contact us at <a href="mailto:<?= e(ADMIN_EMAIL) ?>" style="color:var(--primary);"><?= e(ADMIN_EMAIL) ?></a>.</p>
    </div>
</main>
<?php require __DIR__ . '/footer.php'; ?>
