<?php
// Variables from render(): $title, $flash, $errors, $old
$flashClasses = [
    'success' => 'alert-success',
    'error'   => 'alert-danger',
    'warning' => 'alert-warning',
    'info'    => 'alert-info',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Social App') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Optional: <link href="<?= e(url('/assets/css/style.css')) ?>" rel="stylesheet"> -->
</head>
<body class="bg-light">

<?php require APP . '/views/layouts/nav.php'; ?>

<main class="container py-4">
    <?php if (!empty($flash)): ?>
        <div class="alert <?= e($flashClasses[$flash['type']] ?? 'alert-info') ?> alert-dismissible fade show" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>