<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCategory extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function jobs()
    {
        return $this->belongsToMany(Job::class, 'jobs_with_categories');
    }

    /**
     * Job Categories are also represented as worker skills
     * to filter the jobs with worker skills
     */
    public function workers()
    {
        return $this->belongsToMany(WorkerProfile::class, 'workers_with_skills', 'skill_id', 'worker_profile_id');
    }
}
