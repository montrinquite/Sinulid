<?php
/** @var array $currentUser */ /** @var array $errors */ /** @var array $old */ /** @var string $csrf */
$name = $old['full_name'] ?? $currentUser['full_name'];
$bio  = $old['bio'] ?? ($currentUser['bio'] ?? '');
?>
<div class="page-head"><h1 class="h5 mb-0">Edit profile</h1></div>
<form method="post" action="<?= e(url('/profile/edit')) ?>" enctype="multipart/form-data" class="form-pane needs-validation" novalidate>
    <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
    <div class="d-flex align-items-center gap-3 mb-3">
        <?= avatar($currentUser['profile_image'], $currentUser['full_name'], 84) ?>
        <div>
            <label for="profile_image" class="form-label mb-1">Profile picture</label>
            <input type="file" id="profile_image" name="profile_image" class="form-control<?= isset($errors['profile_image']) ? ' is-invalid' : '' ?>" accept="image/jpeg,image/png,image/gif,image/webp">
            <div class="invalid-feedback"><?= e($errors['profile_image'] ?? '') ?></div>
            <div class="form-text">JPG, PNG, GIF or WEBP, up to 2 MB.</div>
            <?php if ($currentUser['profile_image']): ?>
                <div class="form-check mt-1"><input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove_image"><label class="form-check-label" for="remove_image">Remove current picture</label></div>
            <?php endif; ?>
        </div>
    </div>
    <div class="mb-3">
        <label for="full_name" class="form-label">Full name</label>
        <input type="text" id="full_name" name="full_name" class="form-control<?= isset($errors['full_name']) ? ' is-invalid' : '' ?>" value="<?= e($name) ?>" maxlength="100" required>
        <div class="invalid-feedback"><?= e($errors['full_name'] ?? 'Enter your full name.') ?></div>
    </div>
    <div class="mb-3">
        <label for="username_ro" class="form-label">Username</label>
        <input type="text" id="username_ro" class="form-control" value="@<?= e($currentUser['username']) ?>" disabled>
    </div>
    <div class="mb-3">
        <label for="bio" class="form-label">Bio</label>
        <textarea id="bio" name="bio" rows="3" maxlength="300" data-counter="bio-count" class="form-control<?= isset($errors['bio']) ? ' is-invalid' : '' ?>"><?= e($bio) ?></textarea>
        <div class="invalid-feedback"><?= e($errors['bio'] ?? '') ?></div>
        <div class="text-end small text-secondary"><span id="bio-count">0</span>/300</div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-dark rounded-pill px-4">Save changes</button>
        <a class="btn btn-outline-secondary rounded-pill" href="<?= e(url('/profile/' . rawurlencode($currentUser['username']))) ?>">Cancel</a>
    </div>
</form>