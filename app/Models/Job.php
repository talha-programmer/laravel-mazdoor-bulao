<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;
    protected $table = "work_jobs";

    protected $fillable = [
        'title',
        'details',
        'budget',
        'deadline',
        'location',
        'has_allotted',

    ];

    public function bids()
    {
        return $this->hasMany(JobBid::class, 'job_id');
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
        return $this->belongsToMany(JobCategory::class, 'jobs_with_categories');
    }

    public function bidUsers()
    {
        //return $this->bids()->with('offeredBy');
        //return $this->hasManyThrough(User::class, JobBid::class, 'project_id', 'offered_by');
    }




}
