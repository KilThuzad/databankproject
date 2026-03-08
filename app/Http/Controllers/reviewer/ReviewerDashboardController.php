<?php

namespace App\Http\Controllers\reviewer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ResearchProject;
use App\Models\User;
use App\Models\ProjectReview;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReviewerDashboardController extends Controller
{
    public function showDashboard(Request $request)
    {
        $category = $request->input('category');
        $memberAgencyId = $request->input('member_agencies_id'); 
        $reviewerId = auth()->id();


        $assignedProjectsQuery = DB::table('review_assignments as ra')
            ->join('research_projects as rp', 'ra.project_id', '=', 'rp.id')
            ->join('users as u', 'rp.submitted_by', '=', 'u.id')
            ->leftJoin('member_agencies as ma', 'u.member_agencies_id', '=', 'ma.id')
            ->where('ra.reviewer_id', $reviewerId)
            ->select(
                'ra.id as assignment_id',
                'ra.status as assignment_status',
                'ra.deadline as assignment_deadline',
                'ra.assigned_at',
                'rp.id as project_id',
                'rp.title',
                'rp.category',
                'rp.status as project_status',
                'rp.created_at as submitted_at',
                DB::raw("CONCAT(u.firstname, ' ', u.lastname) as submitter_name"),
                'ma.name as agency_name'
            );

        if ($category) {
            $assignedProjectsQuery->where('rp.category', $category);
        }
        if ($memberAgencyId) {
            $assignedProjectsQuery->where('u.member_agencies_id', $memberAgencyId);
        }

        $assignedProjects = $assignedProjectsQuery->orderBy('ra.assigned_at', 'desc')->get();

        $stats = [
            'total_projects' => $assignedProjects->count(),
            'approved_projects' => $assignedProjects->where('project_status', 'approved')->count(),
            'pending_projects' => $assignedProjects->whereIn('project_status', ['pending', 'submitted'])->count(),
            'average_score' => ProjectReview::where('reviewer_id', $reviewerId)->avg('overall_score') ?? 0,
            'total_reviews' => ProjectReview::where('reviewer_id', $reviewerId)->count(),
            'reviews_this_month' => ProjectReview::where('reviewer_id', $reviewerId)
                ->whereMonth('submitted_at', now()->month)
                ->count(),
            'projects_by_category' => $assignedProjects
                ->groupBy('category')
                ->map->count()
                ->toArray(),
            'score_distribution' => ProjectReview::where('reviewer_id', $reviewerId)
                ->select(
                    DB::raw('CASE 
                        WHEN overall_score >= 9 THEN "9-10"
                        WHEN overall_score >= 7 THEN "7-8.9"
                        WHEN overall_score >= 5 THEN "5-6.9"
                        ELSE "Below 5"
                    END as score_range'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('score_range')
                ->pluck('count', 'score_range')
                ->toArray()
        ];

        $recentReviews = ProjectReview::with(['project'])
            ->where('reviewer_id', $reviewerId)
            ->when($category, fn($q) => $q->whereHas('project', fn($q2) => $q2->where('category', $category)))
            ->when($memberAgencyId, fn($q) => $q->whereHas('project.user', fn($q2) => $q2->where('member_agencies_id', $memberAgencyId)))
            ->orderBy('submitted_at', 'desc')
            ->limit(5)
            ->get();

        $categories = DB::table('research_projects')->select('category')->distinct()->pluck('category');
        $memberAgencies = DB::table('member_agencies')->orderBy('name')->get();

        return view('project.reviewer.dashboard.dashboard', compact(
            'assignedProjects',
            'recentReviews',
            'stats',
            'categories',
            'category',
            'memberAgencies',
            'memberAgencyId'
        ));
    }

    public function getProjectDetails($id)
    {
        $project = ResearchProject::with(['reviews', 'reviews.reviewer', 'user.memberAgency'])->findOrFail($id);

        return response()->json([
            'project' => $project,
            'reviews' => $project->reviews,
            'statistics' => [
                'average_score' => $project->reviews->avg('overall_score'),
                'total_reviews' => $project->reviews->count(),
                'recommendation_summary' => $project->reviews->groupBy('recommendation')->map->count()
            ]
        ]);
    }

    public function getAllProject(Request $request)
    {
        $memberAgencyId = $request->input('member_agencies_id');
        $reviewerId = auth()->id(); 

        $projectsQuery = ResearchProject::with(['user.memberAgency'])
            ->whereIn('id', function($query) use ($reviewerId) {
                $query->select('project_id')
                      ->from('review_assignments')
                      ->where('reviewer_id', $reviewerId)
                      ->whereIn('status', ['assigned', 'in_review']); 
            });

        if ($memberAgencyId) {
            $projectsQuery->whereHas('user', fn($q) => $q->where('member_agencies_id', $memberAgencyId));
        }

        $projects = $projectsQuery->latest()->take(5)->get();
        $totalProjects = $projectsQuery->count();

        return view('project.reviewer.dashboard.dashboard', compact(
            'projects',
            'totalProjects',
            'memberAgencyId',
        ));
    }
}
