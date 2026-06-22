<?php

namespace Src\Auth;

use Model\User;

class IdentityProvider implements IdentityInterface
{
    private static ?IdentityProvider $instance = null;

    public static function single(): IdentityProvider
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function findIdentity(int $id)
    {
        return User::where('id', $id)->first();
    }

    public function getId(): int
    {
        return (int) (\Src\Session::get('user_id') ?? 0);
    }

    public function attemptIdentity(array $credentials)
    {
        $user = User::where('username', $credentials['login'])->first();
        if ($user && password_verify($credentials['password'], $user->password)) {
            \Src\Session::set('user_type', $user->role);
            return $user;
        }
        return null;
    }

    public static function getUserType(): string
    {
        return \Src\Session::get('user_type') ?? '';
    }

    public static function getDisplayName(): string
    {
        $user = self::currentUser();
        if ($user && method_exists($user, 'getDisplayName')) {
            return $user->getDisplayName();
        }
        return 'Гость';
    }

    public static function hasRole(string $role): bool
    {
        return self::getUserType() === $role;
    }

    public static function currentUser()
    {
        $instance = self::single();
        $id = $instance->getId();
        if ($id > 0) {
            return $instance->findIdentity($id);
        }
        return null;
    }
}
