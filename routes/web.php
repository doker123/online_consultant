<?php

use Src\Route;

Route::add(['GET'], '/', [Controller\Public\Home::class, 'index']);

Route::add(['GET', 'POST'], '/signup', [Controller\Public\Auth::class, 'signup']);
Route::add(['GET', 'POST'], '/login', [Controller\Public\Auth::class, 'login']);
Route::add(['GET'], '/logout', [Controller\Public\Auth::class, 'logout']);

Route::add(['GET'], '/chat', [Controller\ChatController::class, 'index'])->middleware('auth');
Route::add(['POST'], '/chat/send', [Controller\ChatController::class, 'send'])->middleware('auth');
Route::add(['GET'], '/chat/messages/{id}', [Controller\ChatController::class, 'messages'])->middleware('auth');
Route::add(['POST'], '/chat/read/{id}', [Controller\ChatController::class, 'readMessage'])->middleware('auth');
Route::add(['GET'], '/chat/status', [Controller\ChatController::class, 'status'])->middleware('auth');

Route::add(['GET'], '/admin', [Controller\AdminController::class, 'index'])->middleware('role:admin');
Route::add(['GET'], '/admin/chats', [Controller\AdminController::class, 'chats'])->middleware('role:admin');
Route::add(['GET'], '/admin/chat/{id}', [Controller\AdminController::class, 'chat'])->middleware('role:admin');
Route::add(['POST'], '/admin/chat/{id}/send', [Controller\AdminController::class, 'sendMessage'])->middleware('role:admin');
Route::add(['POST'], '/admin/ban/{id}', [Controller\AdminController::class, 'banUser'])->middleware('role:admin');
Route::add(['POST'], '/admin/unban/{id}', [Controller\AdminController::class, 'unbanUser'])->middleware('role:admin');
Route::add(['GET'], '/admin/users', [Controller\AdminController::class, 'users'])->middleware('role:admin');
Route::add(['POST'], '/admin/avatar', [Controller\AdminController::class, 'uploadAvatar'])->middleware('auth');

Route::add(['GET'], '/admin/pages', [Controller\AdminController::class, 'pages'])->middleware('role:admin');
Route::add(['GET', 'POST'], '/admin/pages/create', [Controller\AdminController::class, 'createPage'])->middleware('role:admin');
Route::add(['GET', 'POST'], '/admin/pages/{id}/edit', [Controller\AdminController::class, 'editPage'])->middleware('role:admin');
Route::add(['POST'], '/admin/pages/{id}/delete', [Controller\AdminController::class, 'deletePage'])->middleware('role:admin');
Route::add(['POST'], '/admin/pages/{id}/toggle', [Controller\AdminController::class, 'togglePage'])->middleware('role:admin');

Route::add(['GET'], '/page/{slug}', [Controller\Public\Home::class, 'page']);
