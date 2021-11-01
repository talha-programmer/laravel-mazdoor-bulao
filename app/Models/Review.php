<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'review_text',
        'rating',
        'review_type'
    ];
    use HasFactory;

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function givenBy()
    {
        return $this->belongsTo(User::class, 'given_by');
    }

    public function givenTo()
    {
        return $this->belongsTo(User::class, 'given_to');
    }

    public static function getOrderReviews($orderId, $reviewType = null)
    {
        if($reviewType){
            return Review::where('order_id' , '=', $orderId)
                ->where('review_type', '=', $reviewType)->with(['givenBy', 'givenTo'])->get();
        }else{
            return Review::where('order_id' , '=', $orderId)->with(['givenBy', 'givenTo'])->get();
        }
    }



}
