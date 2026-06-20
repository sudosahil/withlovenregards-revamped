<?php
/**
 * Admin shell header. Every admin page includes this after its own
 * require_admin() guard. Expects $adminTitle and optional $adminActive (slug).
 */
require_once __DIR__ . '/../../core/functions.php';

$adminTitle = $adminTitle ?? 'Dashboard';
$adminActive = $adminActive ?? '';
$adminUser = $_SESSION['admin_user'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title><?= e($adminTitle) ?> | <?= e(SITE_NAME) ?> Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
</head>
<body class="admin">
<div class="admin-shell">
    <?php require __DIR__ . '/admin-sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-topbar">
            <button class="admin-menu-toggle" id="adminMenuToggle" aria-label="Toggle menu"><i class="fa-solid fa-bars"></i></button>
            <h1><?= e($adminTitle) ?></h1>
            <div class="admin-topbar__spacer"></div>
            <a class="abtn abtn--ghost abtn--sm" href="<?= e(url()) ?>" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> View Site</a>
            <div class="admin-topbar__user">
                <span class="avatar"><?= e(strtoupper($adminUser[0])) ?></span>
                <span><?= e($adminUser) ?></span>
            </div>
        </div>
        <div class="admin-content">
