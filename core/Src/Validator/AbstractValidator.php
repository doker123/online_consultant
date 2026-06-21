<?php

namespace Src\Auth;

use Model\Admin;
use Model\Aspirant;
use Model\ScientificDirector;

class IdentityProvider implements IdentityInterface
{
    public function findIdentity(int $id)
    {
        $userType = \Src\Session::get("user_type");

        return match ($userType) {
            "admin" => Admin::where("id", $id)->first(),
            "director" => ScientificDirector::where(
                "director_id",
                $id,
            )->first(),
            "aspirant" => Aspirant::where("aspirant_id", $id)->first(),
            default => null,
        };
    }

    public function getId(): int
    {
        return (int) (\Src\Session::get("user_id") ?? 0);
    }

    public function attemptIdentity(array $credentials)
    {
        $admin = Admin::where("login", $credentials["login"])->first();
        if (
            $admin &&
            password_verify($credentials["password"], $admin->password)
        ) {
            \Src\Session::set("user_type", "admin");
            return $admin;
        }

        $director = ScientificDirector::where(
            "login",
            $credentials["login"],
        )->first();
        if (
            $director &&
            password_verify($credentials["password"], $director->password)
        ) {
            \Src\Session::set("user_type", "director");
            return $director;
        }

        $aspirant = Aspirant::where("login", $credentials["login"])->first();
        if (
            $aspirant &&
            password_verify($credentials["password"], $aspirant->password)
        ) {
            \Src\Session::set("user_type", "aspirant");
            return $aspirant;
        }

        return null;
    }

    public static function getUserType(): string
    {
        return \Src\Session::get("user_type") ?? "";
    }

    public static function getDisplayName(): string
    {
        $user = self::single()->findIdentity(self::single()->getId());
        if ($user && method_exists($user, "getDisplayName")) {
            return $user->getDisplayName();
        }
        return "Гость";
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

    private static ?IdentityProvider $instance = null;

    public static function single(): IdentityProvider
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {}
}
