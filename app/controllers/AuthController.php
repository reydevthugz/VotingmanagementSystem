<?php
namespace App\Controllers;

use App\Models\Student;
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

    public function registerForm(): void
    {
        $this->render('auth/register', [
            'pageTitle' => 'Register',
            'errors' => flash('_errors') ?? [],
            'old' => flash('_old') ?? [],
            'notice' => flash(),
        ]);
    }

    public function register(): void
    {
        $studentId = trim($_POST['student_id'] ?? '');
        $fullname = trim($_POST['fullname'] ?? '');
        $year = trim($_POST['year'] ?? '');
        $section = trim($_POST['section'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $passwordConfirmation = (string) ($_POST['password_confirmation'] ?? '');
        $termsAccepted = isset($_POST['terms']);
        $errors = [];

        if ($studentId === '') {
            $errors['student_id'] = 'Student ID is required.';
        }

        if ($fullname === '') {
            $errors['fullname'] = 'Full name is required.';
        }

        if ($year === '') {
            $errors['year'] = 'Please select your year.';
        }

        if ($section === '') {
            $errors['section'] = 'Section is required.';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if ($password === '') {
            $errors['password'] = 'Password is required.';
        }

        if ($passwordConfirmation === '') {
            $errors['password_confirmation'] = 'Please confirm your password.';
        }

        if ($password !== '' && $passwordConfirmation !== '' && $password !== $passwordConfirmation) {
            $errors['password_confirmation'] = 'Passwords do not match.';
        }

        if (!$termsAccepted) {
            $errors['terms'] = 'Please agree to the terms to continue.';
        }

        if (!empty($errors)) {
            $this->backWithErrors('/register', $errors, [
                'student_id' => $studentId,
                'fullname' => $fullname,
                'year' => $year,
                'section' => $section,
                'email' => $email,
            ]);
        }

        $userModel = new User();
        $studentModel = new Student();

        if ($userModel->findByEmail($email)) {
            $this->backWithErrors('/register', ['email' => 'This email is already registered.'], [
                'student_id' => $studentId,
                'fullname' => $fullname,
                'year' => $year,
                'section' => $section,
                'email' => $email,
            ]);
        }

        if ($studentModel->findByEmail($email)) {
            $this->backWithErrors('/register', ['email' => 'This email is already registered.'], [
                'student_id' => $studentId,
                'fullname' => $fullname,
                'year' => $year,
                'section' => $section,
                'email' => $email,
            ]);
        }

        $userModel->create([
            'fullname' => $fullname,
            'username' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role' => 'student',
        ]);

        $studentModel->create([
            'fullname' => $fullname,
            'email' => $email,
            'course' => 'General',
            'year' => $year,
            'section' => $section,
            'password' => $password,
        ]);

        $this->flash('success', 'Registration successful. Please sign in.');
        $this->redirect('/login');
    }

    public function login(): void
    {
        // Accept both email and username/student ID formats
        $credential = trim($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $errors = [];

        // Validate input is not empty (but allow any non-empty string - email, username, or student ID)
        if ($credential === '') {
            $errors['email'] = 'Please enter your email or student ID.';
        }

        if ($password === '') {
            $errors['password'] = 'Password is required.';
        }

        if (!empty($errors)) {
            $this->backWithErrors('/login', $errors, ['email' => $credential]);
        }

        // Query database by username/email credential
        // The users table stores login identifiers in the 'username' column
        $userModel = new User();
        $user = $userModel->findByEmail($credential);

        if (!$user) {
            // Generic error message for security (don't reveal if user exists)
            $this->backWithErrors('/login', ['auth' => 'Invalid email/ID or password.'], ['email' => $credential]);
        }

        $storedPassword = (string) $user['password'];
        $isValidPassword = false;
        $passwordInfo = password_get_info($storedPassword);

        if (($passwordInfo['algo'] ?? null) !== null && ($passwordInfo['algo'] ?? 0) !== 0) {
            $isValidPassword = password_verify($password, $storedPassword);
        } elseif (hash_equals($storedPassword, $password)) {
            // Backward-compatible fallback: upgrade legacy plaintext password to bcrypt.
            $isValidPassword = true;
            $userModel->updatePasswordHash((int) $user['id'], password_hash($password, PASSWORD_BCRYPT));
        }

        if (!$isValidPassword) {
            $this->backWithErrors('/login', ['auth' => 'Invalid email/ID or password.'], ['email' => $credential]);
        }

        if (($user['status'] ?? 'active') !== 'active') {
            $this->backWithErrors('/login', ['auth' => 'Your account is inactive. Please contact administrator.'], ['email' => $credential]);
        }

        session_regenerate_id(true);
        $sessionUser = [
            'id' => (int) $user['id'],
            'name' => $user['fullname'],
            'email' => $user['username'],
            'role' => $user['role'],
            'agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        ];

        if ($user['role'] === 'student') {
            $student = (new Student())->findByEmail((string) $user['username']);
            if ($student) {
                $sessionUser['student_id'] = (int) $student['student_id'];
            }
        }

        $_SESSION['user'] = $sessionUser;
        $this->recordActivity('user_login', ['user_id' => (int) $user['id'], 'role' => $user['role']]);

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
