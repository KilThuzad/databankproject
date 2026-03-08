<?php

namespace App\Http\Controllers\reviewer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReviewSubmittedMail;
use App\Helpers\ActivityLogger;
use App\Models\ResearchProject;
use App\Models\User;
use App\Models\ProjectTeam;
use App\Models\ProjectComment;
use App\Models\MemberAgency;
use App\Models\ProjectReview;

class ReviewerResearchesController extends Controller
{
    public function index(Request $request)
    {
        ActivityLogger::log(
            'viewed',
            'ReviewAssignment',
            null,
            'Reviewer viewed assigned projects list'
        );

        $reviewerId = auth()->id();
        $memberAgencyId = $request->input('member_agencies_id');
        $category = $request->input('category');

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
                DB::raw("CONCAT(u.firstname, ' ', u.lastname) as submitter_name"),
                'ma.name as agency_name'
            );

        if ($memberAgencyId) {
            $assignedProjectsQuery->where('u.member_agencies_id', $memberAgencyId);
        }
        if ($category) {
            $assignedProjectsQuery->where('rp.category', $category);
        }

        $assignedProjects = $assignedProjectsQuery->orderBy('ra.assigned_at', 'desc')->get();

        $memberAgencies = MemberAgency::orderBy('name')->get();
        $categories = DB::table('research_projects')->select('category')->distinct()->pluck('category');

        return view('project.reviewer.researches.index', compact(
            'assignedProjects',
            'memberAgencies',
            'memberAgencyId',
            'categories',
            'category'
        ));
    }

    public function show($id)
    {
        $project = ResearchProject::with([
            'user.memberAgency',
            'team' => function ($q) {
                $q->select('users.id', 'username', 'firstname', 'lastname', 'email');
            },
            'comments.user',
            'reviews'
        ])->findOrFail($id);

        $assignedUserIds = ProjectTeam::pluck('user_id')->toArray();

        $availableMembers = User::select('id', 'firstname', 'lastname', 'email', 'role')
            ->whereIn('role', ['researcher', 'reviewer'])
            ->whereNotIn('id', $assignedUserIds)
            ->orderBy('firstname')
            ->get();

        ActivityLogger::log(
            'viewed',
            'ResearchProject',
            $id, 
            'viewed project ' . $project->title
        );

        return view('project.reviewer.researches.project-details', compact('project', 'availableMembers'));
    }

    public function updateStatus(Request $request, ResearchProject $project)
    {
        $request->validate([
            'status' => 'required|in:pending_review,in_review,needs_revision,complete,approved',
            'status_notes' => 'nullable|string|max:500',
        ]);

        $oldStatus = $project->status;
        $newStatus = $request->status;

        $project->update([
            'status' => $newStatus,
            'status_notes' => $request->status_notes,
        ]);

        $project->comments()->create([
            'user_id' => auth()->id(),
            'comment' => "Status changed from " . ucfirst(str_replace('_', ' ', $oldStatus)) .
                         " to " . ucfirst(str_replace('_', ' ', $newStatus)) .
                         ($request->status_notes ? "\n\nNotes: " . $request->status_notes : ''),
        ]);

        ActivityLogger::log(
            'updated',
            'ResearchProject',
            $project->id, 
            'updated project status from "' . ucfirst(str_replace('_', ' ', $oldStatus)) . 
            '" to "' . ucfirst(str_replace('_', ' ', $newStatus)) . '"' .
            ($request->status_notes ? '. Notes: ' . $request->status_notes : '')
        );

        return redirect()->back()->with('success', "✅ Project status updated to " . ucfirst(str_replace('_', ' ', $newStatus)));
    }

    public function storeComment(Request $request, $projectId)
    {
        $request->validate([
            'comment' => 'required|string|max:2000'
        ]);

        $project = ResearchProject::findOrFail($projectId);
        $comment = ProjectComment::create([
            'project_id' => $projectId,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        ActivityLogger::log(
            'added',
            'ProjectComment',
            $comment->id,
            'added comment on project ' . $project->title
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully.'
            ]);
        }

        return back()->with('success', 'Comment added successfully.');
    }


    public function updateComment(Request $request, $projectId, $commentId)
    {
        if ($request->isJson()) {
            $request->validate([
                'comment' => 'required|string|max:1000',
            ]);
            $commentText = $request->input('comment');
        } else {
            $request->validate([
                'comment' => 'required|string|max:1000',
            ]);
            $commentText = $request->comment;
        }

        $comment = ProjectComment::where('id', $commentId)
                                ->where('user_id', auth()->id())
                                ->firstOrFail();

        if (!$comment) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comment not found.'
                ], 404);
            }
            return back()->with('error', 'Comment not found.');
        }

        $project = ResearchProject::findOrFail($projectId);
        $oldComment = $comment->comment;
        
        $comment->update(['comment' => $commentText]);

        ActivityLogger::log(
            'updated',
            'ProjectComment',
            $comment->id,
            'updated comment on project ' . $project->title
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment updated successfully.'
            ]);
        }

        return back()->with('success', 'Comment updated successfully.');
    }

    public function destroyComment(Request $request, $projectId, $commentId)
    {
        $project = ResearchProject::findOrFail($projectId);
        $comment = $project->comments()->findOrFail($commentId);

        if ($comment->user_id !== auth()->id()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }
            
            return back()->with('error', 'Unauthorized action.');
        }

        $deletedComment = $comment->comment;
        $comment->delete();

        ActivityLogger::log(
            'deleted',
            'ProjectComment',
            $commentId,
            'deleted comment on project ' . $project->title
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully.'
            ]);
        }

        return back()->with('success', 'Comment deleted successfully.');
    }

    public function submitReview(Request $request, $project)
    {
        $projectId = $project;
        
        $request->validate([
            'score_originality'   => 'required|numeric|min:1|max:5',
            'score_methodology'   => 'required|numeric|min:1|max:5',
            'score_contribution'  => 'required|numeric|min:1|max:5',
            'score_clarity'       => 'required|numeric|min:1|max:5',
            'overall_score'       => 'required|numeric|min:1|max:5',
            'recommendation'      => 'required|in:accept,revision,reject',
            'comments'            => 'nullable|string|max:2000',
            'is_confidential'     => 'nullable|boolean',
        ]);

        $isAssigned = DB::table('review_assignments')
            ->where('project_id', $projectId)
            ->where('reviewer_id', auth()->id())
            ->exists();

        if (!$isAssigned) {
            ActivityLogger::log(
                'attempted unauthorized review',
                'ProjectReview',
                null,
                'attempted to submit review for project ' . $project->title . ' without assignment'
            );
            
            return back()->with('error', 'You are not assigned to review this project.');
        }

        $projectModel = ResearchProject::findOrFail($projectId);
        
        $review = ProjectReview::create([
            'project_id'          => $projectId,
            'reviewer_id'         => auth()->id(),
            'score_originality'   => $request->score_originality,
            'score_methodology'   => $request->score_methodology,
            'score_contribution'  => $request->score_contribution,
            'score_clarity'       => $request->score_clarity,
            'overall_score'       => $request->overall_score,
            'comments'            => $request->comments,
            'recommendation'      => $request->recommendation,
            'is_confidential'     => $request->has('is_confidential') ? 1 : 0,
            'submitted_at'        => now(),
        ]);

        DB::table('review_assignments')
            ->where('project_id', $projectId)
            ->where('reviewer_id', auth()->id())
            ->update(['status' => 'completed']);


        $averageScore = ProjectReview::where('project_id', $projectId)
            ->avg('overall_score');

        if ($averageScore >= 4.0) {
            $projectModel->status = 'complete';
        } else {
            $projectModel->status = 'needs_revision';
        }

        $projectModel->save();

        $projectOwner = $projectModel->user;

        if ($projectOwner && $projectOwner->email) {
            Mail::to($projectOwner->email)
                ->send(new ReviewSubmittedMail($projectModel, $review));
        }    

        ActivityLogger::log(
            'submitted',
            'ProjectReview',
            $review->id,
            'submitted review for project: '
        );

        return back()->with('success', '✅ Review submitted successfully.');
    }

    public function uploadFile(Request $request, $id)
    {
        $request->validate([
            'project_file' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $project = ResearchProject::findOrFail($id);

        if ($request->hasFile('project_file')) {
            $oldFilePath = $project->file_path;
            $oldFileName = $oldFilePath ? basename($oldFilePath) : 'No previous file';

            if ($oldFilePath && file_exists(storage_path('app/public/' . $oldFilePath))) {
                unlink(storage_path('app/public/' . $oldFilePath));
            }

            $file = $request->file('project_file');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('research_files', $filename, 'public');

            $project->file_path = $path;
            $project->updated_at = now();
            $project->save();

            ActivityLogger::log(
                'uploaded',
                'ResearchProject',
                $project->id,
                'uploaded/replaced file for project ' . $project->title
            );

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Project file uploaded successfully!'
                ]);
            }

            return back()->with('success', 'Project file uploaded successfully!');
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'No file selected.'
            ]);
        }

        return back()->with('error', 'No file selected.');
    }
}