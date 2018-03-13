<?php
// bootstrap.php
define('ROOT', __DIR__ . DIRECTORY_SEPARATOR);
define('SRC', ROOT . 'src' . DIRECTORY_SEPARATOR);

spl_autoload_register(function ($class) {
    $file = SRC . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

require './vendor/autoload.php';
require './php-di/container.php';
require './utils/functions.php';