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
            $this->redirectBasedOnRole();
            return;
        }

        $this->view('auth/login', [
            'pageTitle' => 'Login - Sweet Cookie Oven',
        ]);
    }

    /**
     * Process login submission with Smart Role Redirection
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

            // Success: log user into session with their role
            $roleId = (int)($user['role_id'] ?? 1);
            $roleName = $user['role_name'] ?? 'Customer';
            Session::login($user['id'], $user['full_name'], $roleId, $roleName);

            // Smart RBAC redirection
            $this->redirectBasedOnRole();
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
            $this->redirectBasedOnRole();
            return;
        }

        $this->view('auth/register', [
            'pageTitle' => 'Sign Up - Sweet Cookie Oven',
        ]);
    }

    /**
     * Process registration submission (Default role: Customer)
     */
    public function register(): void {
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phoneNumber = trim($_POST['phone_number'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($fullName) || empty($email) || empty($phoneNumber) || empty($password)) {
            flash('error', 'All fields are required.');
            $this->redirect('/register');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Please enter a valid email address.');
            $this->redirect('/register');
            return;
        }

        if (!User::isValidPhoneNumber($phoneNumber)) {
            flash('error', 'Please enter a valid PH mobile (11 digits, e.g. 09171234567 or +639171234567) or landline number.');
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
            // Default role is 1 (Customer)
            $created = $userModel->create($fullName, $email, $hashedPassword, 1, $phoneNumber);

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

    /**
     * Helper to route users to the appropriate portal according to their RBAC role
     */
    private function redirectBasedOnRole(): void {
        if (Session::isAdmin()) {
            $this->redirect('/admin');
        } elseif (Session::isBaker()) {
            $this->redirect('/baker');
        } else {
            $this->redirect('/');
        }
    }
}
