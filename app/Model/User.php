<?php

namespace Model;

use Illuminate\Database\Eloquent\Model;
use Src\Auth\IdentityInterface;

class User extends Model implements IdentityInterface
{

    protected $table = 'users';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'avatar',
        'created_at',
        'updated_at',
    ];

    protected $hidden = ['password'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            $user->password = password_hash($user->password, PASSWORD_BCRYPT);
            $user->created_at = date('Y-m-d H:i:s');
        });

        static::updating(function ($user) {
            if ($user->isDirty('password')) {
                $user->password = password_hash($user->password, PASSWORD_BCRYPT);
            }
            $user->updated_at = date('Y-m-d H:i:s');
        });
    }

    public function findIdentity(int $id)
    {
        return self::where('id', $id)->first();
    }

    public function getId(): int
    {
        return (int) $this->id;
    }

    public function attemptIdentity(array $credentials)
    {
        $user = self::where('username', $credentials['login'])->first();
        if ($user && password_verify($credentials['password'], $user->password)) {
            return $user;
        }
        return null;
    }

    public function getDisplayName(): string
    {
        return $this->username;
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isUser(): bool
    {
        return $this->hasRole('user');
    }

    public function isOnline(): bool
    {
        $lastSeen = $this->last_seen ?? null;
        if (!$lastSeen) return false;
        return strtotime($lastSeen) > strtotime('-5 minutes');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, 'user_id');
    }
}
