<?php
declare(strict_types=1);

class ProfileController extends Controller
{
    private UserModel $users;
    private PostModel $posts;

    public function __construct()
    {
        $this->users = new UserModel();
        $this->posts = new PostModel();
    }

    public function show(string $username): void
    {
        $me   = $this->requireAuth();
        $user = $this->users->findByUsername($username);
        if (!$user) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'User not found']);
            return;
        }
        $per  = PostController::PER_PAGE;
        $rows = $this->posts->feed($me, $per + 1, 0, 'latest', (int)$user['id']);

        $this->view('profile/show', [
            'title'      => $user['full_name'] . ' (@' . $user['username'] . ')',
            'profile'    => $user,
            'isOwner'    => (int)$user['id'] === $me,
            'postCount'  => $this->posts->countByUser((int)$user['id']),
            'posts'      => array_slice($rows, 0, $per),
            'hasMore'    => count($rows) > $per,
            'perPage'    => $per,
        ]);
    }

    public function edit(): void
    {
        $this->requireAuth();
        $this->view('profile/edit', [
            'title'  => 'Edit profile',
            'errors' => $_SESSION['errors'] ?? [],
            'old'    => $_SESSION['old'] ?? [],
        ]);
        unset($_SESSION['errors'], $_SESSION['old']);
    }

    public function update(): void
    {
        $me = $this->requireAuth();
        $this->verifyCsrf();
        $current = $this->users->findById($me);

        $fullName = trim((string)($_POST['full_name'] ?? ''));
        $bio      = trim((string)($_POST['bio'] ?? ''));
        $errors   = [];

        if ($fullName === '' || mb_strlen($fullName) > 100) $errors['full_name'] = 'Full name is required (max 100 characters).';
        if (mb_strlen($bio) > 300) $errors['bio'] = 'Bio must be 300 characters or fewer.';

        $newImage = null;
        if (!$errors) {
            [$newImage, $imgErr] = Uploader::save($_FILES['profile_image'] ?? null, 'profiles');
            if ($imgErr) $errors['profile_image'] = $imgErr;
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old']    = ['full_name' => $fullName, 'bio' => $bio];
            $this->redirect('/profile/edit');
        }

        $clear = !empty($_POST['remove_image']) && $newImage === null;
        $this->users->updateProfile($me, $fullName, $bio === '' ? null : $bio, $newImage, $clear);
        if ($newImage !== null || $clear) Uploader::delete($current['profile_image'], 'profiles');

        $this->flash('success', 'Profile updated.');
        $this->redirect('/profile/' . rawurlencode($current['username']));
    }
}