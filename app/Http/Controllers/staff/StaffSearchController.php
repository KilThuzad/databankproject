<?php

namespace App\Http\Controllers\staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ResearchProject;
use App\Models\User;
use App\Models\University;
use App\Models\Category;

class StaffSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->query('query');

        $projects = ResearchProject::where('title', 'like', "%$query%")
            ->orWhere('description', 'like', "%$query%")
            ->get();

        if (    
            $projects->isEmpty()
        ) {
            return view('project.staff.search.search_result', compact(
                'query', 'projects'
            ))->with('empty', true);
        }

        return view('project.staff.search.search_result', compact('query', 'projects'));
    }
}
