<?php

class ProfileController extends Controller
{
    private const PER_PAGE = 10;

    private UserModel $users;
    private PostModel $posts;
    private LikeModel $likes;

    public function __construct()
    {
        $this->users = new UserModel();
        $this->posts = new PostModel();
        $this->likes = new LikeModel();
    }

    /** GET /profile/{id} : public profile with the user's posts */
    public function showProf(int $id): void
    {
        $profile = $this->users->findById($id);
        if ($profile === null) {
            $this->abort(404, 'User not found.');
        }

        $total = $this->posts->countByUser($id);
        $pages = max(1, (int) ceil($total / self::PER_PAGE));
        $page  = min($this->page(), $pages);
        $posts = $this->posts->getByUser($id, self::PER_PAGE, ($page - 1) * self::PER_PAGE);

        $me = auth_user();

        $this->render('profile/show', [
            'profile'   => $profile,
            'posts'     => $posts,
            'postCount' => $total,
            'likedIds'  => $me && $posts
                ? $this->likes->likedPostIds($me['id'], array_column($posts, 'id'))
                : [],
            'isOwner'   => $me !== null && $me['id'] === $id,
            'page'      => $page,
            'pages'     => $pages,
        ], $profile['username']);
    }

    /** GET /profile/edit */
    public function profEdit(): void
    {
        $me = $this->requireAuth();
        $this->render('profile/edit', ['profile' => $this->users->findById($me['id'])], 'Edit profile');
    }

    /** POST /profile/edit : change username and email */
    public function profUpd(): void
    {
        $me = $this->requireAuth();
        $this->verifyCsrf();

        $username = $this->input('username');
        $email    = $this->input('email');
        $errors   = [];

        if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) {
            $errors['username'] = 'Username must be 3-30 letters, numbers or underscores.';
        } else {
            $other = $this->users->findByUsername($username);
            if ($other !== null && (int) $other['id'] !== $me['id']) {
                $errors['username'] = 'That username is already taken.';
            }
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        } else {
            $other = $this->users->findByEmail($email);
            if ($other !== null && (int) $other['id'] !== $me['id']) {
                $errors['email'] = 'That email is already registered.';
            }
        }

        if ($errors) {
            $this->backWithErrors('/profile/edit', $errors, ['username' => $username, 'email' => $email]);
        }

        $this->users->update($me['id'], $username, $email);
        $_SESSION['username'] = $username; // keep the nav bar in sync

        $this->flash('success', 'Profile updated.');
        $this->redirect('/profile/' . $me['id']);
    }

    /** GET /profile/password */
    public function changePass(): void
    {
        $this->requireAuth();
        $this->render('profile/password', [], 'Change password');
    }

    /** POST /profile/password */
    public function updPass(): void
    {
        $me = $this->requireAuth();
        $this->verifyCsrf();

        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        $current = is_string($current) ? $current : '';
        $new     = is_string($new) ? $new : '';
        $confirm = is_string($confirm) ? $confirm : '';

        $user   = $this->users->findWithHash($me['id']);
        $errors = [];

        if ($user === null || !password_verify($current, $user['password_hash'])) {
            $errors['current_password'] = 'Your current password is incorrect.';
        }
        if (strlen($new) < 8) {
            $errors['new_password'] = 'New password must be at least 8 characters.';
        }
        if ($new !== $confirm) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }

        if ($errors) {
            $this->backWithErrors('/profile/password', $errors);
        }

        $this->users->updatePassword($me['id'], password_hash($new, PASSWORD_DEFAULT));
        session_regenerate_id(true);

        $this->flash('success', 'Password changed.');
        $this->redirect('/profile/' . $me['id']);
    }
}