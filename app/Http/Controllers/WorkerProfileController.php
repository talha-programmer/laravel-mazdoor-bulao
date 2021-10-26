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
        $profile = auth()->user()->workerProfile()->with('skills')->first();
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


}
