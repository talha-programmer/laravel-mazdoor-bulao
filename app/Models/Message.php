<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'message_text',
        'attachment_url',
        'message_type',
    ];
    use HasFactory;

    public function from()
    {
        return $this->belongsTo(User::class, 'from');
    }

    public function to()
    {
        return $this->belongsTo(User::class, 'to');
    }

    public function notification()
    {
        return $this->morphOne(
            Notification::class,
            'notifiable',
            'notifiable_type',
            'notifiable_id'
        );
    }

    public static function chatBetweenUsers($userId1, $userId2)
    {
        return Message::query()
            ->where([
                ['to', '=', $userId1],
                ['from', '=', $userId2],
            ])->orWhere([
                ['to', '=', $userId2],
                ['from', '=', $userId1]
            ])->latest()
            ->limit(100)
            ->get()
            ->toArray();
    }


}
