<?php
declare(strict_types=1);

class LikeController extends Controller
{
    public function toggle(string $id): void
    {
        $me = $this->requireAuth(true);
        $this->verifyCsrf(true);

        $postId = (int)$id;
        if (!(new PostModel())->exists($postId)) {
            $this->json(['ok' => false, 'message' => 'Post not found.'], 404);
        }
        $likes = new LikeModel();
        $liked = $likes->toggle($postId, $me);
        $this->json(['ok' => true, 'liked' => $liked, 'count' => $likes->count($postId)]);
    }
}