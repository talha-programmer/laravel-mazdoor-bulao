<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use const http\Client\Curl\AUTH_ANY;

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

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable', 'imageable_type', 'imageable_id' );
    }

    /**
     * Fetch jobs filtered by the category Ids provided.
     */
    public static function jobWithCategoryIds(array $categoryIds)
    {
        $loggedInUserId = -1;
        if(Auth::check()){
            $loggedInUserId = Auth::id();
        }
        return Job::query()
            ->where('posted_by', '!=' , $loggedInUserId)
            ->join('jobs_with_categories', 'work_jobs.id', '=', 'jobs_with_categories.job_id')
            ->whereIn('jobs_with_categories.job_category_id', $categoryIds)
            ->latest()
            ->get()->unique();
    }





}
