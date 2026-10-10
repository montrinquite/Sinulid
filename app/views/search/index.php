<?php /** @var string $q */ /** @var array $users */ /** @var array $posts */ /** @var array $currentUser */ ?>
<div class="page-head"><h1 class="h5 mb-0">Search</h1></div>
<form action="<?= e(url('/search')) ?>" method="get" class="form-pane pb-2" role="search">
    <label for="q" class="visually-hidden">Search users or posts</label>
    <div class="input-group">
        <input type="search" id="q" name="q" class="form-control rounded-start-pill" value="<?= e($q) ?>" placeholder="Search by name, @username or post text" maxlength="100" autofocus>
        <button class="btn btn-dark rounded-end-pill px-3" aria-label="Search"><i class="bi bi-search"></i></button>
    </div>
</form>
<?php if ($q === ''): ?>
    <div class="empty-state"><i class="bi bi-search"></i><h2 class="h5">Find people on Threadly</h2><p class="text-secondary mb-0">Search by full name or username. Post keywords work too.</p></div>
<?php else: ?>
    <h2 class="section-title">People</h2>
    <?php if (!$users): ?>
        <p class="px-3 text-secondary">No users found for “<?= e($q) ?>”.</p>
    <?php endif; ?>
    <?php foreach ($users as $u): ?>
        <a class="user-row user-row-lg" href="<?= e(url('/profile/' . rawurlencode($u['username']))) ?>">
            <?= avatar($u['profile_image'], $u['full_name'], 48) ?>
            <span class="min-w-0">
                <span class="d-block fw-semibold text-truncate"><?= e($u['full_name']) ?></span>
                <span class="d-block text-secondary small">@<?= e($u['username']) ?></span>
                <?php if ($u['bio']): ?><span class="d-block small text-truncate"><?= e($u['bio']) ?></span><?php endif; ?>
            </span>
        </a>
    <?php endforeach; ?>
    <h2 class="section-title">Posts</h2>
    <?php if (!$posts): ?>
        <p class="px-3 text-secondary">No posts match “<?= e($q) ?>”.</p>
    <?php endif; ?>
    <div id="feed" data-static="1">
        <?php foreach ($posts as $post) { require BASE_PATH . '/app/views/feed/_post.php'; } ?>
    </div>
<?php endif; ?>