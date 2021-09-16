<?php

namespace App\Http\Controllers;

use App\Models\ProjectCategory;
use Illuminate\Http\Request;

class ProjectCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin'])->only(['store', 'destroy']);
    }

    public function index()
    {
        return ProjectCategory::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = null;
        if($request->project_category_id > 0){
            $category = ProjectCategory::find($request->project_category_id);
        }else{
            $category = new ProjectCategory();
        }

        $category->name = $request->name;
        $category->save();


        return $category;
    }

    public function destroy(ProjectCategory $category)
    {
       return $category->delete();
    }
}
