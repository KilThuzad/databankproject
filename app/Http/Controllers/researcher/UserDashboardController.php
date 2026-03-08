<?php

namespace App\Http\Controllers\researcher;

use App\Http\Controllers\Controller;
use App\Models\ResearchProject;
use Illuminate\Support\Facades\DB;

class UserDashboardController extends Controller
{
    public function showDashboard()
    {
        $user = auth()->user();

        $events = DB::table('events')
            ->select('title', 'event_date')
            ->get();

        $projects = ResearchProject::with(['category', 'user'])
            ->where('submitted_by', $user->id)
            ->orWhereHas('team', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->get();

        $totalProjects = $projects->count();

        $projectsByStatus = ResearchProject::where('submitted_by', $user->id)
            ->orWhereHas('team', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $recentProjects = ResearchProject::with(['category','user'])
            ->where(function ($q) use ($user) {
                $q->where('submitted_by', $user->id)
                ->orWhereHas('team', function ($q2) use ($user) {
                    $q2->where('users.id', $user->id); 
                });
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $stats = [
            'total'     => $totalProjects,
            'pending'   => $projectsByStatus['pending_review'] ?? 0,
            'revision'  => $projectsByStatus['needs_revision'] ?? 0,
            'completed' => $projectsByStatus['complete'] ?? 0,
        ];

        return view('project.researcher.dashboard.dashboard', compact(
            'events', 
            'projects', 
            'totalProjects', 
            'projectsByStatus', 
            'recentProjects', 
            'stats'
        ));
    }

}
