<?php

namespace App\Http\Controllers;

use App\Enums\JobStatus;
use App\Models\BuyerProfile;
use App\Models\Job;

use App\Models\WorkerProfile;
use App\Util\ImageUtil;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\Request;


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
        $categories = $request->categories;
        $city = $request->city;
        if($categories){
            $categories = explode(',', $categories);
        }
        $jobs = Job::filterJobs($categories, $user, $city);
//        if($user){
//            $skillIds = WorkerProfile::getWorkerSkillIds($user->id);
//            if($skillIds && sizeof($skillIds) > 0){
//                $jobs = Job::filterJobs($skillIds, $user);
//
//            } else{
//                $jobs = Job::where(
//                    [['posted_by', '!=', $user->id],
//                        ['status', '=', JobStatus::Hiring]
//                    ])->with(['postedBy:id,name', 'categories'])->latest()->get();
//            }
//        }
//        else{
//            $jobs = Job::where('status', '=', JobStatus::Hiring)->with(['postedBy:id,name', 'categories'])->latest()->get();
//        }
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
            'city' => 'required|string|max:255',
            'area' => 'string|max:255',
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
        $city = $request->city;
        $area = $request->area;
        $categories = explode(',' , $request->categories);
        $jobId = $request->job_id;

        $job = null;

        if($jobId > 0){
            $job = Job::find($jobId);
            $job->categories()->detach();

            // detach images and delete them
            $images = $job->images;
            foreach ($images as $image){
                $imagePath = public_path() . "/$image->image_path";
                $thumbnailPath = public_path() . "/$image->image_thumbnail_path";
                if(file_exists($imagePath)){
                    unlink($imagePath);
                }
                if(file_exists($thumbnailPath)){
                    unlink($thumbnailPath);
                }
            }
            $job->images()->delete();
        } else {
            $job = new Job();
            $job->postedBy()->associate(auth()->user());
        }

        $job->fill([
            'title' => $title,
            'details' => $details,
            'city' => $city,
            'area' => $area,
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

                    $loggedInUsername = auth()->user()->username;

                    $image = $request->file("image:" . $i);
                    $imageUrl = "images/jobs/{$loggedInUsername}";

                    ImageUtil::saveImage($image, $imageUrl, $job);
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
        $job = $job->load(['postedBy.profileImage', 'categories', 'images']);
        $job->no_of_bids = $job->bids()->count();
        $job->buyer_profile = BuyerProfile::getBuyerProfile($job->postedBy->id);
        $response = [
          'job' => $job,
        ];

        return response($response, 200);
    }

    public function singleJobPosted(Job $job)
    {
        $job = $job->load(['categories', 'bids.offeredBy', 'bids.order', 'orders', 'images']);
        $job->no_of_bids = $job->bids()->count();
        $response = [
          'job' => $job,
        ];

        return response($response, 200);
    }

    /*public function markAsCompleted(Job $job)
    {
        $job->status = JobStatus::JobCompleted;
        $job->save();
        $response = [
          'status' => 'Marked as completed successfully',
        ];

        return response($response, 200);
    }*/

    public function updateJobStatus(Request $request)
    {
        $request->validate([
            'job_status' => ['required', new EnumValue(JobStatus::class)],
            'job_id' =>  'required'
        ]);

        $job = Job::findOrFail($request->job_id);
        $job->status = $request->job_status;

        if($job->save()){
            return response(['status' => 'Job status updated', 200]);
        }else{
            return response(['status' => 'Error occurred!'], 200);
        }
    }

    public function getCities()
    {
        $cities = Job::getCities();

        return response(['cities' => $cities], 200);
    }


}
