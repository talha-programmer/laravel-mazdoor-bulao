<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use App\Models\User;
use App\Models\WorkerProfile;
use Illuminate\Http\Request;

class WorkerProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $profile = $user->workerProfile()->with('skills')->first();
        $reviews = WorkerProfile::reviewsReceived($user)->latest()->limit(5)->get();
        $profile->reviews = $reviews;
        return response(['worker_profile' => $profile], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required',
            'is_edit' => 'boolean',
            'skills' => 'required|string'
        ]);

        $user = auth()->user();
        $skills = explode(',', $request->skills);


        $profile = WorkerProfile::where('user_id', '=', $user->id)->first();
        if($profile){
            // in case of edit
            $profile->skills()->detach();
        }else{
            $profile = new WorkerProfile();
            $profile->user()->associate($user);
        }

        $profile->description = $request->description;
        $profile->save();

        $profile->skills()->attach($skills);

        return response(['status' => 'Profile created successfully!'], 200);
    }

    public function getReviewsGiven(Request $request)
    {
        $request->validate([
            'user_id' => 'required|numeric|min:1',
        ]);
        $user = User::findOrFail($request->user_id);
        $reviewsGiven = WorkerProfile::reviewsGiven($user);

        $response = ['reviews_given' => $reviewsGiven];
        return response($response, 200);
    }

    public function getReviewsReceived(Request $request)
    {
        $request->validate([
            'user_id' => 'required|numeric|min:1',
        ]);
        $user = User::findOrFail($request->user_id);
        $reviewsReceived = WorkerProfile::reviewsReceived($user);

        $response = ['reviews_received' => $reviewsReceived];
        return response($response, 200);
    }


}
