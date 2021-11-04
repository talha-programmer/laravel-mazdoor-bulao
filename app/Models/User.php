<?php

namespace App\Models;

use http\Env\Request;
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

    public function workerProfile()
    {
        return $this->hasOne(WorkerProfile::class, 'user_id');
    }

    public function buyerProfile()
    {
        return $this->hasOne(BuyerProfile::class, 'user_id');
    }

    public function messagesSent()
    {
        return $this->hasMany(Message::class, 'from');
    }

    public function messagesReceived()
    {
        return $this->hasMany(Message::class, 'to');
    }

    public function allowedChat1()
    {
        return $this->hasMany(AllowedChat::class, 'user_1');
    }

    public function allowedChat2()
    {
        return $this->hasMany(AllowedChat::class, 'user_2');
    }

    /**
     * Return the array of the users to which chat is allowed for the current user
     * The keys of the array are the ids of the users
     */
    public function allowedChats(): array
    {
        $chat1 = $this->allowedChat1()->with(['user2'])->get();
        $chat2 = $this->allowedChat2()->with(['user1'])->get();
        $allowedChats = [];

        foreach ($chat1 as $chat) {
            $user = $chat->user2;
            $allowedChats[$user->id] = $user;
        }

        foreach ($chat2 as $chat) {
            $user = $chat->user1;
            if(!array_key_exists( $user->id, $allowedChats)){
                $allowedChats[$user->id] = $user;
            }
        }

        return $allowedChats;
    }

    /**
    * Return sent and received messages of this user to / from the userId passed in parameter
     */
    public function chatWithUser($userId)
    {
        $secondUser = User::findorFail($userId);


        $messagesReceived = $this->messagesReceived()->where('from','=', $userId)->get()->toArray();
        $messagesSent = $this->messagesSent()->where('to', '=', $userId)->get()->toArray();

        $allMessages =  array_merge($messagesReceived, $messagesSent);

        // Sorting the array in ascending order with 'created_at'
        // We can shift item1 and item2 to sort it in desc order
        usort($allMessages, function ($item1, $item2) {
            return $item1['created_at'] <=> $item2['created_at'];
        });

        return $allMessages;
    }

    public function reviewsGiven()
    {
        return $this->hasMany(Review::class, 'given_by');
    }

    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'given_to');
    }

    public function appNotifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function profileImage()
    {
        return $this->morphOne(Image::class, 'imageable', 'imageable_type', 'imageable_id');
    }




}
