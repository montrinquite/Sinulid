<?php
/** @var array $currentUser */ /** @var string $csrf */
$path = '/' . trim(substr((string)parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), strlen(BASE_URL)), '/');
$isHome    = $path === '/';
$isSearch  = str_starts_with($path, '/search');
$isProfile = $path === '/profile/' . $currentUser['username'] || $path === '/profile/edit';
$act = fn(bool $on) => $on ? ' active" aria-current="page' : '';
?>
<header class="mobile-top d-md-none">
  <a class="brand" href="<?= e(url('/')) ?>"><i class="bi bi-at"></i><span>Threadly</span></a>
    <div class="d-flex align-items-center gap-1">
        <button type="button" class="btn btn-icon" data-action="toggle-theme" aria-label="Toggle dark mode">
            <i class="bi bi-moon-stars icon-moon"></i><i class="bi bi-sun icon-sun"></i>
        </button>
        <form method="post" action="<?= e(url('/logout')) ?>" class="m-0">
            <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
            <button class="btn btn-icon" aria-label="Log out"><i class="bi bi-box-arrow-right"></i></button>
        </form>
    </div>
</header>
<div class="app-layout">
    <nav class="sidebar d-none d-md-flex" aria-label="Main navigation">
        <a class="brand mb-3" href="<?= e(url('/')) ?>"><i class="bi bi-at"></i><span class="label">Threadly</span></a>
        <a class="side-link<?= $act($isHome) ?>" href="<?= e(url('/')) ?>"><i class="bi bi-house-door"></i><span class="label">Home</span></a>
        <a class="side-link<?= $act($isSearch) ?>" href="<?= e(url('/search')) ?>"><i class="bi bi-search"></i><span class="label">Search</span></a>
        <a class="side-link" href="<?= e(url('/#composer')) ?>" data-action="focus-composer"><i class="bi bi-plus-square"></i><span class="label">Create Post</span></a>
        <a class="side-link<?= $act($isProfile) ?>" href="<?= e(url('/profile/' . rawurlencode($currentUser['username']))) ?>"><i class="bi bi-person"></i><span class="label">Profile</span></a>
        <div class="mt-auto d-flex flex-column gap-1">
            <button type="button" class="side-link btn-reset" data-action="toggle-theme">
                <i class="bi bi-moon-stars icon-moon"></i><i class="bi bi-sun icon-sun"></i><span class="label">Dark mode</span>
            </button>
            <form method="post" action="<?= e(url('/logout')) ?>" class="m-0">
                <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                <button class="side-link btn-reset w-100"><i class="bi bi-box-arrow-right"></i><span class="label">Logout</span></button>
            </form>
        </div>
    </nav>
  <main id="main" class="center-col">