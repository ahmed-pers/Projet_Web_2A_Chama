<?php

require_once __DIR__ . '/config.php';

spl_autoload_register(function ($class) {
    $directories = [
        __DIR__ . '/../app/core/',
        __DIR__ . '/../app/models/',
        __DIR__ . '/../app/controllers/',
    ];

    foreach ($directories as $directory) {
        $file = $directory . $class . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});
