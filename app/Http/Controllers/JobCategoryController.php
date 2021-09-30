<?php

namespace App\Http\Controllers;

use App\Models\JobCategory;
use Illuminate\Http\Request;

class JobCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin'])->only(['store', 'destroy']);
    }

    public function index()
    {
        return JobCategory::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = null;
        if($request->job_category_id > 0){
            $category = JobCategory::find($request->job_category_id);
        }else{
            $category = new JobCategory();
        }

        $category->name = $request->name;
        $category->save();


        return $category;
    }

    public function destroy(JobCategory $category)
    {
       return $category->delete();
    }
}
