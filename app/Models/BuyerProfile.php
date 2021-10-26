<?php

namespace App\Models;

use App\Enums\ReviewType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyerProfile extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Reviews which are given by this buyer to other workers
     */
    // TODO test these two functions
    public function reviewsGiven()
    {
        $user = $this->user()->getModel();
        return $user->reviewsGiven()->where('review_type' , '=', ReviewType::FromBuyerToWorker)->get();
    }

    /**
     * Reviews which are received by this buyer from other workers
     */
    public function reviewsReceived()
    {
        $user = $this->user()->getModel();
        return $user->reviewsReceived()->where('review_type' , '=', ReviewType::FromWorkerToBuyer)->get();
    }
}
