<?php

class PostController extends Controller
{
    private const PER_PAGE = 10;
    private const MAX_LEN  = 500;

    private PostModel $posts;
    private CommentModel $comments;
    private LikeModel $likes;

    public function __construct()
    {
        $this->posts    = new PostModel();
        $this->comments = new CommentModel();
        $this->likes    = new LikeModel();
    }

    /** GET / : the feed, newest first */
    public function index(): void
    {
        $total = $this->posts->count();
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page  = min($this->page(), $pages);
        $posts = $this->posts->getAll(self::PER_PAGE, ($page - 1) * self::PER_PAGE);

        $this->render('posts/feeds', [
            'posts'    => $posts,
            'likedIds' => $this->likedIds($posts),
            'page'     => $page,
            'pages'    => $pages,
        ], 'Home');
    }

    /** POST /posts : create a post */
    public function store(): void
    {
        $user = $this->requireAuth();
        $this->verifyCsrf();

        $content = $this->input('content');
        $errors  = $this->validate($content);
        if ($errors) {
            $this->backWithErrors('/', $errors, ['content' => $content]);
        }

        $this->posts->create($user['id'], $content);
        $this->flash('success', 'Post published.');
        $this->redirect('/');
    }

    /** GET /posts/{id} : one post with its comments */
    public function show(int $id): void
    {
        $post = $this->posts->findById($id);
        if ($post === null) {
            $this->abort(404, 'Post not found.');
        }

        $user  = auth_user();
        $liked = $user !== null && $this->likes->hasLiked($id, $user['id']);

        $this->render('posts/show', [
            'post'     => $post,
            'comments' => $this->comments->getByPost($id),
            'liked'    => $liked,
            'likedIds' => $liked ? [$id] : [],
        ], 'Post by ' . $post['username']);
    }

    /** GET /posts/{id}/edit */
    public function editPost(int $id): void
    {
        $post = $this->ownedPost($id);
        $this->render('posts/edit', ['post' => $post], 'Edit post');
    }

    /** POST /posts/{id}/update */
    public function updPost(int $id): void
    {
        $user = $this->requireAuth();
        $this->verifyCsrf();
        $this->ownedPost($id);

        $content = $this->input('content');
        $errors  = $this->validate($content);
        if ($errors) {
            $this->backWithErrors("/posts/$id/edit", $errors, ['content' => $content]);
        }

        $this->posts->update($id, $user['id'], $content);
        $this->flash('success', 'Post updated.');
        $this->redirect("/posts/$id");
    }

    /** POST /posts/{id}/delete */
    public function delPost(int $id): void
    {
        $user = $this->requireAuth();
        $this->verifyCsrf();
        $this->ownedPost($id);

        $this->posts->delete($id, $user['id']);
        $this->flash('success', 'Post deleted.');
        $this->redirect('/');
    }

    /* ---------- helpers ---------- */

    /** Logged in + the post exists + it belongs to this user, else stop. */
    private function ownedPost(int $id): array
    {
        $user = $this->requireAuth();
        $post = $this->posts->findById($id);

        if ($post === null) {
            $this->abort(404, 'Post not found.');
        }
        if ((int) $post['user_id'] !== $user['id']) {
            $this->abort(403, 'You can only change your own posts.');
        }
        return $post;
    }

    private function validate(string $content): array
    {
        if ($content === '') {
            return ['content' => 'Post cannot be empty.'];
        }
        if (mb_strlen($content) > self::MAX_LEN) {
            return ['content' => 'Post is too long (max ' . self::MAX_LEN . ' characters).'];
        }
        return [];
    }

    /** Ids of the given posts that the current user has liked. */
    private function likedIds(array $posts): array
    {
        $user = auth_user();
        if ($user === null || !$posts) {
            return [];
        }
        return $this->likes->likedPostIds($user['id'], array_column($posts, 'id'));
    }
}