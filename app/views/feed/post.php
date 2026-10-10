<?php
/** @var array $post */ /** @var array $currentUser */
$mine   = (int)$post['user_id'] === (int)$currentUser['id'];
$edited = strtotime($post['updated_at']) - strtotime($post['created_at']) > 1;
$liked  = (bool)$post['liked_by_me'];
?>
<article class="post-card" data-post-id="<?= (int)$post['id'] ?>" data-content="<?= e($post['content']) ?>">
    <a href="<?= e(url('/profile/' . rawurlencode($post['username']))) ?>" class="flex-shrink-0"><?= avatar($post['profile_image'], $post['full_name'], 44) ?></a>
    <div class="post-body">
        <header class="d-flex align-items-start justify-content-between gap-2">
            <div class="min-w-0">
                <a class="fw-semibold text-body text-decoration-none" href="<?= e(url('/profile/' . rawurlencode($post['username']))) ?>"><?= e($post['full_name']) ?></a>
                <span class="text-secondary">@<?= e($post['username']) ?></span>
                <span class="text-secondary">· <time datetime="<?= e($post['created_at']) ?>"><?= e(time_ago($post['created_at'])) ?></time><?= $edited ? ' · edited' : '' ?></span>
            </div>
            <?php if ($mine): ?>
                <div class="dropdown">
                    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Post options"><i class="bi bi-three-dots"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><button class="dropdown-item" type="button" data-action="edit-post"><i class="bi bi-pencil me-2"></i>Edit text</button></li>
                        <li><a class="dropdown-item" href="<?= e(url('/posts/' . (int)$post['id'] . '/edit')) ?>"><i class="bi bi-image me-2"></i>Edit with image</a></li>
                        <li><button class="dropdown-item text-danger" type="button" data-action="delete-post"><i class="bi bi-trash me-2"></i>Delete</button></li>
                    </ul>
                </div>
            <?php endif; ?>
        </header>
        <div class="post-text"><?= nl2br(e($post['content'])) ?></div>
        <?php if ($post['image']): ?>
            <img class="post-img" loading="lazy" src="<?= e(upload_url($post['image'], 'posts')) ?>" alt="Image attached to <?= e($post['full_name']) ?>'s post">
        <?php endif; ?>
        <div class="post-actions">
            <button type="button" class="btn-action btn-like<?= $liked ? ' liked' : '' ?>" aria-pressed="<?= $liked ? 'true' : 'false' ?>" aria-label="Like">
                <i class="bi <?= $liked ? 'bi-heart-fill' : 'bi-heart' ?>"></i><span class="like-count"><?= (int)$post['like_count'] ?></span>
            </button>
            <button type="button" class="btn-action btn-comments" aria-expanded="false" aria-label="Comments">
                <i class="bi bi-chat"></i><span class="comment-count"><?= (int)$post['comment_count'] ?></span>
            </button>
        </div>
        <section class="comments-panel d-none" aria-label="Comments">
            <div class="comments-list"></div>
            <form class="comment-form" autocomplete="off">
                <label class="visually-hidden" for="c-<?= (int)$post['id'] ?>">Write a comment</label>
                <input id="c-<?= (int)$post['id'] ?>" name="content" class="form-control" maxlength="500" placeholder="Write a comment…" required>
                <button class="btn btn-dark rounded-pill btn-sm px-3">Reply</button>
            </form>
        </section>
    </div>
</article>