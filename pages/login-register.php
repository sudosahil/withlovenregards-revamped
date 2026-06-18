<?php
/**
 * Combined login / register page with two tabs. Posts to api/auth.php.
 * #login and #register anchors select the corresponding tab on load.
 */
require_once __DIR__ . '/../includes/functions.php';

if (is_logged_in()) {
    header('Location: ' . url('my-account'));
    exit;
}

$error = $_SESSION['auth_error'] ?? '';
unset($_SESSION['auth_error']);

$seo = [
    'title'       => 'Login or Register | ' . SITE_NAME,
    'description' => 'Sign in to your account or create a new one to track orders and save favourites.',
    'canonical'   => BASE_URL . '/login-register/',
];
require __DIR__ . '/../includes/header.php';
?>
<main class="container">
    <div class="auth-wrap">
        <div class="auth-tabs">
            <button class="auth-tab active" data-tab="login">Sign In</button>
            <button class="auth-tab" data-tab="register">Register</button>
        </div>

        <?php if ($error): ?>
            <div class="alert alert--error"><?= e($error) ?></div>
        <?php endif; ?>

        <!-- Login -->
        <div class="auth-panel active" id="panel-login">
            <form action="<?= e(url('api/auth.php?action=login')) ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-field" style="margin-bottom:14px;"><label>Email</label><input type="email" name="email" required></div>
                <div class="form-field" style="margin-bottom:14px;"><label>Password</label><input type="password" name="password" required></div>
                <label class="filter-check" style="margin-bottom:14px;"><input type="checkbox" name="remember"> Remember me</label>
                <button type="submit" class="btn btn--primary btn--block btn--lg">Sign In</button>
                <p style="text-align:center;margin-top:12px;font-size:.85rem;"><a href="#" style="color:var(--primary);">Forgot password?</a></p>
            </form>
        </div>

        <!-- Register -->
        <div class="auth-panel" id="panel-register">
            <form action="<?= e(url('api/auth.php?action=register')) ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-field" style="margin-bottom:14px;"><label>Full Name</label><input type="text" name="name" required></div>
                <div class="form-field" style="margin-bottom:14px;"><label>Email</label><input type="email" name="email" required></div>
                <div class="form-field" style="margin-bottom:14px;"><label>Phone</label><input type="tel" name="phone"></div>
                <div class="form-field" style="margin-bottom:14px;"><label>Password</label><input type="password" name="password" minlength="6" required></div>
                <div class="form-field" style="margin-bottom:14px;"><label>Confirm Password</label><input type="password" name="confirm_password" minlength="6" required></div>
                <button type="submit" class="btn btn--primary btn--block btn--lg">Create Account</button>
            </form>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
