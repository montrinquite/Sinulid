<?php
/**
 * Base controller: rendering, redirects, auth guard, CSRF, flash messages.
 * Controllers call models and pass data to views. Views never query the DB.
 */
abstract class Controller
{
    protected function render(string $view, array $data = [], string $title = 'Social App'): void
    {
        // One-time session data: flash message, validation errors, old input
        $data += [
            'title'  => $title,
            'flash'  => $this->pull('flash'),
            'errors' => $this->pull('errors') ?? [],
            'old'    => $this->pull('old') ?? [],
        ];
 
        $this->include(APP . '/views/layouts/header.php', $data);
        $this->include(APP . "/views/$view.php", $data);
        $this->include(APP . '/views/layouts/footer.php', $data);
    }
 
    private function include(string $file, array $vars): void
    {
        extract($vars, EXTR_SKIP);
        require $file;
    }
 
    protected function redirect(string $path): void
    {
        header('Location: ' . url($path));
        exit;
    }
 
    /** Redirect to the page the user came from (same site only). */
    protected function redirectBack(string $fallback = '/'): void
    {
        $ref = parse_url($_SERVER['HTTP_REFERER'] ?? '');
        if (!empty($ref['path']) && ($ref['host'] ?? $_SERVER['HTTP_HOST']) === ($_SERVER['HTTP_HOST'] ?? '')) {
            $path = $ref['path'] . (isset($ref['query']) ? '?' . $ref['query'] : '');
            header('Location: ' . $path);
            exit;
        }
        $this->redirect($fallback);
    }
 
    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }
 
    protected function backWithErrors(string $path, array $errors, array $old = []): void
    {
        $_SESSION['errors'] = $errors;
        $_SESSION['old']    = $old;
        $this->redirect($path);
    }
 
    private function pull(string $key): mixed
    {
        $value = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $value;
    }
 
    protected function requireAuth(): array
    {
        $user = auth_user();
        if ($user === null) {
            $this->flash('error', 'Please log in first.');
            $this->redirect('/login');
        }
        return $user;
    }
 
    protected function guestOnly(): void
    {
        if (auth_user() !== null) {
            $this->redirect('/');
        }
    }
 
    protected function verifyCsrf(): void
    {
        $sent = $_POST['_csrf'] ?? '';
        if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
            $this->abort(419, 'Your session expired. Please go back and try again.');
        }
    }
 
    protected function abort(int $code, string $message): void
    {
        http_response_code($code);
        $this->render('errors/error', ['code' => $code, 'message' => $message], "Error $code");
        exit;
    }
 
    protected function input(string $key): string
    {
        $v = $_POST[$key] ?? '';
        return is_string($v) ? trim($v) : '';
    }
 
    protected function page(): int
    {
        return max(1, (int) ($_GET['page'] ?? 1));
    }
}