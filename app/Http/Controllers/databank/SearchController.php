<?php

namespace App\Http\Controllers\databank;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ResearchProject;
use App\Models\User;
use App\Models\University;
use App\Models\Category;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->query('query');

        $projects = ResearchProject::where('title', 'like', "%$query%")
            ->orWhere('description', 'like', "%$query%")
            ->get();

        $users = User::where('firstname', 'like', "%$query%")
            ->orWhere('lastname', 'like', "%$query%")
            ->orWhere('email', 'like', "%$query%")
            ->get();

        $universities = University::where('name', 'like', "%$query%")
            ->orWhere('address', 'like', "%$query%")
            ->get();

        $categories = Category::where('name', 'like', "%$query%")
            ->get();

        if (
            $projects->isEmpty() &&
            $users->isEmpty() &&
            $universities->isEmpty() &&
            $categories->isEmpty()
        ) {
            return view('project.search.search_result', compact(
                'query', 'projects', 'users', 'universities', 'categories'
            ))->with('empty', true);
        }

        return view('project.search.search_result', compact('query', 'projects', 'users', 'universities', 'categories'));
    }
}
