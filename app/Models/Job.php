<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        'status'
    ];

    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        return Str::kebab($this->title .' ' . $this->id);
    }

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

    /**
     * Fetch jobs filtered by the category Ids provided.
     */
    public static function jobWithCategoryIds(array $categoryIds)
    {
        return Job::query()
            ->join('jobs_with_categories', 'work_jobs.id', '=', 'jobs_with_categories.job_id')
            ->whereIn('jobs_with_categories.job_category_id', $categoryIds)
            ->get()->unique();
    }





}
