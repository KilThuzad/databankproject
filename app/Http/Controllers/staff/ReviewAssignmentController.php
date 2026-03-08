<?php

namespace App\Http\Controllers\staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ResearchProject;
use App\Models\User;
use App\Models\ReviewAssignment;
use App\Models\ProjectReview;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityLogger;

class ReviewAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $projects = ResearchProject::orderBy('title')->get();

        $projectReviews = ProjectReview::all();

        $reviewers = User::where('role', 'reviewer')
            ->orderBy('firstname')
            ->orderBy('lastname')
            ->get();

        $assignments = ReviewAssignment::with([
                'project.user.memberAgency',
                'reviewer'
            ])
            ->when($request->member_agencies_id, function ($q) use ($request) {
                $q->whereHas('project.user', function ($q2) use ($request) {
                    $q2->where('member_agencies_id', $request->member_agencies_id);
                });
            })
            ->when($request->category, function ($q) use ($request) {
                $q->whereHas('project', function ($q2) use ($request) {
                    $q2->where('category', $request->category);
                });
            })
            ->latest('assigned_at')
            ->paginate(10);

        return view(
            'project.staff.ReviewAssignment.index',
            compact('assignments', 'projects', 'reviewers', 'projectReviews')
        );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'reviewer_id' => 'required|exists:users,id',
            'deadline' => 'required|date|after_or_equal:today',
            'status' => 'sometimes|in:assigned,in_progress,completed'
        ]);

        try {
            DB::beginTransaction();

            $assignment = ReviewAssignment::findOrFail($id);

            $oldReviewer = $assignment->reviewer_id;
            $oldDeadline = $assignment->deadline;
            $oldStatus = $assignment->status;

            // Prevent duplicate reviewer for the same project
            $exists = ReviewAssignment::where('project_id', $assignment->project_id)
                ->where('reviewer_id', $request->reviewer_id)
                ->where('id', '!=', $assignment->id)
                ->exists();

            if ($exists) {
                return back()->with('error', 'This reviewer is already assigned to this project.');
            }

            // Prepare update data
            $updateData = [
                'reviewer_id' => $request->reviewer_id,
                'deadline' => $request->deadline,
            ];

            // Add status if provided
            if ($request->has('status')) {
                $updateData['status'] = $request->status;
            }

            $assignment->update($updateData);

            // Get reviewer names for logging
            $oldReviewerName = User::find($oldReviewer)->fullName ?? "User ID: {$oldReviewer}";
            $newReviewerName = User::find($request->reviewer_id)->fullName ?? "User ID: {$request->reviewer_id}";

            // Build description for logging
            $description = "Updated reviewer assignment:\n";
            $description .= "- Reviewer: {$oldReviewerName} → {$newReviewerName}\n";
            $description .= "- Deadline: {$oldDeadline} → {$request->deadline}";
            
            if ($request->has('status') && $oldStatus !== $request->status) {
                $description .= "\n- Status: {$oldStatus} → {$request->status}";
            }

            ActivityLogger::log(
                'updated',
                'ReviewAssignment',
                $assignment->id,
                $description
            );

            DB::commit();

            return back()->with('success', 'Reviewer assignment updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating assignment: ' . $e->getMessage());
            return back()->with('error', 'Failed to update assignment: ' . $e->getMessage());
        }
    }

    public function AssignR(ResearchProject $project)
    {
        $projects = ResearchProject::orderBy('title')->get();

        $reviewers = User::where('role', 'reviewer')
            ->orderBy('firstname')
            ->orderBy('lastname')
            ->get();

        return view(
            'project.staff.ReviewAssignment.create',
            compact('projects', 'reviewers', 'project')
        );
    }

    public function AssignedReviewer(Request $request, ResearchProject $project)
    {
        $request->validate([
            'reviewer_id' => 'required|exists:users,id',
            'deadline' => 'required|date|after:today',
        ]);

        try {
            DB::beginTransaction();

            $alreadyAssigned = ReviewAssignment::where('project_id', $project->id)
                ->where('reviewer_id', $request->reviewer_id)
                ->exists();

            if ($alreadyAssigned) {
                return back()->with('error', 'This reviewer is already assigned to this project.');
            }

            $assignment = ReviewAssignment::create([
                'project_id' => $project->id,
                'reviewer_id' => $request->reviewer_id,
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
                'deadline' => $request->deadline,
                'status' => 'in_progress',
            ]);

            $reviewerName = User::find($request->reviewer_id)->fullName ?? "User ID: {$request->reviewer_id}";

            ActivityLogger::log(
                'created',
                'ReviewAssignment',
                $assignment->id,
                "Assigned reviewer: {$reviewerName} to project: {$project->title}"
            );

            if ($project->is_staff_approved !== 'approved') {
                $project->update(['is_staff_approved' => 'approved']);

                ActivityLogger::log(
                    'updated',
                    'ResearchProject',
                    $project->id,
                    'Auto-approved project after reviewer assignment'
                );
            }

            DB::commit();

            return redirect()
                ->route('review_assignments.index')
                ->with('success', 'Reviewer assigned successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error assigning reviewer: ' . $e->getMessage());
            return back()->with('error', 'Failed to assign reviewer: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Find the assignment with relationships
            $assignment = ReviewAssignment::with(['project', 'reviewer'])->findOrFail($id);
            
            // Check if assignment can be deleted
            if ($assignment->status === 'completed') {
                return redirect()->back()
                    ->with('error', 'Cannot delete a completed review assignment.');
            }
            
            // Store info for logging
            $projectTitle = $assignment->project->title ?? 'Unknown Project';
            $reviewerName = $assignment->reviewer ? 
                $assignment->reviewer->firstname . ' ' . $assignment->reviewer->lastname : 
                'Unknown Reviewer';
            
            // Log the deletion
            ActivityLogger::log(
                'deleted',
                'ReviewAssignment',
                $assignment->id,
                "Deleted assignment for project: {$projectTitle}, Reviewer: {$reviewerName}"
            );
            
            // Delete the assignment
            $assignment->delete();
            
            DB::commit();

            return redirect()->route('review_assignments.index')
                ->with('success', 'Review assignment deleted successfully.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('review_assignments.index')
                ->with('error', 'Review assignment not found.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            \Log::error('Database error deleting assignment: ' . $e->getMessage());
            return redirect()->route('review_assignments.index')
                ->with('error', 'Database error occurred while deleting the assignment.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting review assignment: ' . $e->getMessage());
            
            return redirect()->route('review_assignments.index')
                ->with('error', 'An error occurred while deleting the assignment.');
        }
    }
}