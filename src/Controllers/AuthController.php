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
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => $genericError], 400);
            }
            flash('error', $genericError);
            $this->redirect('/login');
            return;
        }

        try {
            $userModel = new User();
            $user = $userModel->findByEmail($email);

            if (!$user || !password_verify($password, $user['password'])) {
                if ($this->isAjax()) {
                    $this->json(['success' => false, 'error' => $genericError], 401);
                }
                flash('error', $genericError);
                $this->redirect('/login');
                return;
            }

            // Success: log user into session with their role
            $roleId = (int)($user['role_id'] ?? 1);
            $roleName = $user['role_name'] ?? 'Customer';
            Session::login($user['id'], $user['full_name'], $roleId, $roleName);

            if ($this->isAjax()) {
                $this->json([
                    'success'  => true,
                    'redirect' => url($this->getRedirectPathForRole()),
                ]);
            }

            // Smart RBAC redirection
            $this->redirectBasedOnRole();
        } catch (\Throwable $e) {
            $errorMsg = env('APP_DEBUG', false) ? ('Database error: ' . $e->getMessage()) : $genericError;
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => $errorMsg], 500);
            }
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

        $errors = [];

        if ($fullName === '') {
            $errors['full_name'] = 'Full name is required.';
        }

        if ($email === '') {
            $errors['email'] = 'Email address is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        }

        if ($phoneNumber === '') {
            $errors['phone_number'] = 'Phone number is required.';
        } elseif (!User::isValidPhoneNumber($phoneNumber)) {
            $errors['phone_number'] = 'Enter a valid 11-digit PH mobile (e.g. 09171234567) or landline.';
        }

        if ($password === '') {
            $errors['password'] = 'Password is required.';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'Password must be at least 6 characters.';
        }

        try {
            $userModel = new User();

            // Validate email uniqueness alongside field checks
            if ($email !== '' && !isset($errors['email'])) {
                $existing = $userModel->findByEmail($email);
                if ($existing) {
                    $errors['email'] = 'Email is already registered.';
                }
            }

            if (!empty($errors)) {
                if ($this->isAjax()) {
                    $this->json([
                        'success' => false,
                        'errors'  => $errors,
                        'error'   => reset($errors),
                    ], 400);
                }
                flash('error', implode(' ', $errors));
                $this->redirect('/register');
                return;
            }

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            // Default role is 1 (Customer)
            $created = $userModel->create($fullName, $email, $hashedPassword, 1, $phoneNumber);

            if ($created) {
                if ($this->isAjax()) {
                    flash('success', 'Account created successfully! You can now log in.');
                    $this->json([
                        'success'  => true,
                        'message'  => 'Account created successfully! Redirecting to login...',
                        'redirect' => url('/login'),
                    ]);
                }
                flash('success', 'Account created successfully! You can now log in.');
                $this->redirect('/login');
                return;
            }

            $err = 'Failed to create account. Please try again.';
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => $err], 500);
            }
            flash('error', $err);
            $this->redirect('/register');
        } catch (\Throwable $e) {
            $err = 'Registration error: ' . $e->getMessage();
            if ($this->isAjax()) {
                $this->json(['success' => false, 'error' => $err], 500);
            }
            flash('error', $err);
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

    private function getRedirectPathForRole(): string {
        if (Session::isAdmin()) {
            return '/admin';
        } elseif (Session::isBaker()) {
            return '/baker';
        }
        return '/';
    }

    /**
     * Helper to route users to the appropriate portal according to their RBAC role
     */
    private function redirectBasedOnRole(): void {
        $this->redirect($this->getRedirectPathForRole());
    }
}
