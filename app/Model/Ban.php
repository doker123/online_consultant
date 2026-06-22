<?php

namespace Model;

use Illuminate\Database\Eloquent\Model;

class Ban extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'banned_by',
        'reason',
        'expires_at',
        'is_permanent',
        'created_at',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($ban) {
            $ban->created_at = date('Y-m-d H:i:s');
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'banned_by');
    }

    public function isExpired(): bool
    {
        if ($this->is_permanent) {
            return false;
        }
        return strtotime($this->expires_at) < time();
    }

    public function getRemainingTime(): string
    {
        if ($this->is_permanent) {
            return 'навсегда';
        }

        $remaining = strtotime($this->expires_at) - time();
        if ($remaining <= 0) {
            return 'истёк';
        }

        $hours = floor($remaining / 3600);
        $minutes = floor(($remaining % 3600) / 60);

        if ($hours > 0) {
            return $hours . ' ч. ' . $minutes . ' мин.';
        }
        return $minutes . ' мин.';
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->where('is_permanent', 1)
              ->orWhere('expires_at', '>', date('Y-m-d H:i:s'));
        });
    }
}
