<?php

namespace App\Http\Controllers\staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ResearchProject;
use App\Models\MemberAgency;

class StaffDashboardController extends Controller
{
    public function index(Request $request)
    {
        $memberAgencyId = $request->input('member_agencies_id'); // updated
        $category       = $request->input('category');

        $baseQuery = ResearchProject::query();

        if ($memberAgencyId) {
            $baseQuery->whereHas('submittedBy', function ($q) use ($memberAgencyId) {
                $q->where('member_agencies_id', $memberAgencyId); // updated field
            });
        }


        if ($category) {
            $baseQuery->where('category', $category);
        }

        $totalProjects = (clone $baseQuery)->count();

        $totalPending = (clone $baseQuery)
            ->where('is_staff_approved', 'pending')
            ->count();

        $totalApproved = (clone $baseQuery)
            ->where('is_staff_approved', 'approved')
            ->count();

        $approvedPercentage = $totalProjects > 0
            ? round(($totalApproved / $totalProjects) * 100, 2)
            : 0;

        $pendingPercentage = $totalProjects > 0
            ? round(($totalPending / $totalProjects) * 100, 2)
            : 0;

        $projectsByCategory = (clone $baseQuery)
            ->selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->get();


        $recentProjects = (clone $baseQuery)
            ->with('submittedBy') 
            ->latest()
            ->take(5)
            ->get();

        $memberAgencies = MemberAgency::orderBy('name')->get();

        $categories = ResearchProject::select('category')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('project.staff.dashboard.dashboard', compact(
            'totalProjects',
            'totalPending',
            'totalApproved',
            'pendingPercentage',
            'approvedPercentage',
            'projectsByCategory',
            'recentProjects',
            'memberAgencies', 
            'categories',
            'memberAgencyId', 
            'category'
        ));
    }
}
