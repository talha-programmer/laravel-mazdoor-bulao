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
    public static function reviewsGiven(User $user)
    {
        return $user->reviewsGiven()
            ->where('review_type' , '=', ReviewType::FromBuyerToWorker)
            ->with(['givenBy', 'givenTo', 'order'])
            ->get();
    }

    /**
     * Reviews which are received by this buyer from other workers
     */
    public static function reviewsReceived(User $user)
    {
        return $user->reviewsReceived()->where('review_type' , '=', ReviewType::FromWorkerToBuyer)->get();
    }
}
