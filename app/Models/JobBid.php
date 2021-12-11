<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobBid extends Model
{
    use HasFactory;
    protected $fillable = [
        'offered_amount',
        'details',
        'status',
        'completion_time'
    ];

    public function job()
    {
        return $this->belongsTo(Job::class,'job_id');
    }

    public function offeredBy()
    {
        return $this->belongsTo(User::class, 'offered_by');
    }

    public function order()
    {
        return $this->hasOne(Order::class, 'job_bid_id');
    }
}
