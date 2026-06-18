<?php
/**
 * Admin login. Separate session flag from the customer session.
 *
 * Placeholder credentials (replace with a DB-backed admin user table when live):
 *   username: admin   password: admin123
 * The password is verified against a bcrypt hash so the live swap is trivial.
 */
require_once __DIR__ . '/../includes/functions.php';

// Logout
if (($_GET['action'] ?? '') === 'logout') {
    unset($_SESSION['admin_logged_in'], $_SESSION['admin_user']);
    header('Location: ' . url('admin/'));
    exit;
}

// Already in.
if (is_admin()) {
    header('Location: ' . url('admin/dashboard'));
    exit;
}

// Placeholder admin: username "admin", password "admin123".
$ADMIN_USERNAME = 'admin';
$ADMIN_HASH = password_hash('admin123', PASSWORD_BCRYPT); // recomputed each load (placeholder only)

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $error = 'Session expired. Please try again.';
    } else {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        if ($username === $ADMIN_USERNAME && password_verify($password, $ADMIN_HASH)) {
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = $username;
            header('Location: ' . url('admin/dashboard'));
            exit;
        }
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Admin Login | <?= e(SITE_NAME) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
</head>
<body class="admin">
<div class="admin-login">
    <div class="admin-login__card">
        <h1>With<span style="color:#e8335a;">Love</span>NRegards</h1>
        <p class="sub">Admin Panel Login</p>
        <?php if ($error): ?>
            <div class="alert-inline alert-inline--error"><?= e($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <?= csrf_field() ?>
            <div class="afield"><label>Username</label><input type="text" name="username" autofocus required></div>
            <div class="afield"><label>Password</label><input type="password" name="password" required></div>
            <button type="submit" class="abtn abtn--primary" style="width:100%;justify-content:center;">Sign In</button>
        </form>
        <p style="text-align:center;font-size:.78rem;color:#8a8a9a;margin-top:18px;">Demo credentials: admin / admin123</p>
    </div>
</div>
</body>
</html>
