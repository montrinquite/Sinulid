<?php
/** @var array $profile */ /** @var bool $isOwner */ /** @var int $postCount */
/** @var array $posts */ /** @var bool $hasMore */ /** @var int $perPage */
?>
<section class="profile-head">
  <div class="d-flex justify-content-between align-items-start gap-3">
    <div class="min-w-0">
      <h1 class="h3 fw-bold mb-0"><?= e($profile['full_name']) ?></h1>
      <div class="text-secondary">@<?= e($profile['username']) ?></div>
    </div>
    <?= avatar($profile['profile_image'], $profile['full_name'], 84) ?>
  </div>
  <p class="mt-3 mb-2"><?= $profile['bio'] ? nl2br(e($profile['bio'])) : '<span class="text-secondary">No bio yet.</span>' ?></p>
  <div class="text-secondary small mb-3">
    <i class="bi bi-calendar3 me-1"></i>Joined <?= e(date('F j, Y', strtotime($profile['created_at']))) ?>
    <span class="mx-2">·</span><strong class="text-body"><?= (int)$postCount ?></strong> <?= $postCount === 1 ? 'post' : 'posts' ?>
  </div>
  <?php if ($isOwner): ?>
    <a class="btn btn-outline-dark w-100 rounded-pill" href="<?= e(url('/profile/edit')) ?>"><i class="bi bi-pencil me-1"></i>Edit Profile</a>
  <?php endif; ?>
</section>
<div class="page-head"><ul class="nav nav-tabs-lite"><li><a class="active" href="#">Posts</a></li></ul></div>
<div id="feed" data-endpoint="<?= e(url('/feed/load')) ?>" data-sort="latest" data-user="<?= e($profile['username']) ?>" data-offset="<?= (int)$perPage ?>">
  <?php foreach ($posts as $post) { require BASE_PATH . '/app/views/feed/_post.php'; } ?>
</div>
<div id="empty-state" class="empty-state<?= $posts ? ' d-none' : '' ?>">
  <i class="bi bi-chat-square-text"></i>
  <h2 class="h5">No posts yet</h2>
  <p class="text-secondary mb-0"><?= $isOwner ? 'Share your first post from the home page.' : e($profile['full_name']) . ' hasn\'t posted anything yet.' ?></p>
</div>
<div class="text-center my-4">
  <div id="feed-spinner" class="spinner-border spinner-border-sm d-none" role="status"><span class="visually-hidden">Loading…</span></div>
  <button type="button" id="load-more" class="btn btn-outline-secondary rounded-pill px-4<?= $hasMore ? '' : ' d-none' ?>">Load more</button>
</div>