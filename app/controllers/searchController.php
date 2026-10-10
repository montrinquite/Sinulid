<?php
declare(strict_types=1);

class SearchController extends Controller
{
    public function index(): void
    {
        $me = $this->requireAuth();
        $q  = trim((string)($_GET['q'] ?? ''));
        $q  = mb_substr($q, 0, 100);

        $users = $posts = [];
        if ($q !== '') {
            $users = (new UserModel())->search(ltrim($q, '@'), 20);
            $posts = (new PostModel())->feed($me, 20, 0, 'latest', null, $q);
        }
        $this->view('search/index', [
            'title' => $q === '' ? 'Search' : 'Search: ' . $q,
            'q'     => $q,
            'users' => $users,
            'posts' => $posts,
        ]);
    }
}