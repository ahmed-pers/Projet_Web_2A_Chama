<?php

class Controller
{
    // Show a view inside the main layout.
    protected function render($view, $data, $title)
    {
        // $data contains things like $services, $errors, $title...
        extract($data);

        ob_start();
        require __DIR__ . '/../views/' . $view . '.php';
        $content = ob_get_clean();

        require __DIR__ . '/../views/layout.php';
    }

    protected function redirect($page)
    {
        header('Location: index.php?page=' . $page);
        exit;
    }
}
