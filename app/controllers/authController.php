<?php

class AuthController extends Controller
{
    private UserModel $users;

    public function __construct()
    {
        $this->users = new UserModel();
    }

    /** GET /login */
    public function showLogin(): void
    {
        $this->guestOnly();
        $this->render('auth/login', [], 'Log in');
    }

    /** GET /register */
    public function showReg(): void
    {
        $this->guestOnly();
        $this->render('auth/register', [], 'Register');
    }

    /** POST /register */
    public function register(): void
    {
        $this->guestOnly();
        $this->verifyCsrf();

        $username = $this->input('username');
        $email    = $this->input('email');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';
        $password = is_string($password) ? $password : '';
        $confirm  = is_string($confirm) ? $confirm : '';

        $errors = [];

        if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) {
            $errors['username'] = 'Username must be 3-30 letters, numbers or underscores.';
        } elseif ($this->users->usernameExists($username)) {
            $errors['username'] = 'That username is already taken.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        } elseif ($this->users->emailExists($email)) {
            $errors['email'] = 'That email is already registered.';
        }

        if (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters.';
        }
        if ($password !== $confirm) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }

        if ($errors) {
            $this->backWithErrors('/register', $errors, ['username' => $username, 'email' => $email]);
        }

        // The model stores whatever hash it is given, so hash here.
        $this->users->create($username, $email, password_hash($password, PASSWORD_DEFAULT));

        $this->flash('success', 'Account created. You can log in now.');
        $this->redirect('/login');
    }

    /** POST /login */
    public function login(): void
    {
        $this->guestOnly();
        $this->verifyCsrf();

        $email    = $this->input('email');
        $password = $_POST['password'] ?? '';
        $password = is_string($password) ? $password : '';

        $user = $this->users->findByEmail($email);

        // One message for "no such user" and "wrong password" on purpose.
        if ($user === null || !password_verify($password, $user['password_hash'])) {
            $this->backWithErrors('/login', ['login' => 'Invalid email or password.'], ['email' => $email]);
        }

        session_regenerate_id(true); // stops session fixation
        $_SESSION['user_id']  = (int) $user['id'];
        $_SESSION['username'] = $user['username'];

        $this->flash('success', 'Welcome back, ' . $user['username'] . '!');
        $this->redirect('/');
    }

    /** POST /logout */
    public function logout(): void
    {
        $this->verifyCsrf();

        unset($_SESSION['user_id'], $_SESSION['username']);
        session_regenerate_id(true);

        $this->flash('success', 'You have been logged out.');
        $this->redirect('/login');
    }
}