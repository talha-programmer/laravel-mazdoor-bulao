<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'starting_time',
        'ending_time',
        'status'
    ];

    protected $appends = [
        'url'
    ];

    public function getUrlAttribute()
    {
        return $this->starting_time . '-' . $this->id;
    }

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    public function bid()
    {
        return $this->belongsTo(JobBid::class, 'job_bid_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'order_id');
    }

}
