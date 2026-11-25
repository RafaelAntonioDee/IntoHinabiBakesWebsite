<?php

// Ensure session is started safely
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

/**
 * Load environment variables from .env file
 */
function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$key, $val] = explode('=', $line, 2);
            $key = trim($key);
            $val = trim($val);
            // Remove wrapping quotes if present
            if ((str_starts_with($val, '"') && str_ends_with($val, '"')) ||
                (str_starts_with($val, "'") && str_ends_with($val, "'"))) {
                $val = substr($val, 1, -1);
            }
            if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
                putenv("{$key}={$val}");
                $_ENV[$key] = $val;
                $_SERVER[$key] = $val;
            }
        }
    }
}

// Load .env from project root
loadEnv(dirname(__DIR__) . '/.env');

/**
 * Get environment variable with fallback
 */
function env($key, $default = null) {
    $value = getenv($key);
    if ($value === false) {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
    if ($value === 'true' || $value === '(true)') return true;
    if ($value === 'false' || $value === '(false)') return false;
    if ($value === 'null' || $value === '(null)') return null;
    return $value;
}

/**
 * PSR-4 Autoloader for "App\" namespace
 */
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = dirname(__DIR__) . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

/**
 * Calculate dynamic application base path for flexible deployment
 * (Works both under http://localhost/subdir/... and standalone domain)
 */
function getBasePath() {
    static $basePath = null;
    if ($basePath !== null) {
        return $basePath;
    }

    $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
    $scriptDir = str_replace('\\', '/', $scriptDir);
    $trimmed = trim($scriptDir, '/.');
    $basePath = ($trimmed === '') ? '' : '/' . $trimmed;
    return $basePath;
}

/**
 * Generate application URL
 */
function url($path = '') {
    $base = getBasePath();
    $path = '/' . ltrim($path, '/');
    return ($path === '/') ? ($base ?: '/') : ($base . $path);
}

/**
 * Generate asset URL
 */
function asset($path = '') {
    $base = getBasePath();
    return $base . '/' . ltrim($path, '/');
}

/**
 * Get current normalized route path relative to application root
 */
function currentRoute() {
    if (!empty($_GET['route'])) {
        $trimmed = trim($_GET['route'], '/');
        return $trimmed === '' ? '/' : '/' . $trimmed;
    }
    if (!empty($_GET['page'])) {
        $trimmed = trim($_GET['page'], '/');
        return $trimmed === '' ? '/' : '/' . $trimmed;
    }

    $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $baseDir = getBasePath();

    if ($baseDir !== '' && str_starts_with($requestUri, $baseDir)) {
        $requestUri = substr($requestUri, strlen($baseDir));
    }

    if (str_starts_with($requestUri, '/index.php')) {
        $requestUri = substr($requestUri, strlen('/index.php'));
    }

    $trimmed = trim($requestUri, '/');
    return $trimmed === '' ? '/' : '/' . $trimmed;
}

/**
 * Safe string escaping
 */
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to an application route
 */
function redirect($path) {
    header('Location: ' . url($path));
    exit;
}

/**
 * Set or retrieve flash messages
 */
function flash($key, $message = null) {
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return;
    }

    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }

    return null;
}

/**
 * Retrieve old input from session (if any)
 */
function old($key, $default = '') {
    return $_SESSION['old_input'][$key] ?? $default;
}
