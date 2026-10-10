<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'));

ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => !empty($_SERVER['HTTPS']),
]);
session_start();

spl_autoload_register(function (string $class): void {
    foreach (['core', 'controllers', 'models'] as $dir) {
        $file = BASE_PATH . "/app/{$dir}/{$class}.php";
        if (is_file($file)) { require $file; return; }
    }
});

require BASE_PATH . '/app/core/helpers.php';

$router = new Router();

$router->get('/login',     'AuthController@showLogin');
$router->post('/login',    'AuthController@login');
$router->get('/register',  'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->post('/logout',   'AuthController@logout');

$router->get('/',                      'PostController@index');
$router->get('/feed/load',             'PostController@load');
$router->post('/posts',                'PostController@store');
$router->get('/posts/{id}/edit',       'PostController@edit');
$router->post('/posts/{id}/update',    'PostController@update');
$router->post('/posts/{id}/delete',    'PostController@destroy');

$router->get('/posts/{id}/comments',   'CommentController@index');
$router->post('/posts/{id}/comments',  'CommentController@store');
$router->post('/comments/{id}/update', 'CommentController@update');
$router->post('/comments/{id}/delete', 'CommentController@destroy');

$router->post('/posts/{id}/like',      'LikeController@toggle');

$router->get('/profile/edit',          'ProfileController@edit');
$router->post('/profile/edit',         'ProfileController@update');
$router->get('/profile/{username}',    'ProfileController@show');

$router->get('/search',                'SearchController@index');

$router->get('/media/{folder}/{file}', 'MediaController@show');

try {
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (Throwable $e) {
    error_log((string)$e);
    http_response_code(500);
    exit('Something went wrong. Please try again.');
}