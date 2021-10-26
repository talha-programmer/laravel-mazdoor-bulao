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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }



     /**
     * Reviews which are given by this worker to other buyers
      */
    public function reviewsGiven()
    {
        $user = $this->user()->getModel();
        return $user->reviewsGiven()->where('review_type' , '=', ReviewType::FromWorkerToBuyer)->get();
    }

    /**
    * Reviews which are received by this worker from other buyers
     */
    public function reviewsReceived()
    {
        $user = $this->user()->getModel();
        return $user->reviewsReceived()->where('review_type' , '=', ReviewType::FromBuyerToWorker)->get();
    }
}
