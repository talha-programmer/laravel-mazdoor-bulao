<?php

namespace App\Http\Controllers;

use App\Models\BuyerProfile;
use App\Models\User;
use App\Models\WorkerProfile;
use Illuminate\Http\Request;

class BuyerProfileController extends Controller
{

    public function __construct()
    {
        return $this->middleware(['auth']);
    }

    public function index()
    {
        $profile = auth()->user()->buyerProfile;

        return response(['buyer_profile' => $profile], 200);
    }


    public function store(Request $request)
    {
        $user = auth()->user();
        $user->workerProfile()->create([

        ]);

        return response(['status' => 'Profile created successfully!']);
    }


    public function getReviewsGiven(Request $request)
    {
        $request->validate([
            'user_id' => 'required|numeric|min:1',
        ]);
        $user = User::findOrFail($request->user_id);
        $reviewsGiven = BuyerProfile::reviewsGiven($user);

        $response = ['reviews_given' => $reviewsGiven];
        return response($response, 200);
    }

    public function getReviewsReceived(Request $request)
    {
        $request->validate([
            'user_id' => 'required|numeric|min:1',
        ]);
        $user = User::findOrFail($request->user_id);
        $reviewsReceived = BuyerProfile::reviewsReceived($user);

        $response = ['reviews_received' => $reviewsReceived];
        return response($response, 200);
    }



}
