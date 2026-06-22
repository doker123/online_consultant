<?php

namespace Model;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'chat_id',
        'sender_id',
        'text',
        'is_read',
        'created_at',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($message) {
            $message->is_read = 0;
            $message->created_at = date('Y-m-d H:i:s');
        });
    }

    public function chat()
    {
        return $this->belongsTo(Chat::class, 'chat_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
