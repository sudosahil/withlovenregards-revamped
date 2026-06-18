<?php
/**
 * 404 Not Found.
 */
require_once __DIR__ . '/../includes/functions.php';
http_response_code(404);

$seo = [
    'title'       => 'Page Not Found | ' . SITE_NAME,
    'description' => 'The page you are looking for could not be found.',
    'canonical'   => BASE_URL . '/404/',
];
require __DIR__ . '/../includes/header.php';
?>
<main class="container">
    <div class="empty-state" style="padding:80px 20px;">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <h1 style="font-size:2.4rem;">404</h1>
        <h3>Page Not Found</h3>
        <p>The page you're looking for doesn't exist or has moved.</p>
        <a class="btn btn--primary" href="<?= e(url()) ?>">Back to Home</a>
    </div>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
