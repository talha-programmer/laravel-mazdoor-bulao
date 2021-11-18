<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Util\ImageUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['userBids', 'appliedJobs', 'storeUserProfile']);
    }

    public function user()
    {
        $response = [
            'user' => auth()->user()->load('profileImage')
        ];
        return response($response, '200');
    }


    public function userBids()
    {
        $user = auth()->user();
        $bids = $user->bids()->with('job')->get();
        $response = [
            'bids' => $bids
        ];

        return response($response, 200);
    }

    public function appliedJobs()
    {
        $user = auth()->user();
        $response = [
            'applied_jobs' => $user->appliedJobs()
        ];

        return response($response, 200);
    }

    public function postedJobs()
    {
        $user = auth()->user();
        $postedJobs = $user->jobs;
        $response = [
            'posted_jobs' => $postedJobs
        ];
        return response($response, 200);
    }

    public function storeUserProfile(Request $request)
    {
        $request->validate([
            'phone_number' => '',
            'location' => '',
            'profile_picture' => '',
        ]);

        $phoneNumber = $request->phone_number;
        $location = $request->location;

        $user = auth()->user();

        if(is_file($request->profile_picture)) {
            $user->profileImage()->delete();    // In case of edit

            $profilePicture = $request->file('profile_picture');
            $imagePath = "images/profile-pictures";

            ImageUtil::saveImage($profilePicture, $imagePath, $user);
        }

        $user->phone_number = $phoneNumber;
        $user->location = $location;

        if($user->save()){
            return response(['status' => 'Profile saved successfully!'], 200);
        }
        return response(['status'=> 'An error occurred while saving profile!'], 403);
    }


    public function chatUsers()
    {
        $user = auth()->user();
        $response = [
            'chat_users' => $user->allowedChats()
        ];

        return response($response, 200);
    }


}
