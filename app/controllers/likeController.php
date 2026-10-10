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

        $this->redirectBack("/posts/$postId");
    }
}