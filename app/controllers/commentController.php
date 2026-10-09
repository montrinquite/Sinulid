<?php

require_once __DIR__ . '/../models/Comment.php';

class CommentController
{
    private $commentModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->commentModel = new Comment();
    }

    /** GET /post/{id}/comments : show all comments of a post */
    public function index($postId)
    {
        $comments = $this->commentModel->getByPost((int) $postId);
        $this->respond(['comments' => $comments]);
    }

    /** POST /post/{id}/comments : add a comment */
    public function store($postId)
    {
        $userId = $this->requireLogin();
        $body   = trim($_POST['body'] ?? '');

        if ($body === '') {
            return $this->respond(['error' => 'Comment cannot be empty.'], 422);
        }
        if (mb_strlen($body) > 500) {
            return $this->respond(['error' => 'Comment is too long (max 500).'], 422);
        }

        $id = $this->commentModel->create((int) $postId, $userId, $body);

        $this->respond(['message' => 'Comment added.', 'id' => $id], 201);
    }

    /** POST /comment/{id}/update : edit own comment */
    public function update($commentId)
    {
        $userId  = $this->requireLogin();
        $comment = $this->commentModel->find((int) $commentId);

        if (!$comment) {
            return $this->respond(['error' => 'Comment not found.'], 404);
        }
        if ((int) $comment['user_id'] !== $userId) {
            return $this->respond(['error' => 'Not allowed.'], 403);
        }

        $body = trim($_POST['body'] ?? '');
        if ($body === '' || mb_strlen($body) > 500) {
            return $this->respond(['error' => 'Invalid comment text.'], 422);
        }

        $this->commentModel->update((int) $commentId, $body);
        $this->respond(['message' => 'Comment updated.']);
    }

    /** POST /comment/{id}/delete : delete own comment */
    public function delete($commentId)
    {
        $userId  = $this->requireLogin();
        $comment = $this->commentModel->find((int) $commentId);

        if (!$comment) {
            return $this->respond(['error' => 'Comment not found.'], 404);
        }
        if ((int) $comment['user_id'] !== $userId) {
            return $this->respond(['error' => 'Not allowed.'], 403);
        }

        $this->commentModel->delete((int) $commentId);
        $this->respond(['message' => 'Comment deleted.']);
    }

    /* ---------- helpers ---------- */

    /** Returns the logged-in user's id or stops with 401. */
    private function requireLogin()
    {
        if (empty($_SESSION['user_id'])) {
            $this->respond(['error' => 'Please log in first.'], 401);
            exit;
        }
        return (int) $_SESSION['user_id'];
    }

    /** Sends a JSON response. Swap for a view() call if you render pages. */
    private function respond(array $data, int $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}