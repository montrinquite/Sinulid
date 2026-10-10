<?php
declare(strict_types=1);

class Controller
{
    public function view(string $view, array $data = [], bool $layout = true): void
    {
        extract($data, EXTR_SKIP);
        $currentUser = $this->currentUser();
        $flash = $this->pullFlash();
        $csrf = $this->csrfToken();
        $suggested = ($layout && $currentUser) ? (new UserModel())->suggested((int)$currentUser['id'], 4) : [];
        if ($layout) require BASE_PATH . '/app/views/layouts/header.php';
        require BASE_PATH . "/app/views/{$view}.php";
        if ($layout) require BASE_PATH . '/app/views/layouts/footer.php';
    }

    public function partial(string $view, array $data = []): string
    {
        extract($data, EXTR_SKIP);
        $currentUser = $this->currentUser();
        $csrf = $this->csrfToken();
        ob_start();
        require BASE_PATH . "/app/views/{$view}.php";
        return (string)ob_get_clean();
    }
 
    public function isAjax(): bool
    {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch';
    }

    public function json(array $payload, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function redirect(string $path): never
    {
        header('Location: ' . url($path));
        exit;
    }

    // ---- Auth helpers ----
    public function userId(): ?int { return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null; }

    public function currentUser(): ?array
    {
        static $cache = null;
        if ($cache === null && $this->userId()) {
            $cache = (new UserModel())->findById($this->userId()) ?: false;
        }
        return $cache ?: null;
    }

    public function requireAuth(bool $ajax = false): int
    {
        if (!$this->userId() || !$this->currentUser()) {
            session_unset();
            if ($ajax) $this->json(['ok' => false, 'message' => 'Please log in first.'], 401);
            $this->redirect('/login');
        }
        return $this->userId();
    }

    public function requireGuest(): void
    {
        if ($this->userId()) $this->redirect('/');
    }

    public function csrfToken(): string
    {
        if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf'];
    }

    public function verifyCsrf(bool $ajax = false): void
    {
        $sent = $_POST['csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!is_string($sent) || !hash_equals($_SESSION['csrf'] ?? '', $sent)) {
            if ($ajax) $this->json(['ok' => false, 'message' => 'Invalid security token. Refresh the page.'], 419);
            http_response_code(419);
            exit('Invalid CSRF token.');
        }
    }

    public function flash(string $type, string $message): void { $_SESSION['flash'] = compact('type', 'message'); }
    private function pullFlash(): ?array { $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f; }
}