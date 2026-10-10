<?php

class CommentController extends Controller
{
    private const MAX_LEN = 500;

    private CommentModel $comments;
    private PostModel $posts;

    public function __construct()
    {
        $this->comments = new CommentModel();
        $this->posts    = new PostModel();
    }

    /** POST /posts/{id}/comments : add a comment */
    public function comment(int $postId): void
    {
        $user = $this->requireAuth();
        $this->verifyCsrf();

        if ($this->posts->getOwnerId($postId) === null) {
            $this->abort(404, 'Post not found.');
        }

        $content = $this->input('content');
        $errors  = $this->validate($content);
        if ($errors) {
            $this->backWithErrors("/posts/$postId", $errors, ['content' => $content]);
        }

        $this->comments->create($postId, $user['id'], $content);
        $this->flash('success', 'Comment added.');
        $this->redirect("/posts/$postId");
    }

    /** GET /comments/{id}/edit */
    public function editComm(int $id): void
    {
        $comment = $this->ownedComment($id);
        $this->render('comments/edit', ['comment' => $comment], 'Edit comment');
    }

    /** POST /comments/{id}/update */
    public function updComm(int $id): void
    {
        $user = $this->requireAuth();
        $this->verifyCsrf();
        $comment = $this->ownedComment($id);

        $content = $this->input('content');
        $errors  = $this->validate($content);
        if ($errors) {
            $this->backWithErrors("/comments/$id/edit", $errors, ['content' => $content]);
        }

        $this->comments->update($id, $user['id'], $content);
        $this->flash('success', 'Comment updated.');
        $this->redirect('/posts/' . $comment['post_id']);
    }

    /** POST /comments/{id}/delete */
    public function delComm(int $id): void
    {
        $user = $this->requireAuth();
        $this->verifyCsrf();
        $comment = $this->ownedComment($id);

        $this->comments->delete($id, $user['id']);
        $this->flash('success', 'Comment deleted.');
        $this->redirect('/posts/' . $comment['post_id']);
    }

    /* ---------- helpers ---------- */

    /** Logged in + the comment exists + it belongs to this user, else stop. */
    private function ownedComment(int $id): array
    {
        $user    = $this->requireAuth();
        $comment = $this->comments->findById($id);

        if ($comment === null) {
            $this->abort(404, 'Comment not found.');
        }
        if ((int) $comment['user_id'] !== $user['id']) {
            $this->abort(403, 'You can only change your own comments.');
        }
        return $comment;
    }

    private function validate(string $content): array
    {
        if ($content === '') {
            return ['content' => 'Comment cannot be empty.'];
        }
        if (mb_strlen($content) > self::MAX_LEN) {
            return ['content' => 'Comment is too long (max ' . self::MAX_LEN . ' characters).'];
        }
        return [];
    }
}