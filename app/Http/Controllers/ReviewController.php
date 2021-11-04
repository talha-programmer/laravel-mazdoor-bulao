<?php

namespace App\Http\Controllers;

use App\Enums\ReviewType;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth'])->only(['store']);
    }

    /**
     * Get reviews associated with a single order
     * If no review type is provided, all types of reviews will be fetched
     */
    public function getOrderReviews(Request $request)
    {
        $request->validate([
            'order_id' => 'required|numeric|min:1',
            'review_type' => [new EnumValue(ReviewType::class)],      // TODO this is showing error in postman
        ]);

        $reviewType = $request->review_type;
        $orderId = $request->order_id;
        $reviews = Review::getOrderReviews($orderId, $reviewType);
        $response = ['order_reviews'=> $reviews];

        return response($response, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|numeric|min:1',
            'review_text' => '',
            'rating' => 'required|numeric|between:1,5',
            'review_type' => ['required', new EnumValue(ReviewType::class)],
            'given_to' => 'required|numeric|min:1'
        ]);

        $order = Order::findOrFail($request->order_id);
        $givenTo = User::findOrFail($request->given_to);
        $loggedInUser = auth()->user();

        $review = new Review([
            'rating' => $request->rating,
            'review_text' => $request->review_text,
            'review_type' => $request->review_type
        ]);
        $review->givenBy()->associate($loggedInUser);
        $review->givenTo()->associate($givenTo);
        $review->order()->associate($order);

        if($review->save()){
            // Update the rating of the user who has received the review
            $profile = null;
            if($review->review_type === ReviewType::FromWorkerToBuyer){
                $profile = BuyerProfile::getBuyerProfile($review->given_to);

            }else if($review->review_type == ReviewType::FromBuyerToWorker){
                $profile = WorkerProfile::getWorkerProfile($review->given_to);
            }

            if($profile){
                $profileRating = $profile->rating;
                $newRating = null;
                if($profileRating == null || $profileRating == 0){
                    $newRating = $review->rating;
                }else{
                    $newRating =  ($profileRating + $review->rating) / 2.0;
                }
                $profile->rating = $newRating;
                $profile->save();
            }
            return response(['status' => 'Review Saved'], 200);
        }

        return response(['status' => 'Error Occurred while saving the review'], 403);
    }
}
