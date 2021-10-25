<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class WorkerProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $profile = auth()->user()->workerProfile;
        return response(['worker_profile' => $profile], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required',
            'is_edit' => 'boolean'
        ]);

        $user = auth()->user();
        $user->workerProfile()->create([
            'description' => $request->description,
        ]);

        return response(['status' => 'Profile created successfully!'], 200);
    }


}
