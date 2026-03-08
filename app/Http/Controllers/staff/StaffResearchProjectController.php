<?php

namespace App\Http\Controllers\staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ProjectApprovedMail;
use App\Mail\ReviewerAssignedMail;
use App\Models\ResearchProject;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Category;
use App\Models\ProjectTeam;
use App\Models\MemberAgency;
use App\Models\ReviewAssignment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityLogger;


class StaffResearchProjectController extends Controller
{
    public function index(Request $request)
    {
        $memberAgencyId = $request->input('member_agencies_id'); 
        $category       = $request->input('category');

        $projectsQuery = ResearchProject::with(['category', 'user.memberAgency']);  
        if ($memberAgencyId) {
            $projectsQuery->whereHas('user', function ($query) use ($memberAgencyId) {
                $query->where('member_agencies_id', $memberAgencyId); 
            });
        }

        if ($category) {
            $projectsQuery->where('category', $category);
        }

        $projects = $projectsQuery->latest()->get();

        $memberAgencies = MemberAgency::orderBy('name')->get(); // updated

        $projectsByStatusQuery = ResearchProject::query();
        if ($memberAgencyId) {
            $projectsByStatusQuery->whereHas('user', function ($query) use ($memberAgencyId) {
                $query->where('member_agencies_id', $memberAgencyId);
            });
        }
        if ($category) {
            $projectsByStatusQuery->where('category', $category);
        }
        $projectsByStatus = $projectsByStatusQuery
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $Projectcategories = ResearchProject::select('category')
            ->distinct()
            ->pluck('category');

        $projectsByCategoryQuery = ResearchProject::query();
        if ($memberAgencyId) {
            $projectsByCategoryQuery->whereHas('user', function ($query) use ($memberAgencyId) {
                $query->where('member_agencies_id', $memberAgencyId);
            });
        }
        if ($category) {
            $projectsByCategoryQuery->where('category', $category);
        }
        $projectsByCategory = $projectsByCategoryQuery
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category');

        return view('project.staff.research-project.index', compact(
            'projects',
            'memberAgencies',     
            'memberAgencyId',     
            'projectsByStatus',
            'projectsByCategory',
            'Projectcategories',
            'category'
        ));
    }

    public function pending(Request $request)
    {
        $memberAgencyId = $request->input('member_agencies_id');
        $category       = $request->input('category');

        $projectsQuery = ResearchProject::with(['category', 'user.memberAgency'])
            ->where('is_staff_approved', 'pending');

        if ($memberAgencyId) $projectsQuery->whereHas('user', fn($q) => $q->where('member_agencies_id', $memberAgencyId));
        if ($category) $projectsQuery->where('category', $category);

        $projects = $projectsQuery->latest()->get();

        $memberAgencies = MemberAgency::orderBy('name')->get();
        $Projectcategories = ResearchProject::select('category')->distinct()->pluck('category');

        $projectsByStatusQuery = ResearchProject::query();
        if ($memberAgencyId) $projectsByStatusQuery->whereHas('user', fn($q) => $q->where('member_agencies_id', $memberAgencyId));
        if ($category) $projectsByStatusQuery->where('category', $category);

        $projectsByStatus = $projectsByStatusQuery
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $projectsByCategoryQuery = ResearchProject::query();
        if ($memberAgencyId) $projectsByCategoryQuery->whereHas('user', fn($q) => $q->where('member_agencies_id', $memberAgencyId));
        if ($category) $projectsByCategoryQuery->where('category', $category);

        $projectsByCategory = $projectsByCategoryQuery
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category');

        return view('project.staff.research-project.status.pending', compact(
            'projects',
            'memberAgencies',
            'memberAgencyId',
            'projectsByStatus',
            'projectsByCategory',
            'Projectcategories',
            'category'
        ));
    }

    public function approved(Request $request)
    {
        $memberAgencyId = $request->input('member_agencies_id');
        $category       = $request->input('category');

        $projectsQuery = ResearchProject::with(['category', 'user.memberAgency'])
            ->where('is_staff_approved', 'approved');

        if ($memberAgencyId) $projectsQuery->whereHas('user', fn($q) => $q->where('member_agencies_id', $memberAgencyId));
        if ($category) $projectsQuery->where('category', $category);

        $projects = $projectsQuery->latest()->get();

        $memberAgencies = MemberAgency::orderBy('name')->get();
        $Projectcategories = ResearchProject::select('category')->distinct()->pluck('category');

        $projectsByStatusQuery = ResearchProject::query();
        if ($memberAgencyId) $projectsByStatusQuery->whereHas('user', fn($q) => $q->where('member_agencies_id', $memberAgencyId));
        if ($category) $projectsByStatusQuery->where('category', $category);

        $projectsByStatus = $projectsByStatusQuery
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $projectsByCategoryQuery = ResearchProject::query();
        if ($memberAgencyId) $projectsByCategoryQuery->whereHas('user', fn($q) => $q->where('member_agencies_id', $memberAgencyId));
        if ($category) $projectsByCategoryQuery->where('category', $category);

        $projectsByCategory = $projectsByCategoryQuery
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category');

        return view('project.staff.research-project.status.approved', compact(
            'projects',
            'memberAgencies',
            'memberAgencyId',
            'projectsByStatus',
            'projectsByCategory',
            'Projectcategories',
            'category'
        ));
    }


    public function updateStatusFile(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:research_projects,id',
            'status' => 'required|in:pending_review,needs_revision,complete',
            'file' => 'nullable|file|mimes:pdf,doc,docx,zip|max:10240',
        ]);

        $project = ResearchProject::findOrFail($request->project_id);

        $project->status = $request->status;

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            if ($project->file_path && Storage::disk('public')->exists($project->file_path)) {
                Storage::disk('public')->delete($project->file_path);
            }

            $filePath = $file->store('research_files', 'public');

            $project->file_path = $filePath;
        }

        $project->save();

        ActivityLogger::log(
            'Uploaded project file',
            'ResearchProject',
            $project->id,
            'Status changed to ' . $project->status
        );

        return redirect()->back()->with('success', 'Project updated successfully!');
    }


    public function update(Request $request, $id)
    {
        $project = ResearchProject::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending_review,needs_revision,complete',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:20480',
        ]);

        $data = $request->only('title', 'description', 'category', 'status');

        if ($request->hasFile('file')) {

            if ($project->file_path && Storage::disk('public')->exists($project->file_path)) {
                Storage::disk('public')->delete($project->file_path);
            }


            $filePath = $request->file('file')->store('research_files', 'public');
            $data['file_path'] = $filePath;
        }

        $project->update($data);

        return redirect()->route('staffresearchproject.index')->with('success', 'Successfully updated the project!');
    }


    public function show($id, Request $request)
    {
        $project = ResearchProject::with([
            'category',
            'user',
            'team' => function ($q) {
                $q->select('users.id', 'username', 'firstname', 'lastname', 'email');
            }
        ])->findOrFail($id);


        $assignedUserIds = ProjectTeam::pluck('user_id')->toArray();


        $availableMembers = User::select('id', 'firstname', 'lastname', 'email', 'role')
            ->whereIn('role', ['researcher'])
            ->whereNotIn('id', $assignedUserIds)
            ->orderBy('firstname')
            ->get();

        return view(
            'project.staff.research-project.project-details',
            compact('project', 'availableMembers')
        );
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,declined',
        ]);

        $project = ResearchProject::findOrFail($id);
        $project->is_staff_approved = $request->status;
        $project->save();

        if ($request->status === 'approved') {
            Mail::to($project->user->email)
                ->send(new ProjectApprovedMail($project));
        }

        return redirect()->back()->with('success', 'Project status updated successfully!');
    }


    public function setDeadlineByProject(Request $request)
    {
        $request->validate([
            'projects' => 'required|array',
            'deadline' => 'required|date',
        ]);

        ResearchProject::whereIn('id', $request->projects)
            ->update([
                'deadline' => $request->deadline
            ]);

        return back()->with('success', 'Deadlines updated for selected projects.');
    }


    public function download($id)
    {
        $project = ResearchProject::findOrFail($id);

        if (!Storage::disk('public')->exists($project->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download($project->file_path);
    }

    public function viewFile($filename)
    {
        $path = 'research_files/' . $filename;

        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $fullPath = Storage::disk('public')->path($path);
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $pdfFormats = ['pdf'];
        $officeFormats = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];

        $publicUrl = asset('storage/research_files/' . $filename);

        if (in_array($extension, $pdfFormats)) {
            return view('project.research-project.syncfusion-viewer', [
                'fileUrl' => $publicUrl,
                'type' => 'pdf'
            ]);
        } elseif (in_array($extension, $officeFormats)) {
            return view('project.research-project.syncfusion-viewer', [
                'fileUrl' => $publicUrl,
                'type' => 'office',
                'extension' => $extension
            ]);
        } else {
            return response()->file($fullPath, [
                'Content-Type' => Storage::disk('public')->mimeType($path),
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]);
        }
    }

    public function assignReviewerForm(ResearchProject $project)
    {
        $reviewers = User::where('role', 'reviewer')
            ->orderBy('firstname')
            ->orderBy('lastname')
            ->get();


        return view(
            'project.staff.ReviewAssignment.assign_reviewer',
            compact('project', 'reviewers')
        );
    }


    public function storeReviewerAssignment(Request $request, ResearchProject $project)
    {
        $request->validate([
            'reviewer_ids' => 'required|array|min:1',
            'reviewer_ids.*' => 'exists:users,id',
            'deadline' => 'required|date|after:today',
        ], [
            'deadline.after' => 'The selected deadline has already passed. Please choose a future date.',
        ]);

        $assignedReviewers = [];

        foreach ($request->reviewer_ids as $reviewerId) {

            $exists = ReviewAssignment::where('project_id', $project->id)
                ->where('reviewer_id', $reviewerId)
                ->exists();

            if ($exists) {
                continue;
            }

            ReviewAssignment::create([
                'project_id' => $project->id,
                'reviewer_id' => $reviewerId,
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
                'deadline' => $request->deadline,
                'status' => 'in_progress',
            ]);

            $reviewer = User::find($reviewerId);
            $assignedReviewers[] = $reviewer->firstname . ' ' . $reviewer->lastname;
        }

        $oldStatus = $project->is_staff_approved;

        $project->update([
            'is_staff_approved' => 'in_progress'
        ]);

        Mail::to($project->user->email)->send(
            new ReviewerAssignedMail($project, $assignedReviewers, $request->deadline)
        );

        ActivityLogger::log(
            'assigned reviewers',
            'ResearchProject',
            $project->id,
            'assigned a reviewer to project ' . $project->title 
        );

        return redirect()
            ->route('staffresearchproject.show', $project->id)
            ->with('success', 'Reviewer(s) assigned successfully.');
    }

    public function updateApprovalStatus(Request $request, $id)
    {
        $project = ResearchProject::with('user')->findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:approved,declined,pending'
        ]);
        
        $project->update([
            'is_staff_approved' => $validated['status']
        ]);

        if ($validated['status'] === 'approved' && $project->user && $project->user->email) {
            try {
                Mail::to($project->user->email)->queue(new ProjectApprovedMail($project));
                
                Log::info('Project approval email queued for project ID: ' . $project->id);
            } catch (\Exception $e) {
                Log::error('Failed to send approval email: ' . $e->getMessage());
            }
        } else {
            Log::info('Approval email not sent for project ID: ' . $project->id . ' – Reason: ' . 
                ($validated['status'] !== 'approved' ? 'status not approved' : 'user or email missing'));
        }

        ActivityLogger::log(
            'Updated project status via AJAX',
            'ResearchProject',
            $project->id,
            $validated['status'] . ' project ' . $project->title
        );

        session()->flash('success', 'Project approval status updated successfully.');

        return response()->json([
            'success' => true,
            'message' => 'Project approval status updated successfully'
        ]);
    }


    public function uploadDocument(Request $request, $id)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $project = ResearchProject::findOrFail($id);

        if ($project->file_path && Storage::disk('public')->exists($project->file_path)) {
            Storage::disk('public')->delete($project->file_path);
        }

        $path = $request->file('document')->store('research_files', 'public');

        $project->file_path = $path;
        $project->save();

        if ($request->has('notify_team') && $request->notify_team == '1') {
        
        }

        return back()->with('success', 'Document updated successfully!');
    }

}
