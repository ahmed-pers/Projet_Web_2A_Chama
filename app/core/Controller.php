<?php

abstract class Controller
{
    protected function render(string $view, array $data = [], string $title = ''): void
    {
        extract($data);

        ob_start();
        require __DIR__ . '/../views/' . $view . '.php';
        $content = ob_get_clean();

        $flash = $this->takeFlash();
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

    protected function flash(string $message): void
    {
        $_SESSION['flash'] = $message;
    }

    protected function takeFlash(): string
    {
        $message = $_SESSION['flash'] ?? '';
        unset($_SESSION['flash']);
        return $message;
    }
}
