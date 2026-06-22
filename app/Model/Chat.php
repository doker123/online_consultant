<?php

namespace Model;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'admin_id',
        'status',
        'created_at',
        'updated_at',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($chat) {
            $chat->created_at = date('Y-m-d H:i:s');
        });

        static::updating(function ($chat) {
            $chat->updated_at = date('Y-m-d H:i:s');
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'chat_id');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class, 'chat_id')->latest();
    }

    public function getUnreadCount(): int
    {
        return $this->messages()->where('is_read', 0)->where('sender_id', '!=', $this->user_id)->count();
    }
}
