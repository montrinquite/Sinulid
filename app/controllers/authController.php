<?php

class AuthController extends Controller {
public function showLogin(): void
{
    $this->guestOnly();
    $this->render('auth/login', [], 'Log in');
}

public function login(): void
{
    $this->guestOnly();
    $this->verifyCsrf();

    $email    = strtolower($this->input('email'));
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

    $user = $this->users->findByEmail($email);

    if (!$user || !password_verify($password, $user['password'])) {
        $this->backWithErrors('/login', ['login' => 'Incorrect email or password.'], ['email' => $email]);
    }

    session_regenerate_id(true);
    $_SESSION['user_id']  = (int) $user['id'];
    $_SESSION['username'] = $user['username'];

    $this->redirect('/');
}

public function showReg(): void
{
    $this->guestOnly();
    $this->render('auth/register', [], 'Register');
}

public function register(): void
{
    $this->guestOnly();
    $this->verifyCsrf();

    $username = $this->input('username');
    $email    = strtolower($this->input('email'));
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    $confirm  = is_string($_POST['confirm_password'] ?? null) ? $_POST['confirm_password'] : '';
    $old      = ['username' => $username, 'email' => $email];

    $errors = [];

    if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) {
        $errors['username'] = 'Username must be 3-30 characters: letters, numbers, underscore.';
    } elseif ($this->users->findByUsername($username)) {
        $errors['username'] = 'That username is taken.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    } elseif ($this->users->findByEmail($email)) {
        $errors['email'] = 'That email is already registered.';
    }

    if (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters.';
    }

    if ($confirm !== $password) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    if ($errors) {
        $this->backWithErrors('/register', $errors, $old);
    }

    try {
        $this->users->create($username, $email, $password);
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            $this->backWithErrors('/register', ['username' => 'Username or email is already in use.'], $old);
        }
        throw $e;
    }

    $this->flash('success', 'Account created. You can log in now.');
    $this->redirect('/login');
}
}