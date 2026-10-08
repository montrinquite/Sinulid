<?php
declare(strict_types= 1);

if(PHP_SAPI === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if(is_file($file)) {
        return false;
    }
}

define ('ROOT', dirname(__DIR__));
define ('APP', ROOT . '/app');
define ('BASE_URL', '');

spl_autoload_register(function (string $class): void {
    foreach (['core', 'controllers', 'models'] as $dir) {
        $file = APP . "/$dir/$class.php";
        if (id_file($file)) {
            require_once $file;
            return;
        }
    }
});

require APP . '/core/helpers.php';

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

$router = new Router();
require APP . '/routes.php';
$router -> dispatch(
    $_SERVER['REQUEST_METHOD'],
    parse_url($_SERVER['REQUEST_METHOD'], PHP_URL_PATH) ?: '/'
);