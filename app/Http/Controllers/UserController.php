<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\ReviewType;
use App\Enums\UserType;
use App\Models\User;
use App\Util\ImageUtil;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['userBids', 'appliedJobs', 'storeUserProfile']);
    }

    public function index()
    {
        $users = User::with(['profileImage'])->orderBy('name')->get();
        $response = [
            'users' => $users,
        ];

        return response($response, 200);
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'username' => 'required|unique:users|string',
            'email' => 'required|unique:users,email',
            'password' => 'required',
            'user_type' => new EnumValue(UserType::class),
        ]);

        $userType = array_key_exists('user_type', $fields) ? $fields['user_type'] : UserType::Customer;

        $user = User::create([
            'name' => $fields['name'],
            'username' => $fields['username'],
            'email' => $fields['email'],
            'user_type' => $userType,
            'password' => bcrypt($fields['password']),
        ]);

        $user->workerProfile()->create();
        $user->buyerProfile()->create();

        $response = [
            'user' => $user,
        ];

        return response($response, 201);
    }



    public function delete(Request $request)
    {
        $request->validate([
            'user_id' => 'required'
        ]);
        $user = User::find($request->user_id);
        $user->delete();
        return response(['message' => 'Deleted Successfully!'], 200);
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
        $bids = $user->bids()->with(['job', 'order'])->get();
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
        $postedJobs = $user->jobs()->with(['categories'])->latest()->get();
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
            'name' => ''
        ]);

        $phoneNumber = $request->phone_number;
        $location = $request->location;
        $name = $request->name;

        $user = auth()->user();

        if(is_file($request->profile_picture)) {
            $user->profileImage()->delete();    // In case of edit

            $profilePicture = $request->file('profile_picture');
            $imagePath = "images/profile-pictures";

            ImageUtil::saveImage($profilePicture, $imagePath, $user);
        }

        $user->phone_number = $phoneNumber;
        $user->location = $location;
        $user->name = $name;

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

    public function userProfile()
    {
        $user = auth()->user();
        $workerProfile = [
            'orders_completed' => $user->sellingOrders()->where('status', OrderStatus::Completed)->count(),
            'rating' => $user->workerProfile->rating,
            'total_reviews' => $user->reviewsReceived()->where('review_type', ReviewType::FromBuyerToWorker)->count(),
        ];
        $buyerProfile = [
            'jobs_posted' => $user->jobs()->count(),
            'rating' => $user->buyerProfile->rating,
            'total_reviews' => $user->reviewsReceived()->where('review_type', ReviewType::FromWorkerToBuyer)->count(),

        ];
        $profile = [
            'worker_profile' => $workerProfile,
            'buyer_profile' => $buyerProfile
            ];


        return response(['user_profile' => $profile], 200);

    }


}
