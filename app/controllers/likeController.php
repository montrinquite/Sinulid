<?php

class LikeController extends Controller
{
    private LikeModel $likes;
    private PostModel $posts;

    public function __construct()
    {
        $this->likes = new LikeModel();
        $this->posts = new PostModel();
    }

    /** POST /posts/{id}/like : like the post, or unlike it if already liked */
    public function likes(int $postId): void
    {
        $user = $this->requireAuth();
        $this->verifyCsrf();

        if ($this->posts->getOwnerId($postId) === null) {
            $this->abort(404, 'Post not found.');
        }

        if ($this->likes->hasLiked($postId, $user['id'])) {
            $this->likes->remove($postId, $user['id']);
        } else {
            $this->likes->add($postId, $user['id']);
        }

        // Back to the feed, profile or post page the click came from.
        $this->redirectBack("/posts/$postId");
    }
}