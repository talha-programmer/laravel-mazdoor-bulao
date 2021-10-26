<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'review_text',
        'rating'
    ];
    use HasFactory;

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function givenBy()
    {
        return $this->belongsTo(User::class, 'given_by');
    }

    public function givenTo()
    {
        return $this->belongsTo(User::class, 'given_to');
    }

}
