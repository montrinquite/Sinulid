<div class="container" style="max-width: 420px; margin-top: 60px;">
    <h2>Create an Account</h2>

    <form action="<?= e(url('/register')) ?>" method="post">
        <?= csrf_field() ?>

        <?php
        $fields = [
            'username'         => ['Username', 'text'],
            'email'            => ['Email', 'email'],
            'password'         => ['Password', 'password'],
            'confirm_password' => ['Confirm Password', 'password'],
        ];
        foreach ($fields as $key => [$label, $type]):
        ?>
            <div class="mb-3">
                <label for="<?= $key ?>"><?= $label ?></label>
                <input type="<?= $type ?>" name="<?= $key ?>" id="<?= $key ?>"
                       class="form-control <?= isset($errors[$key]) ? 'is-invalid' : '' ?>"
                       <?php if ($type !== 'password'): ?>value="<?= e($old[$key] ?? '') ?>"<?php endif; ?>>
                <?php if (isset($errors[$key])): ?>
                    <div class="invalid-feedback"><?= e($errors[$key]) ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="btn btn-primary w-100">Register</button>
        <p class="mt-3">Already have an account? <a href="<?= e(url('/login')) ?>">Log in</a></p>
    </form>
</div>