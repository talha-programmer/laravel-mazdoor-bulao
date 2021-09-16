<?php

namespace App\Http\Controllers;

use App\Models\Project;

use Illuminate\Http\Request;


class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['store', 'destroy']);
    }

    public function index()
    {
        $projects = Project::all();

        return $projects->toArray();

    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'details' => 'required'
        ]);

        $title = $request->title;
        $details = $request->details;

        $project = new Project([
            'title' => $title,
            'details' => $details
        ]);

        $project->postedBy()->associate(auth()->user());
        $project->save();

        return response(['info' => 'Project has been created successfully!']);
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return response(['info' => 'Project deleted successfully']);
    }

    public function singleProject(Project $project)
    {
        $response = [
          'project' => $project->load(['postedBy', 'categories', 'bids', 'orders']),
        ];

        return response($response, 200);
    }


}
