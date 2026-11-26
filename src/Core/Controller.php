<?php

namespace App\Core;

abstract class Controller {
    /**
     * Render a view file with layout
     * 
     * @param string $view Relative view name (e.g. 'pages/home', 'auth/login')
     * @param array $data Data to extract into the view
     * @param bool $withLayout Whether to wrap with header/footer layouts
     */
    protected function view(string $view, array $data = [], bool $withLayout = true): void {
        $viewFile = dirname(__DIR__, 2) . '/views/' . ltrim($view, '/') . '.php';

        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "View file not found: views/{$view}.php";
            return;
        }

        // Extract variables to view scope
        extract($data);

        if ($withLayout) {
            $headerFile = dirname(__DIR__, 2) . '/views/layouts/header.php';
            $footerFile = dirname(__DIR__, 2) . '/views/layouts/footer.php';

            if (file_exists($headerFile)) {
                require $headerFile;
            }

            require $viewFile;

            if (file_exists($footerFile)) {
                require $footerFile;
            }
        } else {
            require $viewFile;
        }
    }

    /**
     * Check whether the current request expects a JSON/AJAX response
     */
    protected function isAjax(): bool {
        $xhr = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        return strtolower($xhr) === 'xmlhttprequest' || str_contains($accept, 'application/json');
    }

    /**
     * Return JSON response
     */
    protected function json(array $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Redirect to relative route
     */
    protected function redirect(string $path): void {
        redirect($path);
    }
}
