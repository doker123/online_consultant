<?php

namespace Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Src\Auth\IdentityInterface;

class User extends Model implements IdentityInterface
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "username",
        "email",
        "password",
        "role",
        "avatar_id",
        "created_at",
        "updated_at",
    ];

    protected $hidden = ["password"];

    protected static function boot()
    {
        static::creating(function ($user) {
            $user->password = password_hash($user->password, PASSWORD_BCRYPT);
        });

        static::updating(function ($user) {
            if ($user->isDirty("password")) {
                $user->password = password_hash(
                    $user->password,
                    PASSWORD_BCRYPT,
                );
            }
        });
    }

    public function findIdentity(int $id)
    {
        return self::where("id", $id)->first();
    }

    public function getId(): int
    {
        return (int) $this->id;
    }

    public function attemptIdentity(array $credentials)
    {
        $user = self::where("login", $credentials["login"])->first();
        if (
            $user &&
            password_verify($credentials["password"], $user->password)
        ) {
            return $user;
        }
        return null;
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole("admin");
    }

    public function isUser(): bool
    {
        return $this->hasRole("user");
    }

    public function isGuest(): bool
    {
        return $this->hasRole("guest");
    }
}
