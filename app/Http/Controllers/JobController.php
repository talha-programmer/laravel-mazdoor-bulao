<?php

namespace App\Http\Controllers;

use App\Enums\JobStatus;
use App\Models\Image;
use App\Models\Job;

use App\Models\JobCategory;
use App\Models\WorkerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image as ImageLibrary;


class JobController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['store', 'destroy']);
    }

    /**
     * Get the jobs filtered by the skills of the logged-in worker in case of logged-in
     * If not logged-in, then fetch jobs normally
     */
    public function index(Request $request)
    {
        $jobs = null;
        $user = $request->user('sanctum');
        if($user){
            $skillIds = WorkerProfile::getWorkerSkillIds($user->id);
            if($skillIds && sizeof($skillIds) > 0){
                $jobs = Job::jobWithCategoryIds($skillIds);

            } else{
                $jobs = Job::where('posted_by', '!=', $user->id)->with(['postedBy', 'categories'])->latest()->get();
            }
        }
        else{
            $jobs = Job::with(['postedBy', 'categories'])->latest()->get();
        }
        $response = ['jobs' => $jobs->toArray()];
        return response($response, 200);
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
            'job_id' => 'numeric|min:1',         // in case of edit
            'no_of_images' => 'numeric|min:0'
        ]);

        // Single array of all images was not working
        // That's why images are passed as:
        // "no_of_images"
        // "image:0"
        // "image:1" ...


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

        if($job->save()){
            $job->categories()->attach($categories);

            $noOfImages = $request->no_of_images;

            if($noOfImages > 0){
                for ($i=0; $i<$noOfImages; $i++){
                    // Upload images and create their relation
                    $imageObject = new Image();
                    $loggedInUsername = auth()->user()->username;

                    $image = $request->file("image:" . $i);
                    $imageName = time() . '-' . Str::kebab($image->getClientOriginalName());
                    $imageUrl = "images/jobs/{$loggedInUsername}";

                    $thumbnail = ImageLibrary::make($image)->resize('250', null, function ($constraint){
                        $constraint->aspectRatio();
                    });
                    $thumbnailName = "thumbnail-" . $imageName;

                    $image->move(public_path($imageUrl), $imageName);
                    $thumbnail->save(public_path($imageUrl. "/$thumbnailName"));

                    $imageObject->image_url = $imageUrl . "/$imageName";
                    $imageObject->image_thumbnail_url = $imageUrl . "/$thumbnailName";

                    $imageObject->imageable()->associate($job);
                    $imageObject->save();
                }
            }


        }

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
          'job' => $job->load(['postedBy', 'categories', 'bids.offeredBy', 'orders', 'images']),
        ];

        return response($response, 200);
    }



}
