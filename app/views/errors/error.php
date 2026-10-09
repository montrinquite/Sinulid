<?php
$titles = [
    403 => 'Access denied',
    404 => 'Page not found',
    419 => 'Session expired',
    500 => 'Something went wrong',
];
$heading = $titles[$code ?? 500] ?? 'Error';
?>
<div class="container text-center" style="max-width: 520px; margin-top: 80px;">
    <h1 class="display-1 fw-bold text-secondary"><?= e((string) ($code ?? 500)) ?></h1>
    <h2 class="mb-3"><?= e($heading) ?></h2>
    <p class="text-muted mb-4"><?= e($message ?? 'An unexpected error occurred.') ?></p>

    <a href="<?= e(url('/')) ?>" class="btn btn-primary">Back to home</a>
    <?php if (auth_user() === null): ?>
        <a href="<?= e(url('/login')) ?>" class="btn btn-outline-secondary">Log in</a>
    <?php endif; ?>
</div>