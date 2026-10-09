<div class="container" style="max-width: 420px; margin-top: 60px;">
    <h2>Log In</h2>

    <?php if (!empty($errors['login'])): ?>
        <div class="alert alert-danger"><?= e($errors['login']) ?></div>
    <?php endif; ?>

    <form action="<?= e(url('/login')) ?>" method="post">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control"
                   value="<?= e($old['email'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Log In</button>
        <p class="mt-3">No account? <a href="<?= e(url('/register')) ?>">Register</a></p>
    </form>
</div>