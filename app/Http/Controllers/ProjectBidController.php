<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectBid;
use Illuminate\Http\Request;

class ProjectBidController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth'])->only(['store', 'destroy']);
    }

    public function index()
    {
        return ProjectBid::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'offered_amount' => 'required|digits_between:1,10000000',
            'details' => 'required',
            'project_id' => 'required|numeric|min:1',
            'project_bid_id' => 'numeric|min:1'                 // in case of edit
        ]);

        $project = Project::find($request->project_id);

        // TODO: restrict user from creating multiple bids on same project

        $bid = null;

        if($request->project_bid_id){
            // In case of edit
            $bid = ProjectBid::find($request->project_bid_id);
        }else{
            $bid = new ProjectBid();
        }

        $bid->project()->associate($project);

        $bid->offered_amount = $request->offered_amount;
        $bid->details = $request->details;

        $bid->offeredBy()->associate(auth()->user());

        $bid->save();

        return response(['info' => 'Bid added successfully!'], 200);

    }

    public function destroy(ProjectBid $bid)
    {
        $bid->delete();
        return response(['info' => 'Bid deleted successfully'], 200);
    }

    public function projectBids(Project $project)
    {
        return $project->bids()->with('offeredBy')->get();
    }


}
