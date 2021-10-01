<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['userBids', 'appliedJobs']);
    }

    // TODO: Test this method
    public function userBids()
    {
        $user = auth()->user();
        $bids = $user->bids();
        $response = [
            'bids' => $bids
        ];

        return response($response, 200);
    }

//    public function appliedJobs()
//    {
//        $user = auth()->user();
//
//        return $user->appliedJobs();
//    }


}
