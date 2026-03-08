<?php

namespace App\Http\Controllers\databank;

use App\Http\Controllers\Controller;
use App\Models\ResearchProject;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function showDashboard()
    {
        return view('project.dashboard.dashboard');
    }

    public function getAllProject(Request $request)
    {
        $memberAgencyId = $request->input('member_agencies_id');
        $category       = $request->input('category'); 

        $projectsQuery = ResearchProject::with('user');

        if ($memberAgencyId) {
            $projectsQuery->whereHas('user', function ($query) use ($memberAgencyId) {
                $query->where('member_agencies_id', $memberAgencyId);
            });
        }

        if ($category) {
            $projectsQuery->where('category', $category);
        }

        $recentProjects = $projectsQuery->clone()->latest()->take(5)->get();

        $totalProjects = $projectsQuery->clone()->count();

        $projectsByStatus = $projectsQuery->clone()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $projectsByCategory = $projectsQuery->clone()
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category');

        $accountsQuery = User::query();
        if ($memberAgencyId) {
            $accountsQuery->where('member_agencies_id', $memberAgencyId);
        }
        $totalAccounts = $accountsQuery->count();
        $accountsByRole = $accountsQuery->select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->pluck('count', 'role');

        $events = DB::table('events')
            ->select('title', 'event_date')
            ->get();

        $memberAgencies = DB::table('member_agencies')->orderBy('name')->get();
        $categories     = ResearchProject::select('category')->distinct()->pluck('category');

        return view('project.dashboard.dashboard', compact(
            'events',
            'recentProjects',
            'totalAccounts',
            'totalProjects',
            'accountsByRole',
            'projectsByStatus',
            'projectsByCategory',
            'memberAgencies',
            'memberAgencyId',
            'categories',
            'category'
        ));
    }
}