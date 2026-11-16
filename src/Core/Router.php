<?php

namespace App\Core;

class Router {
    private array $routes = [
        'GET'  => [],
        'POST' => [],
    ];

    /**
     * Register a GET route
     */
    public function get(string $path, array|callable $action): void {
        $this->routes['GET'][$this->normalizePath($path)] = $action;
    }

    /**
     * Register a POST route
     */
    public function post(string $path, array|callable $action): void {
        $this->routes['POST'][$this->normalizePath($path)] = $action;
    }

    /**
     * Dispatch the current request
     */
    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $this->getCurrentUri();

        $action = $this->routes[$method][$uri] ?? null;

        // Fallback: check with or without trailing slash
        if (!$action) {
            $altUri = ($uri === '/') ? '' : (str_ends_with($uri, '/') ? rtrim($uri, '/') : $uri . '/');
            $action = $this->routes[$method][$altUri] ?? null;
        }

        if ($action) {
            $this->executeAction($action);
            return;
        }

        // 404 Not Found
        http_response_code(404);
        $notFoundView = dirname(__DIR__, 2) . '/views/errors/404.php';
        if (file_exists($notFoundView)) {
            require dirname(__DIR__, 2) . '/views/layouts/header.php';
            require $notFoundView;
            require dirname(__DIR__, 2) . '/views/layouts/footer.php';
        } else {
            echo "<h1>404 Not Found</h1><p>The requested route [{$uri}] could not be found.</p>";
        }
    }

    /**
     * Normalize path to standard format (e.g. '/products')
     */
    private function normalizePath(string $path): string {
        $trimmed = trim($path, '/');
        return $trimmed === '' ? '/' : '/' . $trimmed;
    }

    /**
     * Extract current URI relative to base folder or query param
     */
    private function getCurrentUri(): string {
        // Query param fallback (?route=/products or ?page=products)
        if (!empty($_GET['route'])) {
            return $this->normalizePath($_GET['route']);
        }
        if (!empty($_GET['page'])) {
            return $this->normalizePath($_GET['page']);
        }

        $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $baseDir = getBasePath();

        if ($baseDir !== '' && str_starts_with($requestUri, $baseDir)) {
            $requestUri = substr($requestUri, strlen($baseDir));
        }

        // Strip index.php if user navigated directly to index.php
        if (str_starts_with($requestUri, '/index.php')) {
            $requestUri = substr($requestUri, strlen('/index.php'));
        }

        return $this->normalizePath($requestUri ?: '/');
    }

    /**
     * Execute controller action or closure
     */
    private function executeAction(array|callable $action): void {
        if (is_callable($action)) {
            call_user_func($action);
            return;
        }

        if (is_array($action) && count($action) === 2) {
            [$class, $method] = $action;
            if (class_exists($class)) {
                $controller = new $class();
                if (method_exists($controller, $method)) {
                    $controller->$method();
                    return;
                }
            }
        }

        throw new \RuntimeException("Invalid route handler configured.");
    }
}
