<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\User;

class AuthController extends Controller {
    /**
     * Show login form
     */
    public function showLogin(): void {
        if (Session::check()) {
            $this->redirect('/profile');
            return;
        }

        $this->view('auth/login', [
            'pageTitle' => 'Login - Sweet Cookie Oven',
        ]);
    }

    /**
     * Process login submission
     */
    public function login(): void {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Generic error message to prevent user enumeration
        $genericError = 'Invalid email or password.';

        if (empty($email) || empty($password)) {
            flash('error', $genericError);
            $this->redirect('/login');
            return;
        }

        try {
            $userModel = new User();
            $user = $userModel->findByEmail($email);

            if (!$user || !password_verify($password, $user['password'])) {
                flash('error', $genericError);
                $this->redirect('/login');
                return;
            }

            // Success: log user into session
            Session::login($user['id'], $user['full_name']);
            flash('success', 'Welcome back, ' . htmlspecialchars($user['full_name']) . '!');
            $this->redirect('/');
        } catch (\Throwable $e) {
            $errorMsg = env('APP_DEBUG', false) ? ('Database error: ' . $e->getMessage()) : $genericError;
            flash('error', $errorMsg);
            $this->redirect('/login');
        }
    }

    /**
     * Show registration form
     */
    public function showRegister(): void {
        if (Session::check()) {
            $this->redirect('/profile');
            return;
        }

        $this->view('auth/register', [
            'pageTitle' => 'Sign Up - Sweet Cookie Oven',
        ]);
    }

    /**
     * Process registration submission
     */
    public function register(): void {
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($fullName) || empty($email) || empty($password)) {
            flash('error', 'All fields are required.');
            $this->redirect('/register');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Please enter a valid email address.');
            $this->redirect('/register');
            return;
        }

        if (strlen($password) < 6) {
            flash('error', 'Password must be at least 6 characters long.');
            $this->redirect('/register');
            return;
        }

        try {
            $userModel = new User();

            // Check if email already registered
            $existing = $userModel->findByEmail($email);
            if ($existing) {
                flash('error', 'Email is already registered. Please login.');
                $this->redirect('/register');
                return;
            }

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $created = $userModel->create($fullName, $email, $hashedPassword);

            if ($created) {
                flash('success', 'Account created successfully! You can now log in.');
                $this->redirect('/login');
                return;
            }

            flash('error', 'Failed to create account. Please try again.');
            $this->redirect('/register');
        } catch (\Throwable $e) {
            flash('error', 'Registration error: ' . $e->getMessage());
            $this->redirect('/register');
        }
    }

    /**
     * Process logout
     */
    public function logout(): void {
        Session::logout();
        flash('success', 'You have been logged out.');
        $this->redirect('/');
    }
}
