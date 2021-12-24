<?php

namespace App\Models;

use App\Enums\JobStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
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
        'city',
        'area',
        'has_allotted',
        'status',
    ];

    protected $appends = ['url', 'location'];

    public function getUrlAttribute()
    {
        return Str::kebab($this->title .' ' . $this->id);
    }

    public function getLocationAttribute()
    {
        $location = $this->city;
        if($this->area){
            $location = "{$this->area}, {$location}";
        }
        return $location;
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
    public static function filterJobs(array $categoryIds = null, User $user = null, string $city = null)
    {
        $loggedInUserId = -1;
        if($user){
            $loggedInUserId = $user->id;
        }

        return Job::query()
            ->where([
                ['posted_by', '!=' , $loggedInUserId],
                ['status', '=', JobStatus::Hiring],
                ['created_at', '>',  now()->subDays(5)->endOfDay()]
                ])
            ->when($city, function ($query, $city){
                return $query->where('city', $city);
            })
            ->join('jobs_with_categories', 'work_jobs.id', '=', 'jobs_with_categories.job_id')
            ->when($categoryIds, function ($query, $categoryIds){
                return $query->whereIn('jobs_with_categories.job_category_id', $categoryIds);
            })
            ->latest()->with(['categories', 'postedBy:id,name'])

            ->get()->unique();
    }

    public static function getCities()
    {
        return Job::query()->distinct()->select(['city'])->get()->pluck('city');

    }





}
