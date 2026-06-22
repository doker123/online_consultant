<?php

namespace Src\Auth;

use Src\Session;

class Auth
{
    private static IdentityInterface $user;

    public static function init(IdentityInterface $user): void
    {
        self::$user = $user;
        if (self::check()) {
        }
    }

    public static function login(IdentityInterface $user): void
    {
        self::$user = $user;
        Session::set("user_id", $user->getId());

        if (method_exists($user, "getUserType")) {
            Session::set("user_type", $user->getUserType());
        }
    }

    public static function attempt(array $credentials): bool
    {
        if ($user = self::$user->attemptIdentity($credentials)) {
            self::login($user);
            return true;
        }
        return false;
    }

    public static function user()
    {
        $id = Session::get("user_id") ?? 0;
        if ($id > 0) {
            return self::$user->findIdentity($id);
        }
        return null;
    }

    public static function check(): bool
    {
        $id = Session::get("user_id") ?? 0;
        return $id > 0 && self::$user->findIdentity($id) !== null;
    }

    public static function logout(): bool
    {
        Session::clear("user_id");
        Session::clear("user_type");
        return true;
    }

    public static function getUserType(): string
    {
        return Session::get("user_type") ?? "";
    }

    public static function hasRole(string $role): bool
    {
        return self::getUserType() === $role;
    }

    public static function isAdmin(): bool
    {
        return self::hasRole("admin");
    }

    public static function isUser(): bool
    {
        return self::hasRole("user");
    }

    public static function getDisplayName(): string
    {
        $user = self::user();
        if ($user && method_exists($user, "getDisplayName")) {
            return $user->getDisplayName();
        }
        return "Гость";
    }

    public static function generateCSRF(): string
    {
        $token = Session::get("csrf_token");
        if ($token === null) {
            $token = bin2hex(random_bytes(32));
            Session::set("csrf_token", $token);
        }
        return $token;
    }
}
