<?php

namespace Controller\Public;

use Model\User;
use Model\Chat;
use Src\View;
use Src\Session;
use Src\Request;
use Src\Auth\Auth as AuthManager;

class Auth
{
    public function signup(Request $request): string
    {
        if ($request->method === 'POST') {
            $username = trim($request->get('username') ?? '');
            $email = trim($request->get('email') ?? '');
            $password = $request->get('password') ?? '';

            $errors = [];

            if (empty($username) || strlen($username) < 3) {
                $errors[] = 'Имя должно быть не менее 3 символов';
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Некорректный email';
            }
            if (empty($password) || strlen($password) < 6) {
                $errors[] = 'Пароль должен быть не менее 6 символов';
            }
            if (User::where('username', $username)->exists()) {
                $errors[] = 'Пользователь с таким именем уже существует';
            }
            if (User::where('email', $email)->exists()) {
                $errors[] = 'Пользователь с таким email уже существует';
            }

            if (empty($errors)) {
                $user = User::create([
                    'username' => $username,
                    'email' => $email,
                    'password' => $password,
                    'role' => 'user',
                ]);

                AuthManager::login($user);

                Chat::create([
                    'user_id' => $user->id,
                ]);

                app()->route->redirect('/');
                exit();
            }

            Session::set('signup_errors', $errors);
            Session::set('old_username', $username);
            Session::set('old_email', $email);
            app()->route->redirect('/signup');
            exit();
        }

        return (string) new View('Public.signup', [
            'errors' => Session::get('signup_errors') ?? [],
            'old' => [
                'username' => Session::get('old_username') ?? '',
                'email' => Session::get('old_email') ?? '',
            ],
        ]);
    }

    public function login(Request $request): string
    {
        if ($request->method === 'POST') {
            $login = trim($request->get('username') ?? '');
            $password = $request->get('password') ?? '';

            if (AuthManager::attempt(['login' => $login, 'password' => $password])) {
                app()->route->redirect('/');
                exit();
            }

            Session::set('login_error', 'Неверное имя пользователя или пароль');
            Session::set('old_login', $login);
            app()->route->redirect('/login');
            exit();
        }

        return (string) new View('Public.login', [
            'error' => Session::get('login_error') ?? '',
            'old_login' => Session::get('old_login') ?? '',
        ]);
    }

    public function logout(Request $request): void
    {
        AuthManager::logout();
        app()->route->redirect('/login');
        exit();
    }
}
