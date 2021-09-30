<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobBid;
use Illuminate\Http\Request;

class JobBidController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth'])->only(['store', 'destroy']);
    }

    public function index()
    {
        return JobBid::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'offered_amount' => 'required|digits_between:1,10000000',
            'details' => 'required',
            'job_id' => 'required|numeric|min:1',
            'job_bid_id' => 'numeric|min:1'                 // in case of edit
        ]);

        $job = Job::find($request->job_id);

        // restrict user from creating multiple bids on same job
        $user = auth()->user();
        if(array_key_exists($job->id, $user->appliedJobs())){
            // The user has already applied to this job
            return response(['error', 'You cannot create multiple bids on same job'], 403);
        }

        $bid = null;

        if($request->job_bid_id){
            // In case of edit
            $bid = JobBid::find($request->job_bid_id);
        }else{
            $bid = new JobBid();
        }

        $bid->job()->associate($job);

        $bid->offered_amount = $request->offered_amount;
        $bid->details = $request->details;

        $bid->offeredBy()->associate(auth()->user());

        $bid->save();

        return response(['info' => 'Bid added successfully!'], 200);

    }

    public function destroy(Request $request)
    {
        $request->validate([
            'job_bid_id' => 'required|numeric|min:1'
        ]);
        $bid = JobBid::find($request->job_bid_id);
        if($bid->delete()){
            return response(['info' => 'Bid deleted successfully'], 200);
        } else{
            return response(['error'=>'There was an error while deleting the bid. Please try again'], 200);
        }
    }

    public function jobBids(Job $job)
    {
        return $job->bids()->with('offeredBy')->get();
    }


}
