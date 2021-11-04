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

    public static function createBuyerProfile($userId)
    {
        $buyerProfile = new BuyerProfile();
        $buyerProfile->user_id = $userId;
        $buyerProfile->rating = 0;
        if($buyerProfile->save()){
            return $buyerProfile;
        }
        return null;
    }

    public static function getBuyerProfile($userId)
    {
        $buyerProfile = BuyerProfile::where('user_id', '=', $userId)->first();
        if($buyerProfile){
            return $buyerProfile;
        }else{
            return BuyerProfile::createBuyerProfile($userId);
        }
    }
}
