<?php

namespace App\Http\Controllers\researcher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ResearchProject;
use App\Models\User;
use App\Models\University;
use App\Models\Category;    

class UserSearchController extends Controller
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
            return view('project.researcher.search.search_result', compact(
                'query', 'projects'
            ))->with('empty', true);
        }

        return view('project.researcher.search.search_result', compact('query', 'projects'));
    }
}
