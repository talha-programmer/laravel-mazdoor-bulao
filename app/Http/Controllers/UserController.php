<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['userBids', 'appliedJobs']);
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

//    public function appliedJobs()
//    {
//        $user = auth()->user();
//
//        return $user->workingJobs();
//    }


}
