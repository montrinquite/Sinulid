<?php /** @var array $errors */ /** @var array $old */ /** @var string $csrf */
$f = fn(string $k) => isset($errors[$k]) ? ' is-invalid' : '';
?>
<div class="auth-card">
    <div class="text-center mb-4"><i class="bi bi-at brand-mark"></i><h1 class="h3 fw-bold mt-2">Create your account</h1></div>
    <form method="post" action="<?= e(url('/register')) ?>" class="needs-validation" novalidate>
        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
        <div class="mb-3">
            <label for="full_name" class="form-label">Full name</label>
            <input type="text" class="form-control<?= $f('full_name') ?>" id="full_name" name="full_name" value="<?= e($old['full_name'] ?? '') ?>" maxlength="100" required autocomplete="name">
            <div class="invalid-feedback"><?= e($errors['full_name'] ?? 'Enter your full name.') ?></div>
        </div>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control<?= $f('username') ?>" id="username" name="username" value="<?= e($old['username'] ?? '') ?>" pattern="[A-Za-z0-9_]{3,30}" required autocomplete="username">
            <div class="form-text">3–30 characters: letters, numbers, underscore.</div>
            <div class="invalid-feedback"><?= e($errors['username'] ?? 'Use 3–30 letters, numbers or underscores.') ?></div>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control<?= $f('email') ?>" id="email" name="email" value="<?= e($old['email'] ?? '') ?>" maxlength="190" required autocomplete="email">
            <div class="invalid-feedback"><?= e($errors['email'] ?? 'Enter a valid email address.') ?></div>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control<?= $f('password') ?>" id="password" name="password" minlength="8" required autocomplete="new-password">
            <div class="invalid-feedback"><?= e($errors['password'] ?? 'Password must be at least 8 characters.') ?></div>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm password</label>
            <input type="password" class="form-control<?= $f('confirm_password') ?>" id="confirm_password" name="confirm_password" required autocomplete="new-password">
            <div class="invalid-feedback"><?= e($errors['confirm_password'] ?? 'Passwords do not match.') ?></div>
        </div>
        <button class="btn btn-dark w-100 rounded-pill py-2">Sign up</button>
    </form>
    <p class="text-center text-secondary mt-3 mb-0">Already have an account? <a href="<?= e(url('/login')) ?>">Log in</a></p>
</div>