<?php /** @var ?array $currentUser */ /** @var string $csrf */ ?>
<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf" content="<?= e($csrf) ?>">
    <title><?= e(($title ?? 'Threadly') . ' • Threadly') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= e(asset('css/style.css')) ?>" rel="stylesheet">
    <script>try{var t=localStorage.getItem('threadly-theme');if(t){document.documentElement.setAttribute('data-bs-theme',t);}}catch(e){}</script>
</head>
<body data-base="<?= e(BASE_URL) ?>" class="<?= $currentUser ? 'is-auth' : 'is-guest' ?>">
    <a class="visually-hidden-focusable skip-link" href="#main">Skip to content</a>
    <?php
    if ($currentUser) {
        require BASE_PATH . '/app/views/layouts/navigation.php';
    } else {
    echo '<main id="main" class="auth-wrap">';
    }