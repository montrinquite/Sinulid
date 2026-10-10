<?php
declare(strict_types=1);

class PostController extends Controller
{
    public const PER_PAGE = 10;
    public const MAX_LEN  = 1000;

    private PostModel $posts;
    public function __construct() { $this->posts = new PostModel(); }

    public function index(): void
    {
        $me   = $this->requireAuth();
        $sort = ($_GET['sort'] ?? 'latest') === 'top' ? 'top' : 'latest';
        $rows = $this->posts->feed($me, self::PER_PAGE + 1, 0, $sort);
        $hasMore = count($rows) > self::PER_PAGE;

        $this->view('feed/index', [
            'title'   => 'Home',
            'posts'   => array_slice($rows, 0, self::PER_PAGE),
            'hasMore' => $hasMore,
            'sort'    => $sort,
            'perPage' => self::PER_PAGE,
        ]);
    }

    public function load(): void
    {
        $me     = $this->requireAuth(true);
        $offset = max(0, (int)($_GET['offset'] ?? 0));
        $sort   = ($_GET['sort'] ?? 'latest') === 'top' ? 'top' : 'latest';
        $author = null;
        if (!empty($_GET['user']) && is_string($_GET['user'])) {
            $u = (new UserModel())->findByUsername($_GET['user']);
            if (!$u) $this->json(['ok' => false, 'message' => 'User not found.'], 404);
            $author = (int)$u['id'];
        }
        $keyword = isset($_GET['q']) && is_string($_GET['q']) ? trim($_GET['q']) : null;

        $rows = $this->posts->feed($me, self::PER_PAGE + 1, $offset, $sort, $author, $keyword);
        $hasMore = count($rows) > self::PER_PAGE;
        $html = '';
        foreach (array_slice($rows, 0, self::PER_PAGE) as $post) {
            $html .= $this->partial('feed/_post', ['post' => $post]);
        }
        $this->json(['ok' => true, 'html' => $html, 'hasMore' => $hasMore, 'next' => $offset + self::PER_PAGE]);
    }

    //store
    public function store(): void
    {
        $me = $this->requireAuth($this->isAjax());
        $this->verifyCsrf($this->isAjax());

        $content = trim((string)($_POST['content'] ?? ''));
        $error = $this->validateContent($content);
        $image = null;

        if (!$error) {
            [$image, $error] = Uploader::save($_FILES['image'] ?? null, 'posts');
        }
        if ($error) $this->fail($error, '/');

        $id = $this->posts->create($me, $content, $image);

        if ($this->isAjax()) {
            $post = $this->posts->find($id, $me);
            $this->json(['ok' => true, 'message' => 'Post published.', 'html' => $this->partial('feed/_post', ['post' => $post])], 201);
        }
        $this->flash('success', 'Post published.');
        $this->redirect('/');
    }

    //edit
    public function edit(string $id): void
    {
        $me   = $this->requireAuth();
        $post = $this->ownedPost((int)$id, $me, false);
        $this->view('posts/edit', [
            'title'  => 'Edit post',
            'post'   => $post,
            'errors' => $_SESSION['errors'] ?? [],
        ]);
        unset($_SESSION['errors']);
    }

    //update
    public function update(string $id): void
    {
        $ajax = $this->isAjax();
        $me   = $this->requireAuth($ajax);
        $this->verifyCsrf($ajax);
        $post = $this->ownedPost((int)$id, $me, $ajax);

        $content = trim((string)($_POST['content'] ?? ''));
        $error = $this->validateContent($content);
        $newImage = null;
        if (!$error) {
            [$newImage, $error] = Uploader::save($_FILES['image'] ?? null, 'posts');
        }
        if ($error) $this->fail($error, "/posts/{$post['id']}/edit");

        $remove = !empty($_POST['remove_image']) && $newImage === null;
        $this->posts->update((int)$post['id'], $content, $newImage, $remove);
        if ($newImage !== null || $remove) Uploader::delete($post['image'], 'posts');

        if ($ajax) {
            $fresh = $this->posts->find((int)$post['id'], $me);
            $this->json(['ok' => true, 'message' => 'Post updated.', 'html' => $this->partial('feed/_post', ['post' => $fresh])]);
        }
        $this->flash('success', 'Post updated.');
        $this->redirect('/');
    }

    //delete
    public function destroy(string $id): void
    {
        $ajax = $this->isAjax();
        $me   = $this->requireAuth($ajax);
        $this->verifyCsrf($ajax);
        $post = $this->ownedPost((int)$id, $me, $ajax);

        $this->posts->delete((int)$post['id']);
        Uploader::delete($post['image'], 'posts');

        if ($ajax) $this->json(['ok' => true, 'message' => 'Post deleted.']);
        $this->flash('success', 'Post deleted.');
        $this->redirect('/');
    }

    //helpers

    private function ownedPost(int $id, int $me, bool $ajax): array
    {
        $post = $this->posts->find($id, $me);
        if (!$post) {
            if ($ajax) $this->json(['ok' => false, 'message' => 'Post not found.'], 404);
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Post not found']);
            exit;
        }
        if ((int)$post['user_id'] !== $me) {
            if ($ajax) $this->json(['ok' => false, 'message' => 'You can only change your own posts.'], 403);
            $this->flash('error', 'You can only change your own posts.');
            $this->redirect('/');
        }
        return $post;
    }

    private function validateContent(string $content): ?string
    {
        if ($content === '') return 'Post text cannot be empty.';
        if (mb_strlen($content) > self::MAX_LEN) return 'Post is too long (max ' . self::MAX_LEN . ' characters).';
        return null;
    }

    private function fail(string $message, string $redirectTo): never
    {
        if ($this->isAjax()) $this->json(['ok' => false, 'message' => $message], 422);
        $this->flash('error', $message);
        $this->redirect($redirectTo);
    }
}