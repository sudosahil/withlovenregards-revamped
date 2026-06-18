<?php
/**
 * Customer auth endpoint: login | register | logout.
 *
 * In placeholder mode there is no users table, so registration creates a
 * session user in-memory and login matches against the placeholder customers
 * (any password accepted for demo accounts). Password hashing with bcrypt is
 * wired so swapping to a real users table is a one-line change.
 */
require_once __DIR__ . '/../includes/functions.php';

$action = $_REQUEST['action'] ?? '';

if ($action === 'logout') {
    unset($_SESSION['user']);
    header('Location: ' . url());
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('login-register'));
    exit;
}

if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    $_SESSION['auth_error'] = 'Your session expired. Please try again.';
    header('Location: ' . url('login-register'));
    exit;
}

if ($action === 'register') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['confirm_password'] ?? '');

    $errors = [];
    if ($name === '') $errors[] = 'Name is required.';
    if (!$email) $errors[] = 'A valid email is required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if ($errors) {
        $_SESSION['auth_error'] = implode(' ', $errors);
        header('Location: ' . url('login-register#register'));
        exit;
    }

    // Live equivalent: INSERT INTO users (name,email,phone,password_hash) VALUES (?,?,?,?)
    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    $_SESSION['user'] = [
        'id'    => 0,
        'name'  => $name,
        'email' => $email,
        'phone' => $phone,
        'hash'  => $hash, // stored to demonstrate flow; not persisted in placeholder mode
    ];
    header('Location: ' . url('my-account'));
    exit;
}

if ($action === 'login') {
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = (string) ($_POST['password'] ?? '');

    if (!$email || $password === '') {
        $_SESSION['auth_error'] = 'Please enter your email and password.';
        header('Location: ' . url('login-register#login'));
        exit;
    }

    // Match against placeholder customers. Live: SELECT password_hash WHERE email = ?
    $matched = null;
    foreach (get_customers() as $c) {
        if (strcasecmp($c['email'], $email) === 0) {
            $matched = $c;
            break;
        }
    }

    if (!$matched) {
        $_SESSION['auth_error'] = 'No account found with that email.';
        header('Location: ' . url('login-register#login'));
        exit;
    }

    // Placeholder accounts have no stored hash — accept any password for demo.
    // Live: if (!password_verify($password, $matched['password_hash'])) { fail }
    $_SESSION['user'] = [
        'id'    => $matched['id'],
        'name'  => $matched['name'],
        'email' => $matched['email'],
        'phone' => $matched['phone'],
    ];
    header('Location: ' . url('my-account'));
    exit;
}

header('Location: ' . url('login-register'));
exit;
