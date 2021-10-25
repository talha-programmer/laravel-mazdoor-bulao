<?php

namespace App\Http\Controllers;

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


}
