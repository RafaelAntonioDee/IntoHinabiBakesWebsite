<?php

namespace App\Core;

class Session {
    public static function check(): bool {
        return !empty($_SESSION['user_id']);
    }

    public static function id(): ?int {
        return $_SESSION['user_id'] ?? null;
    }

    public static function name(): ?string {
        return $_SESSION['user_name'] ?? null;
    }

    public static function login(int|string $id, string $name): void {
        $_SESSION['user_id'] = $id;
        $_SESSION['user_name'] = $name;
        $_SESSION['just_logged_in'] = true;
    }

    public static function logout(): void {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
}
