<?php
declare(strict_types=1);

class AuthController extends Controller
{
    private UserModel $users;

    public function __construct() { $this->users = new UserModel(); }

    public function showLogin(): void
    {
        $this->requireGuest();
        $this->view('auth/login', [
            'title'  => 'Log in',
            'errors' => $_SESSION['errors'] ?? [],
            'old'    => $_SESSION['old'] ?? [],
        ]);
        unset($_SESSION['errors'], $_SESSION['old']);
    }

    public function login(): void
    {
        $this->requireGuest();
        $this->verifyCsrf();

        $identifier = trim((string)($_POST['identifier'] ?? ''));
        $password   = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

        $errors = [];
        if ($identifier === '') $errors['identifier'] = 'Enter your username or email.';
        if ($password === '')   $errors['password']   = 'Enter your password.';
        if ($errors) $this->back('/login', $errors, ['identifier' => $identifier]);

        $user = $this->users->findByLogin($identifier);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->back('/login', ['login' => 'Incorrect username/email or password.'], ['identifier' => $identifier]);
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        unset($_SESSION['csrf']);

        $this->redirect('/');
    }

    public function showRegister(): void
    {
        $this->requireGuest();
        $this->view('auth/register', [
            'title'  => 'Create account',
            'errors' => $_SESSION['errors'] ?? [],
            'old'    => $_SESSION['old'] ?? [],
        ]);
        unset($_SESSION['errors'], $_SESSION['old']);
    }

    public function register(): void
    {
        $this->requireGuest();
        $this->verifyCsrf();

        $fullName = trim((string)($_POST['full_name'] ?? ''));
        $username = trim((string)($_POST['username'] ?? ''));
        $email    = strtolower(trim((string)($_POST['email'] ?? '')));
        $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
        $confirm  = is_string($_POST['confirm_password'] ?? null) ? $_POST['confirm_password'] : '';
        $old      = ['full_name' => $fullName, 'username' => $username, 'email' => $email];

        $errors = [];

        if ($fullName === '' || mb_strlen($fullName) > 100) {
            $errors['full_name'] = 'Full name is required (max 100 characters).';
        }

        if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) {
            $errors['username'] = 'Username must be 3-30 characters: letters, numbers, underscore.';
        } elseif ($this->users->usernameExists($username)) {
            $errors['username'] = 'That username is taken.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
            $errors['email'] = 'Enter a valid email address.';
        } elseif ($this->users->emailExists($email)) {
            $errors['email'] = 'That email is already registered.';
        }

        if (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters.';
        }
        if ($confirm !== $password) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }

        if ($errors) $this->back('/register', $errors, $old);

        try {
            $this->users->create($username, $email, password_hash($password, PASSWORD_DEFAULT), $fullName);
        } catch (PDOException $e) {

            if ($e->getCode() === '23000') {
                $this->back('/register', ['username' => 'Username or email is already in use.'], $old);
            }
            throw $e;
        }

        $this->flash('success', 'Account created. You can log in now.');
        $this->redirect('/login');
    }

    public function logout(): void
    {
        $this->verifyCsrf();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        session_start();
        $this->flash('success', 'You have been logged out.');
        $this->redirect('/login');
    }

    private function back(string $path, array $errors, array $old): never
    {
        $_SESSION['errors'] = $errors;
        $_SESSION['old']    = $old;
        $this->redirect($path);
    }
}