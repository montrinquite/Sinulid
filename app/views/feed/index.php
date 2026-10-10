<?php
/** @var array $posts */ /** @var bool $hasMore */ /** @var string $sort */ /** @var int $perPage */
/** @var array $currentUser */ /** @var string $csrf */
?>
<div class="page-head">
    <ul class="nav nav-tabs-lite" role="tablist" aria-label="Feed tabs">
        <li><a class="<?= $sort === 'latest' ? 'active' : '' ?>" href="<?= e(url('/')) ?>">For you</a></li>
        <li><a class="<?= $sort === 'top' ? 'active' : '' ?>" href="<?= e(url('/?sort=top')) ?>">Top</a></li>
    </ul>
</div>
<form id="composer-form" class="composer" method="post" action="<?= e(url('/posts')) ?>" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
    <?= avatar($currentUser['profile_image'], $currentUser['full_name'], 44) ?>
    <div class="flex-grow-1 min-w-0">
        <label for="composer" class="visually-hidden">What's new?</label>
        <textarea id="composer" name="content" class="composer-input" rows="2" maxlength="1000" placeholder="What's new, <?= e(explode(' ', $currentUser['full_name'])[0]) ?>?" data-counter="char-count" required></textarea>
        <img id="composer-preview" class="post-img d-none" alt="Selected image preview">
        <div class="d-flex align-items-center justify-content-between mt-2">
            <div class="d-flex align-items-center gap-2">
                <label class="btn btn-icon mb-0" for="composer-image" title="Add image" tabindex="0"><i class="bi bi-image"></i></label>
                <input type="file" id="composer-image" name="image" class="visually-hidden" accept="image/jpeg,image/png,image/gif,image/webp" data-preview="composer-preview">
                <span class="small text-secondary"><span id="char-count">0</span>/1000</span>
            </div>
            <button class="btn btn-dark rounded-pill px-4" id="composer-submit">Post</button>
        </div>
    </div>
</form>
<div id="feed" data-endpoint="<?= e(url('/feed/load')) ?>" data-sort="<?= e($sort) ?>" data-offset="<?= (int)$perPage ?>">
    <?php foreach ($posts as $post) { require BASE_PATH . '/app/views/feed/_post.php'; } ?>
</div>
<div id="empty-state" class="empty-state<?= $posts ? ' d-none' : '' ?>">
    <i class="bi bi-chat-square-text"></i>
    <h2 class="h5">No posts yet</h2>
    <p class="text-secondary mb-0">Be the first to share something with Threadly.</p>
</div>
<div class="text-center my-4">
    <div id="feed-spinner" class="spinner-border spinner-border-sm d-none" role="status"><span class="visually-hidden">Loading…</span></div>
    <button type="button" id="load-more" class="btn btn-outline-secondary rounded-pill px-4<?= $hasMore ? '' : ' d-none' ?>">Load more</button>
</div>