<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'details',
        'has_allotted'
    ];

    public function bids()
    {
        return $this->hasMany(ProjectBid::class, 'project_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function categories()
    {
        return $this->belongsToMany(ProjectCategory::class, 'projects_with_categories');
    }

    public function bidUsers()
    {
        //return $this->bids()->with('offeredBy');
        //return $this->hasManyThrough(User::class, ProjectBid::class, 'project_id', 'offered_by');
    }




}
