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

    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    public function bid()
    {
        return $this->belongsTo(ProjectBid::class, 'project_bid_id');
    }


}
