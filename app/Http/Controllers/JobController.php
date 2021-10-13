<?php

namespace App\Http\Controllers;

use App\Enums\JobStatus;
use App\Models\Job;

use App\Models\JobCategory;
use Illuminate\Http\Request;
use Psy\Util\Str;


class JobController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['store', 'destroy']);
    }

    public function index()
    {
        $jobs = Job::with('postedBy')->latest()->get();

        return $jobs->toArray();

    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'details' => 'required',
            'budget' => 'required|numeric',
            'deadline' => 'required|numeric',
            'location' => 'required|string|max:255',
            'categories' => 'required|string',
            'job_id' => 'numeric|min:1'         // in case of edit
        ]);

        $title = $request->title;
        $details = $request->details;
        $budget = $request->budget;
        $deadline = $request->deadline;
        $location = $request->location;

        $categories = explode(',' , $request->categories);
        $jobId = $request->job_id;

        $job = null;

        if($jobId > 0){
            $job = Job::find($jobId);
            $job->categories()->detach();
        } else {
            $job = new Job();
            $job->postedBy()->associate(auth()->user());
        }

        $job->fill([
            'title' => $title,
            'details' => $details,
            'location' => $location,
            'deadline' => $deadline,
            'budget' => $budget,
            'status' => JobStatus::Hiring
        ]);

        $job->save();
        $job->categories()->attach($categories);

        return response(['info' => 'job has been created successfully!']);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'job_id' => 'required|numeric|min:1'
        ]);
        $job = Job::find($request->job_id);
        if($job->delete()) {
            return response(['info' => 'job deleted successfully']);
        } else {
            return response(['error' => 'An error occurred while deleting job', 200]);
        }
    }

    public function singleJob(Job $job)
    {
        $response = [
          'job' => $job->load(['postedBy', 'categories', 'bids.offeredBy', 'orders']),
        ];

        return response($response, 200);
    }


}
