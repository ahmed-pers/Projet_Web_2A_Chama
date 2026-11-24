<?php

abstract class Controller
{
    protected function render(string $view, array $data = [], string $title = ''): void
    {
        extract($data);

        ob_start();
        require __DIR__ . '/../views/' . $view . '.php';
        $content = ob_get_clean();

        require __DIR__ . '/../views/layout.php';
    }

    protected function redirect(string $page): void
    {
        header('Location: index.php?page=' . $page);
        exit;
    }

    protected function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    protected function post(string $key, $default = '')
    {
        return $_POST[$key] ?? $default;
    }

    protected function get(string $key, $default = '')
    {
        return $_GET[$key] ?? $default;
    }
}
