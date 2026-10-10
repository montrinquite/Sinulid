<?php
declare(strict_types=1);

class CommentController extends Controller
{
    private CommentModel $comments;

    public function __construct() { $this->comments = new CommentModel(); }

    //GET comments
    public function index(string $postId): void
    {
        $this->requireAuth(true);
        $postId = (int)$postId;
        if (!$this->comments->postExists($postId)) {
            $this->json(['ok' => false, 'message' => 'Post not found.'], 404);
        }
        $rows = array_map([$this, 'present'], $this->comments->getByPost($postId));
        $this->json(['ok' => true, 'comments' => $rows, 'count' => count($rows)]);
    }

    // POST comments
    public function store(string $postId): void
    {
        $userId = $this->requireAuth(true);
        $this->verifyCsrf(true);
        $postId = (int)$postId;

        if (!$this->comments->postExists($postId)) {
            $this->json(['ok' => false, 'message' => 'Post not found.'], 404);
        }
        $content = $this->validContent();

        $id = $this->comments->create($postId, $userId, $content);
        $this->json([
            'ok'      => true,
            'message' => 'Comment added.',
            'comment' => $this->present($this->comments->find($id)),
            'count'   => $this->comments->countByPost($postId),
        ], 201);
    }

    //update
    public function update(string $id): void
    {
        $userId = $this->requireAuth(true);
        $this->verifyCsrf(true);
        $comment = $this->ownedComment((int)$id, $userId);
        $content = $this->validContent();

        $this->comments->update((int)$comment['id'], $content);
        $this->json([
            'ok'      => true,
            'message' => 'Comment updated.',
            'comment' => $this->present($this->comments->find((int)$comment['id'])),
        ]);
    }

    //delete
    public function destroy(string $id): void
    {
        $userId = $this->requireAuth(true);
        $this->verifyCsrf(true);
        $comment = $this->ownedComment((int)$id, $userId);

        $this->comments->delete((int)$comment['id']);
        $this->json([
            'ok'      => true,
            'message' => 'Comment deleted.',
            'count'   => $this->comments->countByPost((int)$comment['post_id']),
        ]);
    }

    //helpers
    private function ownedComment(int $id, int $userId): array
    {
        $comment = $this->comments->find($id);
        if (!$comment) $this->json(['ok' => false, 'message' => 'Comment not found.'], 404);
        if ((int)$comment['user_id'] !== $userId) {
            $this->json(['ok' => false, 'message' => 'You can only change your own comments.'], 403);
        }
        return $comment;
    }

    private function validContent(): string
    {
        $content = trim((string)($_POST['content'] ?? ''));
        if ($content === '') {
            $this->json(['ok' => false, 'message' => 'Comment cannot be empty.'], 422);
        }
        if (mb_strlen($content) > 500) {
            $this->json(['ok' => false, 'message' => 'Comment is too long (max 500 characters).'], 422);
        }
        return $content;
    }

    private function present(array $c): array
    {
        return [
            'id'         => (int)$c['id'],
            'post_id'    => (int)$c['post_id'],
            'content'    => $c['content'],
            'username'   => $c['username'],
            'full_name'  => $c['full_name'],
            'avatar'     => upload_url($c['profile_image'], 'profiles'),
            'profile_url'=> url('/profile/' . rawurlencode($c['username'])),
            'time'       => time_ago($c['created_at']),
            'edited'     => strtotime($c['updated_at']) - strtotime($c['created_at']) > 1,
            'is_mine'    => (int)$c['user_id'] === $this->userId(),
        ];
    }
}