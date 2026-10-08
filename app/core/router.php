<?php

//post
$router -> get('/', [PostController::class, 'index']);

$router -> post('/posts', [PostController::class, 'store']);
$router -> get('/posts/{id}', [PostController::class, 'show']);
$router -> get('/posts/{id}/edit', [PostController::class, 'editPost']);
$router -> post('/posts/{id}/update', [PostController::class, 'updPost']);
$router -> post('/posts/{id}/delete', [PostController::class, 'delPost']);

//auth
$router -> get('/login', [AuthController::class, 'showLogin']);
$router -> post('/login', [AuthController::class, 'login']);
$router -> get('/register', [AuthController::class, 'showReg']);
$router -> post('/register', [AuthController::class, 'Register']);
$router -> post('/logout', [AuthController::class, 'logout']);

//profile
$router -> get('/profile/edit', [ProfileController::class, 'profEdit']);
$router -> post('/profile/edit', [ProfileController::class, 'profUpd']);
$router -> post('/profile/{id}', [ProfileController::class, 'showProf']);
$router -> get('/profile/password', [ProfileController::class, 'changePass']);

//like
$router -> post('/posts/{id}/like', [LikeController::class, 'likes']);

//comment
$router -> post('/posts/{id}/comments', [CommentController::class, 'comment']);
$router -> post('/comments/{id}/delete', [CommentController::class, 'delComm']);
$router -> get('/comments/{id}/edit', [CommentController::class, 'editComm']);
$router -> post('/comments/{id}/update', [CommentController::class, 'updComm']);