<?php

namespace App\Models;

use App\Enums\ReviewType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkerProfile extends Model
{
    protected $fillable = [
        'description',
    ];

    use HasFactory;

    /**
     * Job Categories are also represented as worker skills
     */
    public function skills()
    {
        return $this->belongsToMany(JobCategory::class, 'workers_with_skills', 'worker_profile_id', 'skill_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }



     /**
     * Reviews which are given by this worker to other buyers
      */
    public static function reviewsGiven(User $user)
    {
        return $user->reviewsGiven()->where('review_type' , '=', ReviewType::FromWorkerToBuyer)->get();
    }

    /**
    * Reviews which are received by this worker from other buyers
     */
    public static function reviewsReceived(User $user)
    {
        return $user->reviewsReceived()->where('review_type' , '=', ReviewType::FromBuyerToWorker)->get();
    }

    public static function getWorkerProfile($userId)
    {
        return WorkerProfile::where('user_id', '=', $userId)->first();
    }

    public static function getWorkerSkillIds($userId)
    {
        $workerProfile = WorkerProfile::getWorkerProfile($userId);
        $skills = $workerProfile->skills()->get()->toArray();
        return array_column($skills, 'id');
    }

}
