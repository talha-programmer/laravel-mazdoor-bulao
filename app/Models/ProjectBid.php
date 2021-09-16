<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectBid extends Model
{
    use HasFactory;

    public function project()
    {
        return $this->belongsTo(Project::class,'project_id');
    }

    public function offeredBy()
    {
        return $this->belongsTo(User::class, 'offered_by');
    }

    public function order()
    {
        return $this->hasOne(Order::class, 'bid_id');
    }
}
