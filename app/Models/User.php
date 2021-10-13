<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'user_type',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Jobs posted by the user
     */
    public function jobs()
    {
        return $this->hasMany(Job::class, 'posted_by' );
    }

    /**
    * Job Orders on which this user is working / completed
     */
    public function sellingOrders()
    {
        return $this->hasMany(Order::class, 'worker_id');
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'worker_skills');
    }

    public function bids()
    {
        return $this->hasMany(JobBid::class, 'offered_by');
    }

    /**
      * Jobs on which this user has created any bid
     */
    public function appliedJobs(): array
    {
        // Return applied jobs as [{'job_id', job}] (job_id as indexes of the array)
        $appliedJobs = [];
        $bids = $this->bids()->with('job')->get();

        foreach ($bids as $bid){
            $job = $bid->job;
            $appliedJobs[$job->id] = $job;
        }

        return $appliedJobs;
    }

//    public function workingJobs()
//    {
//        return $this->bids()->with('job')->get()->where('job.status' ,'=', 0);
//    }


    public function buyingOrders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }


}
