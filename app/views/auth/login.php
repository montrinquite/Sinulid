<?php /** @var array $errors */ /** @var array $old */ /** @var string $csrf */ ?>
<div class="auth-card">
    <div class="text-center mb-4"><i class="bi bi-at brand-mark"></i><h1 class="h3 fw-bold mt-2">Log in to Threadly</h1></div>
  <?php if (!empty($errors['login'])): ?><div class="alert alert-danger" role="alert"><?= e($errors['login']) ?></div><?php endif; ?>
    <form method="post" action="<?= e(url('/login')) ?>" class="needs-validation" novalidate>
        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
        <div class="mb-3">
            <label for="identifier" class="form-label">Username or email</label>
            <input type="text" class="form-control<?= isset($errors['identifier']) ? ' is-invalid' : '' ?>" id="identifier" name="identifier" value="<?= e($old['identifier'] ?? '') ?>" required autofocus autocomplete="username">
            <div class="invalid-feedback"><?= e($errors['identifier'] ?? 'Enter your username or email.') ?></div>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control<?= isset($errors['password']) ? ' is-invalid' : '' ?>" id="password" name="password" required autocomplete="current-password">
            <div class="invalid-feedback"><?= e($errors['password'] ?? 'Enter your password.') ?></div>
        </div>
        <button class="btn btn-dark w-100 rounded-pill py-2">Log in</button>
    </form>
    <p class="text-center text-secondary mt-3 mb-0">New here? <a href="<?= e(url('/register')) ?>">Create an account</a></p>
</div>