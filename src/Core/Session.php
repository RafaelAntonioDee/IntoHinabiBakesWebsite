<?php

namespace App\Core;

class Session {
    /**
     * Check if a user is currently logged in
     */
    public static function check(): bool {
        return !empty($_SESSION['user_id']);
    }

    /**
     * Get logged-in user ID
     */
    public static function id(): ?int {
        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    }

    /**
     * Get logged-in user display name
     */
    public static function name(): ?string {
        return $_SESSION['user_name'] ?? null;
    }

    /**
     * Get logged-in user role ID (1: Customer, 2: Baker, 3: Admin)
     */
    public static function roleId(): int {
        return isset($_SESSION['user_role_id']) ? (int)$_SESSION['user_role_id'] : 1;
    }

    /**
     * Get logged-in user role slug ('customer', 'baker', 'admin')
     */
    public static function role(): string {
        return strtolower($_SESSION['user_role_name'] ?? 'customer');
    }

    /**
     * Get human-readable role name ('Customer', 'Baker', 'Admin')
     */
    public static function roleName(): string {
        return ucfirst(self::role());
    }

    /**
     * Check if user is an Admin
     */
    public static function isAdmin(): bool {
        return self::check() && (self::roleId() === 3 || self::role() === 'admin');
    }

    /**
     * Check if user is a Baker
     */
    public static function isBaker(): bool {
        return self::check() && (self::roleId() === 2 || self::role() === 'baker');
    }

    /**
     * Check if user is Staff (Admin or Baker)
     */
    public static function isStaff(): bool {
        return self::isAdmin() || self::isBaker();
    }

    /**
     * Log in a user and set their session
     */
    public static function login(int|string $id, string $name, int|string $roleId = 1, string $roleName = 'Customer'): void {
        // Prevent session fixation attacks
        if (session_status() === PHP_SESSION_ACTIVE) {
            @session_regenerate_id(true);
        }

        $_SESSION['user_id'] = (int)$id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_role_id'] = (int)$roleId;
        $_SESSION['user_role_name'] = strtolower($roleName);
        $_SESSION['just_logged_in'] = true;
    }

    /**
     * RBAC Route Guard: Require one of the specified roles.
     * Terminate execution and redirect if unauthorized.
     *
     * @param array|string $allowedRoles e.g. ['admin'] or ['baker', 'admin']
     * @param string $redirectFallback Default redirection path
     */
    public static function requireRole(array|string $allowedRoles, string $redirectFallback = '/login'): void {
        $allowed = is_array($allowedRoles) ? array_map('strtolower', $allowedRoles) : [strtolower($allowedRoles)];

        // 1. Not logged in -> redirect to login
        if (!self::check()) {
            flash('error', 'Please log in with an authorized account to continue.');
            header('Location: ' . url($redirectFallback));
            exit;
        }

        $currentRole = self::role();

        // 2. Admins always have access, or check if user's role is in allowed list
        if ($currentRole === 'admin' || in_array($currentRole, $allowed, true)) {
            return;
        }

        // 3. Unauthorized role -> 403 Forbidden
        http_response_code(403);
        flash('error', 'Access Denied: Your account (' . self::roleName() . ') does not have permission to view that page.');

        // Smart bounce based on who they are
        if (self::isBaker()) {
            header('Location: ' . url('/baker'));
        } else {
            header('Location: ' . url('/profile'));
        }
        exit;
    }

    /**
     * Log out current user and destroy session
     */
    public static function logout(): void {
        $_SESSION = [];
        if (ini_get("session.use_cookies") && !headers_sent()) {
            $params = session_get_cookie_params();
            @setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        @session_destroy();
    }
}
