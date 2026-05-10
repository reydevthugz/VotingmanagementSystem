<?php
namespace App\Controllers;

use App\Models\User;

class AuthController extends BaseController
{
    public function loginForm(): void
    {
        $this->render('auth/login', [
            'pageTitle' => 'Login',
            'errors' => flash('_errors') ?? [],
            'old' => flash('_old') ?? [],
            'notice' => flash(),
        ]);
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $errors = [];

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if ($password === '') {
            $errors['password'] = 'Password is required.';
        }

        if (!empty($errors)) {
            $this->backWithErrors('/login', $errors, ['email' => $email]);
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user) {
            $this->backWithErrors('/login', ['auth' => 'Invalid email or password.'], ['email' => $email]);
        }

        $storedPassword = (string) $user['password'];
        $isValidPassword = false;
        $passwordInfo = password_get_info($storedPassword);

        if (($passwordInfo['algo'] ?? null) !== null && ($passwordInfo['algo'] ?? 0) !== 0) {
            $isValidPassword = password_verify($password, $storedPassword);
        } elseif (hash_equals($storedPassword, $password)) {
            // Backward-compatible fallback: upgrade legacy plaintext password to bcrypt.
            $isValidPassword = true;
            $userModel->updatePasswordHash((int) $user['user_id'], password_hash($password, PASSWORD_BCRYPT));
        }

        if (!$isValidPassword) {
            $this->backWithErrors('/login', ['auth' => 'Invalid email or password.'], ['email' => $email]);
        }

        if (($user['status'] ?? 'active') !== 'active') {
            $this->backWithErrors('/login', ['auth' => 'Your account is inactive. Please contact administrator.'], ['email' => $email]);
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int) $user['user_id'],
            'name' => $user['fullname'],
            'email' => $user['email'],
            'role' => $user['role'],
            'agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        ];
        $this->recordActivity('user_login', ['user_id' => (int) $user['user_id'], 'role' => $user['role']]);

        if ($user['role'] === 'student') {
            $this->redirect('/student/dashboard');
        }

        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
        }
        $this->recordActivity('user_logout', ['user_id' => $_SESSION['user']['id'] ?? null]);
        session_destroy();
        session_start();
        $this->flash('success', 'You have been logged out.');
        $this->redirect('/login');
    }
}
