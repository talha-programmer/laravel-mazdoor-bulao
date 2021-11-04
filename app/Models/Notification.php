<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    public function notifiable()
    {
        return $this->morphTo(__FUNCTION__, 'notifiable_type', 'notifiable_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }



}
