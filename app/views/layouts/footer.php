<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php /** @var ?array $currentUser */ /** @var string $csrf */ /** @var ?array $flash */ ?>
    <?php if ($currentUser): ?>
        </main>
            <aside class="aside d-none d-lg-block" aria-label="Search and suggestions">
                <form action="<?= e(url('/search')) ?>" method="get" class="aside-card search-box" role="search">
                    <label for="aside-q" class="visually-hidden">Search users</label>
                    <i class="bi bi-search"></i>
                    <input id="aside-q" type="search" name="q" class="form-control" placeholder="Search people or posts" maxlength="100">
                </form>
                <section class="aside-card">
                    <h2 class="aside-title">Suggested users</h2>
                    <?php if (!$suggested): ?>
                        <p class="text-secondary small mb-0">No other users yet.</p>
                    <?php endif; ?>
                    <?php foreach ($suggested as $s): ?>
                        <a class="user-row" href="<?= e(url('/profile/' . rawurlencode($s['username']))) ?>">
                            <?= avatar($s['profile_image'], $s['full_name'], 40) ?>
                            <span class="min-w-0">
                                <span class="d-block fw-semibold text-truncate"><?= e($s['full_name']) ?></span>
                                <span class="d-block text-secondary small text-truncate">@<?= e($s['username']) ?></span>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </section>
                <section class="aside-card">
                    <h2 class="aside-title">About Threadly</h2>
                    <p class="text-secondary small mb-0">Threadly is a minimalist mini social network built with PHP MVC and MySQL: share short posts, comment, like, and discover people.</p>
                </section>
            </aside>
            <nav class="bottom-nav d-md-none" aria-label="Mobile navigation">
                <a href="<?= e(url('/')) ?>" aria-label="Home"><i class="bi bi-house-door"></i></a>
                <a href="<?= e(url('/search')) ?>" aria-label="Search"><i class="bi bi-search"></i></a>
                <a href="<?= e(url('/#composer')) ?>" data-action="focus-composer" aria-label="Create post"><i class="bi bi-plus-square"></i></a>
                <a href="<?= e(url('/profile/' . rawurlencode($currentUser['username']))) ?>" aria-label="Profile"><i class="bi bi-person"></i></a>
            </nav>
            <div class="modal fade" id="edit-post-modal" tabindex="-1" aria-labelledby="edit-post-title" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
                    <div class="modal-header"><h2 class="modal-title fs-5" id="edit-post-title">Edit post</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                        <div class="modal-body">
                            <label for="edit-post-text" class="visually-hidden">Post text</label>
                            <textarea id="edit-post-text" class="form-control" rows="5" maxlength="1000" data-counter="edit-post-count"></textarea>
                            <div class="text-end small text-secondary mt-1"><span id="edit-post-count">0</span>/1000</div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-dark rounded-pill" id="edit-post-save">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="confirm-modal" tabindex="-1" aria-labelledby="confirm-title" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content">
                    <div class="modal-body text-center p-4">
                        <h2 class="fs-5" id="confirm-title">Are you sure?</h2>
                        <p class="text-secondary" id="confirm-message">This cannot be undone.</p>
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger rounded-pill" id="confirm-ok">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
    <?php else: ?>
        </main>
    <?php endif; ?>
    <div class="toast-container position-fixed top-0 end-0 p-3" id="toast-container" aria-live="polite" aria-atomic="true"></div>
    <?php if (!empty($flash)): ?>
        <div id="flash-data" hidden data-type="<?= e($flash['type']) ?>" data-message="<?= e($flash['message']) ?>"></div>
    <?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>