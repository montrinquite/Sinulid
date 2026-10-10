<?php /** @var array $post */ /** @var array $errors */ /** @var string $csrf */ ?>
<div class="page-head"><h1 class="h5 mb-0">Edit post</h1></div>
<form method="post" action="<?= e(url('/posts/' . (int)$post['id'] . '/update')) ?>" enctype="multipart/form-data" class="form-pane">
    <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
    <div class="mb-3">
        <label for="content" class="form-label">Post text</label>
        <textarea id="content" name="content" rows="5" maxlength="1000" class="form-control" data-counter="edit-count" required><?= e($post['content']) ?></textarea>
        <div class="text-end small text-secondary"><span id="edit-count">0</span>/1000</div>
    </div>
    <?php if ($post['image']): ?>
        <div class="mb-3">
            <p class="form-label mb-1">Current image</p>
            <img class="post-img" src="<?= e(upload_url($post['image'], 'posts')) ?>" alt="Current post image">
            <div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove_image"><label class="form-check-label" for="remove_image">Remove this image</label></div>
        </div>
    <?php endif; ?>
    <div class="mb-3">
        <label for="image" class="form-label"><?= $post['image'] ? 'Replace image' : 'Add an image' ?> (optional)</label>
        <input type="file" id="image" name="image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-dark rounded-pill px-4">Save changes</button>
        <a class="btn btn-outline-secondary rounded-pill" href="<?= e(url('/')) ?>">Cancel</a>
    </div>
</form>