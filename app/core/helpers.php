<?php
/** View/utility helpers. No database access here. */
 
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
 
function url(string $path = '/'): string
{
    return BASE_URL . $path;
}
 
function auth_user(): ?array
{
    return isset($_SESSION['user_id'])
        ? ['id' => (int) $_SESSION['user_id'], 'username' => $_SESSION['username'] ?? '']
        : null;
}
 
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
 
function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}
 
function partial(string $__name, array $__vars = []): void
{
    extract($__vars, EXTR_SKIP);
    require APP . '/views/partials/' . $__name . '.php';
}
 